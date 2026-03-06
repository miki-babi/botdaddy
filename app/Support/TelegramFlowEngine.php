<?php

namespace App\Support;

use App\Models\Bot;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class TelegramFlowEngine
{
    /**
     * @param array<string, mixed> $update
     */
    public function handle(Bot $bot, array $update): void
    {
        $chatId = Arr::get($update, 'message.chat.id')
            ?? Arr::get($update, 'callback_query.message.chat.id');

        if (! is_numeric($chatId)) {
            return;
        }

        $trigger = $this->detectTrigger($update);
        if (! $trigger) {
            return;
        }

        $block = $this->findBlock((array) $bot->flow_json, $trigger);
        if (! $block) {
            return;
        }

        foreach ((array) Arr::get($block, 'actions', []) as $action) {
            $this->runAction($bot, (int) $chatId, (array) $action);
        }
    }

    /**
     * @param array<string, mixed> $update
     */
    public function detectTrigger(array $update): ?string
    {
        $callbackData = Arr::get($update, 'callback_query.data');
        if (is_string($callbackData) && $callbackData !== '') {
            return $callbackData;
        }

        $text = Arr::get($update, 'message.text');
        if (! is_string($text) || $text === '') {
            return null;
        }

        return str_starts_with($text, '/') ? trim($text) : '/start';
    }

    /**
     * @param array<int, array<string, mixed>> $flow
     * @return array<string, mixed>|null
     */
    private function findBlock(array $flow, string $trigger): ?array
    {
        foreach ($flow as $block) {
            $blockTrigger = Arr::get($block, 'trigger');
            $blockId = Arr::get($block, 'id');

            if ($blockTrigger === $trigger || $blockId === $trigger) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $action
     */
    private function runAction(Bot $bot, int $chatId, array $action): void
    {
        $type = Arr::get($action, 'type');

        if ($type === 'send_message') {
            $this->sendMessage($bot, $chatId, (string) Arr::get($action, 'text', ''));

            return;
        }

        if ($type === 'buttons') {
            $items = (array) Arr::get($action, 'items', []);
            $this->sendButtons($bot, $chatId, $items);
        }
    }

    private function sendMessage(Bot $bot, int $chatId, string $text): void
    {
        if ($text === '') {
            return;
        }

        Http::timeout(8)
            ->post($this->apiUrl($bot, 'sendMessage'), [
                'chat_id' => $chatId,
                'text' => $text,
            ]);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function sendButtons(Bot $bot, int $chatId, array $items): void
    {
        if ($items === []) {
            return;
        }

        $inlineKeyboard = [];

        foreach ($items as $item) {
            $text = (string) Arr::get($item, 'text', '');
            $goto = (string) Arr::get($item, 'goto', '');

            if ($text === '' || $goto === '') {
                continue;
            }

            $inlineKeyboard[] = [[
                'text' => $text,
                'callback_data' => $goto,
            ]];
        }

        if ($inlineKeyboard === []) {
            return;
        }

        Http::timeout(8)
            ->post($this->apiUrl($bot, 'sendMessage'), [
                'chat_id' => $chatId,
                'text' => 'Choose an option:',
                'reply_markup' => [
                    'inline_keyboard' => $inlineKeyboard,
                ],
            ]);
    }

    private function apiUrl(Bot $bot, string $method): string
    {
        return sprintf('https://api.telegram.org/bot%s/%s', $bot->bot_token, $method);
    }
}
