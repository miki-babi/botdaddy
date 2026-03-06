<?php

namespace App\Support;

use App\Models\Bot;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramFlowEngine
{
    private const MAX_ACTIONS = 30;

    private const MAX_GOTO_DEPTH = 12;

    /**
     * @var array<string, string>
     */
    private const ACTION_HANDLERS = [
        'send_message' => 'handleSendMessage',
        'buttons' => 'handleButtons',
        'goto' => 'handleGoto',
    ];

    /**
     * @param array<string, int|string> $event
     */
    public function handle(Bot $bot, array $event): void
    {
        try {
            $flow = (array) $bot->flow_json;
            if ($flow === []) {
                return;
            }

            $triggerMap = $this->buildTriggerMap($flow);
            $blockMap = $this->buildBlockMap($flow);

            $trigger = (string) ($event['value'] ?? '');
            $entryBlockId = $this->resolveEntryBlockId($trigger, $triggerMap, $blockMap);
            if (! $entryBlockId) {
                Log::warning('No matching entry block for trigger', [
                    'bot_id' => $bot->id,
                    'trigger' => $trigger,
                ]);

                return;
            }

            $context = [
                'bot_id' => $bot->id,
                'bot_token' => (string) $bot->bot_token,
                'chat_id' => (int) ($event['chat_id'] ?? 0),
                'user_id' => (int) ($event['user_id'] ?? 0),
                'event' => (string) ($event['event'] ?? ''),
                'trigger' => $trigger,
                'flow' => $flow,
                'trigger_map' => $triggerMap,
                'block_map' => $blockMap,
                'action_count' => 0,
            ];

            $this->runBlock($entryBlockId, $context, 0);
        } catch (\Throwable $exception) {
            Log::error('Telegram flow engine failed', [
                'bot_id' => $bot->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $flow
     * @return array<string, string>
     */
    private function buildTriggerMap(array $flow): array
    {
        $triggerMap = [];

        foreach ($flow as $block) {
            $trigger = Arr::get($block, 'trigger');
            $id = Arr::get($block, 'id');

            if (! is_string($trigger) || $trigger === '' || ! is_string($id) || $id === '') {
                continue;
            }

            $triggerMap[$trigger] = $id;
        }

        return $triggerMap;
    }

    /**
     * @param array<int, array<string, mixed>> $flow
     * @return array<string, array<string, mixed>>
     */
    private function buildBlockMap(array $flow): array
    {
        $blockMap = [];

        foreach ($flow as $block) {
            $id = Arr::get($block, 'id');

            if (! is_string($id) || $id === '') {
                continue;
            }

            $blockMap[$id] = $block;
        }

        return $blockMap;
    }

    /**
     * @param array<string, string> $triggerMap
     * @param array<string, array<string, mixed>> $blockMap
     */
    private function resolveEntryBlockId(string $trigger, array $triggerMap, array $blockMap): ?string
    {
        if ($trigger !== '' && isset($triggerMap[$trigger])) {
            return $triggerMap[$trigger];
        }

        if (isset($blockMap['default'])) {
            return 'default';
        }

        if (isset($blockMap['start'])) {
            return 'start';
        }

        return array_key_first($blockMap);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function runBlock(string $blockId, array &$context, int $depth): void
    {
        if ($depth > self::MAX_GOTO_DEPTH) {
            Log::warning('Flow execution stopped: goto depth limit reached', [
                'bot_id' => $context['bot_id'] ?? null,
                'block_id' => $blockId,
                'depth' => $depth,
            ]);

            return;
        }

        $blockMap = (array) ($context['block_map'] ?? []);
        $block = $blockMap[$blockId] ?? null;

        if (! is_array($block)) {
            Log::warning('Flow execution stopped: block not found', [
                'bot_id' => $context['bot_id'] ?? null,
                'block_id' => $blockId,
            ]);

            return;
        }

        $actions = Arr::get($block, 'actions', []);
        if (! is_array($actions)) {
            return;
        }

        foreach ($actions as $action) {
            if (! is_array($action)) {
                continue;
            }

            $context['action_count'] = ((int) ($context['action_count'] ?? 0)) + 1;

            if ((int) $context['action_count'] > self::MAX_ACTIONS) {
                Log::warning('Flow execution stopped: action limit reached', [
                    'bot_id' => $context['bot_id'] ?? null,
                    'block_id' => $blockId,
                    'action_count' => $context['action_count'],
                ]);

                return;
            }

            $gotoTarget = $this->dispatchAction($action, $context);
            if (is_string($gotoTarget) && $gotoTarget !== '') {
                $this->runBlock($gotoTarget, $context, $depth + 1);
                return;
            }
        }
    }

    /**
     * @param array<string, mixed> $action
     * @param array<string, mixed> $context
     */
    private function dispatchAction(array $action, array &$context): ?string
    {
        $type = Arr::get($action, 'type');

        if (! is_string($type) || $type === '') {
            return null;
        }

        $handler = self::ACTION_HANDLERS[$type] ?? null;
        if (! $handler || ! method_exists($this, $handler)) {
            Log::warning('Unknown flow action type', [
                'bot_id' => $context['bot_id'] ?? null,
                'type' => $type,
            ]);

            return null;
        }

        try {
            return $this->{$handler}($action, $context);
        } catch (\Throwable $exception) {
            Log::error('Flow action failed', [
                'bot_id' => $context['bot_id'] ?? null,
                'type' => $type,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param array<string, mixed> $action
     * @param array<string, mixed> $context
     */
    private function handleSendMessage(array $action, array &$context): ?string
    {
        $text = (string) Arr::get($action, 'text', '');

        if ($text === '') {
            return null;
        }

        $this->sendMessage(
            (string) ($context['bot_token'] ?? ''),
            (int) ($context['chat_id'] ?? 0),
            $text
        );

        return null;
    }

    /**
     * @param array<string, mixed> $action
     * @param array<string, mixed> $context
     */
    private function handleButtons(array $action, array &$context): ?string
    {
        $items = Arr::get($action, 'items', []);
        if (! is_array($items) || $items === []) {
            return null;
        }

        $messageText = (string) Arr::get($action, 'text', 'Choose an option:');

        $this->sendButtons(
            (string) ($context['bot_token'] ?? ''),
            (int) ($context['chat_id'] ?? 0),
            $messageText,
            $items
        );

        return null;
    }

    /**
     * @param array<string, mixed> $action
     * @param array<string, mixed> $context
     */
    private function handleGoto(array $action, array &$context): ?string
    {
        $target = (string) Arr::get($action, 'target', '');

        return $target !== '' ? $target : null;
    }

    private function sendMessage(string $botToken, int $chatId, string $text): void
    {
        if ($botToken === '' || $chatId <= 0 || $text === '') {
            return;
        }

        $response = Http::timeout(8)
            ->post($this->apiUrl($botToken, 'sendMessage'), [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

        Log::info('Telegram sendMessage attempted', [
            'chat_id' => $chatId,
            'text_preview' => mb_substr($text, 0, 80),
            'telegram_status' => $response->status(),
            'telegram_ok' => $response->successful(),
            'telegram_body' => $response->json(),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function sendButtons(string $botToken, int $chatId, string $text, array $items): void
    {
        if ($botToken === '' || $chatId <= 0 || $items === []) {
            return;
        }

        $inlineKeyboard = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $buttonText = (string) Arr::get($item, 'text', '');
            $goto = (string) Arr::get($item, 'goto', '');
            $url = (string) Arr::get($item, 'url', '');

            if ($buttonText === '') {
                continue;
            }

            if ($url !== '') {
                $inlineKeyboard[] = [[
                    'text' => $buttonText,
                    'url' => $url,
                ]];

                continue;
            }

            if ($goto !== '') {
                $inlineKeyboard[] = [[
                    'text' => $buttonText,
                    'callback_data' => $goto,
                ]];
            }
        }

        if ($inlineKeyboard === []) {
            return;
        }

        $response = Http::timeout(8)
            ->post($this->apiUrl($botToken, 'sendMessage'), [
                'chat_id' => $chatId,
                'text' => $text !== '' ? $text : 'Choose an option:',
                'reply_markup' => [
                    'inline_keyboard' => $inlineKeyboard,
                ],
            ]);

        Log::info('Telegram buttons message attempted', [
            'chat_id' => $chatId,
            'button_count' => count($inlineKeyboard),
            'telegram_status' => $response->status(),
            'telegram_ok' => $response->successful(),
            'telegram_body' => $response->json(),
        ]);
    }

    private function apiUrl(string $botToken, string $method): string
    {
        return sprintf('https://api.telegram.org/bot%s/%s', $botToken, $method);
    }
}
