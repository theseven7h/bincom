<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\Ward;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResult;
use App\Models\Party;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    /**
     * Get LGAs for a given state.
     */
    public function getLgas($stateId)
    {
        $lgas = Lga::where('state_id', $stateId)
            ->orderBy('lga_name')
            ->get(['uniqueid', 'lga_id', 'lga_name']);

        return response()->json($lgas);
    }

    /**
     * Get Wards for a given LGA (by lga_id).
     */
    public function getWards($lgaId)
    {
        $wards = Ward::where('lga_id', $lgaId)
            ->orderBy('ward_name')
            ->get(['uniqueid', 'ward_id', 'ward_name']);

        return response()->json($wards);
    }

    /**
     * Get Polling Units for a given Ward (by ward_id).
     */
    public function getPollingUnits($wardId)
    {
        $units = PollingUnit::where('ward_id', $wardId)
            ->orderBy('polling_unit_name')
            ->get(['uniqueid', 'polling_unit_name', 'polling_unit_number']);

        return response()->json($units);
    }

    /**
     * Get results for a specific polling unit.
     */
    public function getPuResults($pollingUnitId)
    {
        $pollingUnit = PollingUnit::find($pollingUnitId);

        if (!$pollingUnit) {
            return response()->json(['error' => 'Polling unit not found'], 404);
        }

        $results = AnnouncedPuResult::where('polling_unit_uniqueid', $pollingUnitId)
            ->orderBy('party_abbreviation')
            ->get(['party_abbreviation', 'party_score']);

        $total = $results->sum('party_score');

        return response()->json([
            'polling_unit' => $pollingUnit->polling_unit_name,
            'results' => $results,
            'total' => $total,
        ]);
    }

    /**
     * Get summed results for all polling units in an LGA.
     * We compute the sum ourselves from announced_pu_results (NOT from announced_lga_results).
     */
    public function getLgaResults($lgaId)
    {
        $lga = Lga::where('lga_id', $lgaId)->first();

        if (!$lga) {
            return response()->json(['error' => 'LGA not found'], 404);
        }

        // Get all polling unit uniqueids under this LGA
        $pollingUnitIds = PollingUnit::where('lga_id', $lgaId)->pluck('uniqueid');

        if ($pollingUnitIds->isEmpty()) {
            return response()->json([
                'lga_name' => $lga->lga_name,
                'results' => [],
                'total' => 0,
            ]);
        }

        // Sum scores per party across all polling units in this LGA
        $results = AnnouncedPuResult::whereIn('polling_unit_uniqueid', $pollingUnitIds)
            ->select('party_abbreviation', DB::raw('SUM(party_score) as total_score'))
            ->groupBy('party_abbreviation')
            ->orderBy('party_abbreviation')
            ->get();

        $total = $results->sum('total_score');

        return response()->json([
            'lga_name' => $lga->lga_name,
            'results' => $results,
            'total' => $total,
        ]);
    }

    /**
     * Get all parties.
     */
    public function getParties()
    {
        $parties = Party::orderBy('partyid')->get(['id', 'partyid', 'partyname']);
        return response()->json($parties);
    }
}
