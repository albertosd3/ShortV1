<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Aggregate stats per day/week/year using SQLite strftime
        $perDay = Click::selectRaw("strftime('%Y-%m-%d', created_at) as day, count(*) as total")
            ->groupBy('day')->orderBy('day', 'desc')->limit(30)->get();

        $perWeek = Click::selectRaw("strftime('%Y-W%W', created_at) as week, count(*) as total")
            ->groupBy('week')->orderBy('week', 'desc')->limit(26)->get();

        $perYear = Click::selectRaw("strftime('%Y', created_at) as year, count(*) as total")
            ->groupBy('year')->orderBy('year', 'desc')->limit(5)->get();

        $byDevice = Click::selectRaw('device, count(*) as total')->groupBy('device')->orderByDesc('total')->limit(10)->get();
        $byBrowser = Click::selectRaw('browser, count(*) as total')->groupBy('browser')->orderByDesc('total')->limit(10)->get();
        $byCountry = Click::selectRaw('country, count(*) as total')->groupBy('country')->orderByDesc('total')->limit(20)->get();

        return view('dashboard.index', compact('perDay', 'perWeek', 'perYear', 'byDevice', 'byBrowser', 'byCountry'));
    }
}
