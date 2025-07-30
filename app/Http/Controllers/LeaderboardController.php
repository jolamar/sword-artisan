<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index()
    {
        $scores = Score::with('user')
            ->orderByDesc('score')
            ->take(10)
            ->get();

        return Inertia::render('Leaderboard', [
            'scores' => $scores
        ]);
    }
}
