<?php

namespace App\Support;

use Illuminate\Support\Arr;

class TelegramUpdateNormalizer
{
    /**
     * Normalize raw Telegram webhook payload into a small event object.
     *
     * @param array<string, mixed> $update
     * @return array<string, int|string>|null
     */
    public function normalize(array $update): ?array
    {
        $callbackData = Arr::get($update, 'callback_query.data');
        if (is_string($callbackData) && $callbackData !== '') {
            $chatId = Arr::get($update, 'callback_query.message.chat.id');
            $userId = Arr::get($update, 'callback_query.from.id');

            if (! is_numeric($chatId) || ! is_numeric($userId)) {
                return null;
            }

            return [
                'event' => 'button',
                'value' => trim($callbackData),
                'chat_id' => (int) $chatId,
                'user_id' => (int) $userId,
            ];
        }

        $text = Arr::get($update, 'message.text');
        $chatId = Arr::get($update, 'message.chat.id');
        $userId = Arr::get($update, 'message.from.id');

        if (! is_numeric($chatId) || ! is_numeric($userId) || ! is_string($text) || trim($text) === '') {
            return null;
        }

        $trimmed = trim($text);
        $eventType = str_starts_with($trimmed, '/') ? 'command' : 'text';
        $value = $eventType === 'command' ? $this->normalizeCommand($trimmed) : $trimmed;

        return [
            'event' => $eventType,
            'value' => $value,
            'chat_id' => (int) $chatId,
            'user_id' => (int) $userId,
        ];
    }

    private function normalizeCommand(string $command): string
    {
        $firstToken = explode(' ', $command)[0] ?? $command;

        if (str_contains($firstToken, '@')) {
            return explode('@', $firstToken)[0] ?: $firstToken;
        }

        return $firstToken;
    }
}
