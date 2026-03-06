<?php

namespace Database\Seeders;

use App\Models\BotTemplate;
use Illuminate\Database\Seeder;

class BotTemplateSeeder extends Seeder
{
    /**
     * Seed the application's database with bot templates.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'link_hub',
                'name' => 'Link Hub Bot',
                'description' => 'Simple menu with buttons that open your links.',
                'defaults_json' => [
                    'welcome_message' => 'Welcome. Choose an option below.',
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
                            ['type' => 'buttons', 'items' => [
                                ['text' => '{{button_text}}', 'goto' => 'open_link'],
                            ]],
                        ],
                    ],
                    [
                        'id' => 'open_link',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{products_link}}'],
                        ],
                    ],
                ],
                'sort_order' => 1,
            ],
            [
                'key' => 'lead_collection',
                'name' => 'Lead Collection Bot',
                'description' => 'Collect a name and phone number from new users.',
                'defaults_json' => [
                    'welcome_message' => 'Welcome. Let us collect your details quickly.',
                    'button_text' => 'Start',
                    'settings_json' => [
                        'lead_prompt_name' => 'What is your full name?',
                        'lead_prompt_phone' => 'Please share your phone number.',
                    ],
                ],
                'flow_json' => [
                    [
                        'id' => 'start',
                        'trigger' => '/start',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{welcome_message}}'],
                            ['type' => 'send_message', 'text' => '{{settings_json.lead_prompt_name}}'],
                        ],
                    ],
                ],
                'sort_order' => 2,
            ],
            [
                'key' => 'store',
                'name' => 'Store Bot',
                'description' => 'Welcome users and route them to products and support.',
                'defaults_json' => [
                    'welcome_message' => 'Welcome to our store.',
                    'support_username' => '@support',
                    'products_link' => 'https://example.com/products',
                    'button_text' => 'View Products',
                    'settings_json' => [
                        'support_button_text' => 'Support',
                    ],
                ],
                'flow_json' => [
                    [
                        'id' => 'start',
                        'trigger' => '/start',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{welcome_message}}'],
                            ['type' => 'buttons', 'items' => [
                                ['text' => '{{button_text}}', 'goto' => 'products'],
                                ['text' => '{{settings_json.support_button_text}}', 'goto' => 'support'],
                            ]],
                        ],
                    ],
                    [
                        'id' => 'products',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{products_link}}'],
                        ],
                    ],
                    [
                        'id' => 'support',
                        'actions' => [
                            ['type' => 'send_message', 'text' => 'Contact {{support_username}}'],
                        ],
                    ],
                ],
                'sort_order' => 3,
            ],
            [
                'key' => 'faq',
                'name' => 'FAQ Bot',
                'description' => 'Answer common questions with quick reply buttons.',
                'defaults_json' => [
                    'welcome_message' => 'Ask a question or use the buttons below.',
                    'button_text' => 'Shipping Info',
                    'settings_json' => [
                        'faq_answer' => 'Shipping usually takes 2-5 business days.',
                    ],
                ],
                'flow_json' => [
                    [
                        'id' => 'start',
                        'trigger' => '/start',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{welcome_message}}'],
                            ['type' => 'buttons', 'items' => [
                                ['text' => '{{button_text}}', 'goto' => 'faq_answer'],
                            ]],
                        ],
                    ],
                    [
                        'id' => 'faq_answer',
                        'actions' => [
                            ['type' => 'send_message', 'text' => '{{settings_json.faq_answer}}'],
                        ],
                    ],
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($templates as $template) {
            BotTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
