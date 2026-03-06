<?php

namespace App\Support;

use App\Models\BotTemplate;
use Illuminate\Support\Arr;

class BotTemplates
{
    /**
     * @return array<int, array<string, string>>
     */
    public static function all(): array
    {
        return BotTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['key', 'name', 'description'])
            ->map(fn (BotTemplate $template) => [
                'key' => $template->key,
                'name' => $template->name,
                'description' => $template->description,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(string $templateKey): array
    {
        $template = self::findActiveTemplate($templateKey);

        return (array) $template->defaults_json;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, array<string, mixed>>
     */
    public static function flow(string $templateKey, array $payload): array
    {
        $template = self::findActiveTemplate($templateKey);

        $flow = (array) $template->flow_json;

        return self::replaceTokens($flow, $payload);
    }

    public static function exists(string $templateKey): bool
    {
        return BotTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->exists();
    }

    private static function findActiveTemplate(string $templateKey): BotTemplate
    {
        return BotTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $payload
     * @return mixed
     */
    private static function replaceTokens(mixed $value, array $payload): mixed
    {
        if (is_array($value)) {
            $resolved = [];
            foreach ($value as $key => $item) {
                $resolved[$key] = self::replaceTokens($item, $payload);
            }

            return $resolved;
        }

        if (! is_string($value)) {
            return $value;
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.-]+)\s*\}\}/', function (array $matches) use ($payload): string {
            $path = $matches[1] ?? '';
            $replacement = Arr::get($payload, $path);

            if (is_scalar($replacement) || $replacement === null) {
                return (string) ($replacement ?? '');
            }

            return '';
        }, $value) ?? $value;
    }
}
