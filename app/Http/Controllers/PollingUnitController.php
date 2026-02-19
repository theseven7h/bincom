<?php

namespace App\Http\Controllers;

use App\Models\State;

class PollingUnitController extends Controller
{
    public function index()
    {
        // Preload all states for the first dropdown
        $states = State::orderBy('state_name')->get();

        // Delta state (id=25) is highlighted in the test, so we pass it as default
        $defaultStateId = 25;

        return view('polling-unit.index', compact('states', 'defaultStateId'));
    }
}
