<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Support\BotTemplates;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BotDashboardController extends Controller
{
    /**
     * Display the bot dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $selectedBotId = (int) $request->integer('bot');

        $bots = Bot::query()
            ->where('user_id', $user->id)
            ->withCount('subscribers')
            ->withCount([
                'subscribers as joined_today_count' => fn ($query) => $query->whereDate('joined_at', now()->toDateString()),
            ])
            ->latest()
            ->get();

        $selectedBot = $bots->firstWhere('id', $selectedBotId) ?? $bots->first();

        $selectedSubscribers = $selectedBot
            ? $selectedBot->subscribers()->latest('joined_at')->limit(20)->get()
            : collect();

        return Inertia::render('Dashboard', [
            'templates' => BotTemplates::all(),
            'bots' => $bots,
            'selectedBot' => $selectedBot,
            'selectedSubscribers' => $selectedSubscribers,
            'status' => $request->session()->get('status'),
        ]);
    }
}
