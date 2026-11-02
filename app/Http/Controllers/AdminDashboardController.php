<?php

namespace App\Http\Controllers;

use App\Services\AdminReportService;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    // Admin home — last 30 days snapshot
    public function index(AdminReportService $reports)
    {
        $to = Carbon::today();
        $from = $to->copy()->subDays(29);
        $report = $reports->build($from, $to);

        return view('admin.dashboard', [
            'summary' => $report['summary'],
            'byStatus' => $report['byStatus'],
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }
}
