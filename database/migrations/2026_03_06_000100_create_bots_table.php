<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('telegram_bot_username')->nullable();
            $table->text('bot_token');
            $table->string('bot_token_hash', 64)->unique();
            $table->string('template_key');
            $table->text('welcome_message')->nullable();
            $table->string('support_username')->nullable();
            $table->string('products_link')->nullable();
            $table->string('button_text')->nullable();
            $table->json('settings_json')->nullable();
            $table->json('flow_json')->nullable();
            $table->string('status')->default('draft');
            $table->string('webhook_secret')->nullable();
            $table->string('webhook_status')->default('not_configured');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
