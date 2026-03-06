<?php

use App\Models\Bot;
use App\Models\BotTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    BotTemplate::query()->create([
        'key' => 'store',
        'name' => 'Store Bot',
        'description' => 'Store template',
        'defaults_json' => [
            'welcome_message' => 'Welcome to our store.',
            'support_username' => '@support',
            'products_link' => 'https://example.com/products',
            'button_text' => 'View Products',
            'settings_json' => ['support_button_text' => 'Support'],
        ],
        'flow_json' => [
            [
                'id' => 'start',
                'trigger' => '/start',
                'actions' => [
                    ['type' => 'send_message', 'text' => '{{welcome_message}}'],
                ],
            ],
        ],
        'is_active' => true,
        'sort_order' => 1,
    ]);

    BotTemplate::query()->create([
        'key' => 'link_hub',
        'name' => 'Link Hub Bot',
        'description' => 'Link hub template',
        'defaults_json' => [
            'welcome_message' => 'Welcome.',
            'products_link' => 'https://example.com',
            'button_text' => 'Open Link',
            'settings_json' => [],
        ],
        'flow_json' => [
            [
                'id' => 'start',
                'trigger' => '/start',
                'actions' => [
                    ['type' => 'send_message', 'text' => '{{welcome_message}}'],
                ],
            ],
        ],
        'is_active' => true,
        'sort_order' => 2,
    ]);
});

it('allows authenticated users to create a bot from template', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/bots', [
            'name' => 'Store Assistant',
            'bot_token' => '123:abc',
            'telegram_bot_username' => 'store_assistant_bot',
            'template_key' => 'store',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('bots', [
        'user_id' => $user->id,
        'name' => 'Store Assistant',
        'template_key' => 'store',
        'bot_token_hash' => hash('sha256', '123:abc'),
    ]);
});

it('blocks users from editing bots they do not own', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $bot = Bot::query()->create([
        'user_id' => $owner->id,
        'name' => 'Private Bot',
        'bot_token' => 'token-1',
        'bot_token_hash' => hash('sha256', 'token-1'),
        'template_key' => 'link_hub',
        'flow_json' => [],
    ]);

    $this->actingAs($intruder)
        ->patch("/bots/{$bot->id}", [
            'name' => 'Hacked',
        ])
        ->assertForbidden();
});

it('handles webhook updates and stores subscribers', function () {
    Http::fake();

    $owner = User::factory()->create();

    $bot = Bot::query()->create([
        'user_id' => $owner->id,
        'name' => 'Webhook Bot',
        'bot_token' => 'token-2',
        'bot_token_hash' => hash('sha256', 'token-2'),
        'template_key' => 'link_hub',
        'webhook_secret' => 'secret-123',
        'flow_json' => [
            [
                'id' => 'start',
                'trigger' => '/start',
                'actions' => [
                    ['type' => 'send_message', 'text' => 'Welcome'],
                ],
            ],
        ],
    ]);

    $response = $this->withHeaders([
        'X-Telegram-Bot-Api-Secret-Token' => 'secret-123',
    ])->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start',
            'chat' => ['id' => 998877],
            'from' => [
                'id' => 112233,
                'first_name' => 'Miki',
                'last_name' => 'Dev',
                'username' => 'mikidev',
            ],
        ],
    ]);

    $response->assertOk()->assertJson(['ok' => true]);

    $this->assertDatabaseHas('bot_subscribers', [
        'bot_id' => $bot->id,
        'telegram_user_id' => 112233,
        'username' => 'mikidev',
    ]);
});
