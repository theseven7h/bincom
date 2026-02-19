<?php

namespace App\Http\Controllers;

use App\Models\State;

class LgaResultController extends Controller
{
    public function index()
    {
        $states = State::orderBy('state_name')->get();
        $defaultStateId = 25;

        return view('lga-results.index', compact('states', 'defaultStateId'));
    }
}
