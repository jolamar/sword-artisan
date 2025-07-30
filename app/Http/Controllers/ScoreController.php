<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;

public function store(Request $request)
{
    $request->validate([
        'score' => 'required|integer|min:0',
    ]);

    $score = Score::create([
        'user_id' => $request->user()->id,
        'score' => $request->input('score'),
    ]);

    return response()->json(['success' => true, 'score' => $score]);
}

