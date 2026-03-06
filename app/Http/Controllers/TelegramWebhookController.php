<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\BotSubscriber;
use App\Support\TelegramFlowEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle all Telegram updates for all bots.
     */
    public function __invoke(Request $request, TelegramFlowEngine $engine): JsonResponse
    {
        $bot = $this->resolveBot($request);

        if (! $bot) {
            Log::warning('Telegram webhook bot not resolved', [
                'has_secret_header' => $request->hasHeader('X-Telegram-Bot-Api-Secret-Token'),
                'token_query' => $request->query('token'),
            ]);

            return response()->json(['ok' => false, 'message' => 'Bot not found'], 404);
        }

        $payload = $request->all();

        $this->trackSubscriber($bot, $payload);
        $engine->handle($bot, $payload);

        Log::info('Telegram webhook processed', [
            'bot_id' => $bot->id,
            'update_keys' => array_keys($payload),
        ]);

        return response()->json(['ok' => true]);
    }

    private function resolveBot(Request $request): ?Bot
    {
        $secret = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if (is_string($secret) && $secret !== '') {
            return Bot::query()->where('webhook_secret', $secret)->first();
        }

        $tokenHash = (string) $request->query('token');
        if ($tokenHash !== '') {
            return Bot::query()->where('bot_token_hash', $tokenHash)->first();
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function trackSubscriber(Bot $bot, array $payload): void
    {
        $user = Arr::get($payload, 'message.from') ?? Arr::get($payload, 'callback_query.from');
        if (! is_array($user)) {
            return;
        }

        $telegramUserId = Arr::get($user, 'id');
        if (! is_numeric($telegramUserId)) {
            return;
        }

        BotSubscriber::query()->updateOrCreate(
            [
                'bot_id' => $bot->id,
                'telegram_user_id' => (int) $telegramUserId,
            ],
            [
                'name' => trim(sprintf('%s %s', (string) Arr::get($user, 'first_name', ''), (string) Arr::get($user, 'last_name', ''))),
                'username' => Arr::get($user, 'username'),
                'joined_at' => now(),
            ]
        );
    }
}
