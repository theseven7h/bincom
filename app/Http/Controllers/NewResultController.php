<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Party;
use App\Models\AnnouncedPuResult;
use Illuminate\Http\Request;

class NewResultController extends Controller
{
    public function index()
    {
        $states = State::orderBy('state_name')->get();
        $parties = Party::orderBy('partyid')->get();
        $defaultStateId = 25;

        return view('new-result.index', compact('states', 'parties', 'defaultStateId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'polling_unit_id'   => 'required|integer|exists:polling_unit,uniqueid',
            'scores'            => 'required|array|min:1',
            'scores.*'          => 'required|integer|min:0',
        ]);

        $pollingUnitId = $request->input('polling_unit_id');
        $scores = $request->input('scores'); // ['PDP' => 500, 'ACN' => 300, ...]
        $ip = $request->ip();
        $now = now();

        // Delete any existing results for this polling unit before saving (prevent duplicates)
        AnnouncedPuResult::where('polling_unit_uniqueid', $pollingUnitId)->delete();

        $rows = [];
        foreach ($scores as $partyAbbr => $score) {
            $rows[] = [
                'polling_unit_uniqueid' => $pollingUnitId,
                'party_abbreviation'    => $partyAbbr,
                'party_score'           => (int) $score,
                'entered_by_user'       => 'Admin',
                'date_entered'          => $now,
                'user_ip_address'       => $ip,
            ];
        }

        AnnouncedPuResult::insert($rows);

        return redirect()->route('new-result.index')
            ->with('success', 'Results saved successfully for the selected polling unit.');
    }
}
