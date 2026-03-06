<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Broadcast;
use App\Support\BotTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BotController extends Controller
{
    /**
     * Store a newly created bot.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'bot_token' => ['required', 'string', 'max:255'],
            'telegram_bot_username' => ['nullable', 'string', 'max:120'],
            'template_key' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) || ! BotTemplates::exists($value)) {
                    $fail("The {$attribute} is invalid.");
                }
            }],
        ]);

        $defaults = BotTemplates::defaults($validated['template_key']);

        $bot = Bot::query()->create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'telegram_bot_username' => $validated['telegram_bot_username'] ?? null,
            'bot_token' => $validated['bot_token'],
            'bot_token_hash' => hash('sha256', $validated['bot_token']),
            'template_key' => $validated['template_key'],
            'welcome_message' => $defaults['welcome_message'] ?? null,
            'support_username' => $defaults['support_username'] ?? null,
            'products_link' => $defaults['products_link'] ?? null,
            'button_text' => $defaults['button_text'] ?? null,
            'settings_json' => $defaults['settings_json'] ?? [],
            'flow_json' => BotTemplates::flow($validated['template_key'], $defaults),
        ]);

        Log::info('Bot created', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
            'template_key' => $bot->template_key,
        ]);

        return to_route('dashboard', ['bot' => $bot->id])->with('status', 'Bot created. Customize and publish when ready.');
    }

    /**
     * Update bot content.
     */
    public function update(Request $request, Bot $bot): RedirectResponse
    {
        $this->assertOwnership($request, $bot);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'telegram_bot_username' => ['nullable', 'string', 'max:120'],
            'welcome_message' => ['nullable', 'string', 'max:2000'],
            'support_username' => ['nullable', 'string', 'max:120'],
            'products_link' => ['nullable', 'url', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'faq_answer' => ['nullable', 'string', 'max:2000'],
            'lead_prompt_name' => ['nullable', 'string', 'max:255'],
            'lead_prompt_phone' => ['nullable', 'string', 'max:255'],
            'support_button_text' => ['nullable', 'string', 'max:120'],
        ]);

        $existingSettings = (array) $bot->settings_json;
        $settings = $existingSettings;
        foreach (['faq_answer', 'lead_prompt_name', 'lead_prompt_phone', 'support_button_text'] as $settingKey) {
            if (array_key_exists($settingKey, $validated) && $validated[$settingKey]) {
                $settings[$settingKey] = $validated[$settingKey];
            }
        }

        $bot->fill([
            'name' => $validated['name'],
            'telegram_bot_username' => $validated['telegram_bot_username'] ?? null,
            'welcome_message' => $validated['welcome_message'] ?? null,
            'support_username' => $validated['support_username'] ?? null,
            'products_link' => $validated['products_link'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'settings_json' => $settings,
        ]);

        $flowOverrideEnabled = (bool) ($existingSettings['flow_override'] ?? false);
        if (! $flowOverrideEnabled) {
            $bot->flow_json = BotTemplates::flow($bot->template_key, [
                'welcome_message' => $bot->welcome_message,
                'support_username' => $bot->support_username,
                'products_link' => $bot->products_link,
                'button_text' => $bot->button_text,
                'settings_json' => $settings,
            ]);
        }

        $bot->save();

        Log::info('Bot updated', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
        ]);

        return to_route('dashboard', ['bot' => $bot->id])->with('status', 'Bot content updated.');
    }

    /**
     * Update bot flow JSON from dashboard editor.
     */
    public function updateFlow(Request $request, Bot $bot): RedirectResponse
    {
        $this->assertOwnership($request, $bot);

        $validated = $request->validate([
            'flow_json' => ['required', 'string'],
        ]);

        $decoded = json_decode($validated['flow_json'], true);
        if (! is_array($decoded)) {
            Log::warning('Bot flow update failed: invalid JSON', [
                'bot_id' => $bot->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->withErrors([
                'flow_json' => 'Flow JSON must be a valid JSON array of blocks.',
            ]);
        }

        $settings = (array) $bot->settings_json;
        $settings['flow_override'] = true;

        $bot->flow_json = $decoded;
        $bot->settings_json = $settings;
        $bot->save();

        Log::info('Bot flow updated manually', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
            'flow_blocks' => count($decoded),
        ]);

        return to_route('dashboard', ['bot' => $bot->id])->with('status', 'Flow updated.');
    }

    /**
     * Publish a bot and attempt to configure webhook.
     */
    public function publish(Request $request, Bot $bot): RedirectResponse
    {
        $this->assertOwnership($request, $bot);

        if (! $bot->webhook_secret) {
            $bot->webhook_secret = Str::random(40);
        }

        $bot->status = 'active';
        $bot->published_at = now();

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '') {
            $webhookUrl = "{$appUrl}/telegram/webhook?token={$bot->bot_token_hash}";

            try {
                $response = Http::timeout(10)->post("https://api.telegram.org/bot{$bot->bot_token}/setWebhook", [
                    'url' => $webhookUrl,
                    'secret_token' => $bot->webhook_secret,
                ]);

                $bot->webhook_status = $response->successful() ? 'configured' : 'failed';

                Log::info('Bot publish webhook response', [
                    'bot_id' => $bot->id,
                    'user_id' => $request->user()->id,
                    'webhook_status' => $bot->webhook_status,
                    'telegram_response_status' => $response->status(),
                    'telegram_response_body' => $response->json(),
                ]);
            } catch (\Throwable) {
                $bot->webhook_status = 'failed';
                Log::error('Bot publish webhook request failed', [
                    'bot_id' => $bot->id,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        $bot->save();

        Log::info('Bot published', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
            'webhook_status' => $bot->webhook_status,
        ]);

        return to_route('dashboard', ['bot' => $bot->id])->with('status', 'Bot published. Webhook status updated in settings.');
    }

    /**
     * Create a new broadcast record.
     */
    public function broadcast(Request $request, Bot $bot): RedirectResponse
    {
        $this->assertOwnership($request, $bot);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'audience' => ['required', 'string', 'in:all,last_24_hours,last_7_days'],
        ]);

        $query = $bot->subscribers();

        if ($validated['audience'] === 'last_24_hours') {
            $query->where('joined_at', '>=', now()->subDay());
        }

        if ($validated['audience'] === 'last_7_days') {
            $query->where('joined_at', '>=', now()->subDays(7));
        }

        $recipientCount = $query->count();

        Broadcast::query()->create([
            'bot_id' => $bot->id,
            'message' => $validated['message'],
            'image_url' => $validated['image_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'audience' => $validated['audience'],
            'sent_to' => $recipientCount,
            'sent_at' => now(),
        ]);

        Log::info('Broadcast created', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
            'audience' => $validated['audience'],
            'recipient_count' => $recipientCount,
        ]);

        return to_route('dashboard', ['bot' => $bot->id])->with('status', "Broadcast queued for {$recipientCount} users.");
    }

    /**
     * Delete a bot.
     */
    public function destroy(Request $request, Bot $bot): RedirectResponse
    {
        $this->assertOwnership($request, $bot);

        Log::warning('Bot deleted', [
            'bot_id' => $bot->id,
            'user_id' => $request->user()->id,
        ]);

        $bot->delete();

        return to_route('dashboard')->with('status', 'Bot deleted.');
    }

    private function assertOwnership(Request $request, Bot $bot): void
    {
        $botUserId = (int) $bot->user_id;
        $requestUserId = (int) $request->user()->id;

        if ($botUserId !== $requestUserId) {
            Log::warning('Bot ownership check failed', [
                'bot_id' => $bot->id,
                'bot_user_id' => $botUserId,
                'request_user_id' => $requestUserId,
                'path' => $request->path(),
                'method' => $request->method(),
            ]);
        }

        abort_unless($botUserId === $requestUserId, 403);
    }
}
