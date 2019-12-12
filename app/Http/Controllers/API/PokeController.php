<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Notifications\JustPoking;
use App\Http\Controllers\Controller;

class PokeController extends Controller
{
    public function poke(Request $request, User $user)
    {
        $message = $request->user()->name . " just poked you.";
        $user->notify(new JustPoking($message));

        return response()->json(['message' => 'You just poked ' . $user->name]);
    }
}
