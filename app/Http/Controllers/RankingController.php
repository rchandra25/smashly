<?php

namespace App\Http\Controllers;

use App\Services\EloService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __construct(
        private EloService $eloService
    ) {}

    public function index(Request $request): View
    {
        $period = $request->query('period');
        $validPeriods = ['week', 'month', null];

        if (!in_array($period, $validPeriods)) {
            $period = null;
        }

        $rankings = $this->eloService->getRankings($period);

        return view('rankings.index', compact('rankings', 'period'));
    }
}
