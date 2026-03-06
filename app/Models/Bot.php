<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends Model
{
    /** @use HasFactory<\Database\Factories\BotFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'telegram_bot_username',
        'bot_token',
        'bot_token_hash',
        'template_key',
        'welcome_message',
        'support_username',
        'products_link',
        'button_text',
        'settings_json',
        'flow_json',
        'status',
        'webhook_secret',
        'webhook_status',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bot_token' => 'encrypted',
            'settings_json' => 'array',
            'flow_json' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * The user that owns the bot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bot subscribers.
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(BotSubscriber::class);
    }

    /**
     * Broadcasts sent by this bot.
     */
    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }
}
