<?php

namespace App\Http\Controllers;

use App\Services\AdminReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    // Full reports page with filters
    public function index(Request $request, AdminReportService $reports)
    {
        [$from, $to] = $reports->resolveDateRange(
            $request->input('from'),
            $request->input('to')
        );

        $report = $reports->build($from, $to);
        $maxTrend = $report['dailyTrend']->max('total') ?: 1;

        return view('admin.reports.index', array_merge($report, [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'maxTrend' => $maxTrend,
        ]));
    }

    // Download listings summary as CSV
    public function exportCsv(Request $request, AdminReportService $reports): StreamedResponse
    {
        [$from, $to] = $reports->resolveDateRange(
            $request->input('from'),
            $request->input('to')
        );

        $report = $reports->build($from, $to);
        $filename = 'food-portal-report-'.$from->format('Y-m-d').'_'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($report, $from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Community Food Portal — report']);
            fputcsv($out, ['From', $from->toDateString(), 'To', $to->toDateString()]);
            fputcsv($out, []);

            fputcsv($out, ['Metric', 'Value']);
            foreach ($report['summary'] as $key => $value) {
                fputcsv($out, [$key, $value]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Status', 'Count']);
            foreach ($report['byStatus'] as $row) {
                fputcsv($out, [$row->status, $row->total]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Category', 'Count']);
            foreach ($report['byCategory'] as $row) {
                fputcsv($out, [$row->category, $row->total]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Day', 'New listings']);
            foreach ($report['dailyTrend'] as $row) {
                fputcsv($out, [$row->day, $row->total]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Pickup area', 'Listings']);
            foreach ($report['byArea'] as $row) {
                fputcsv($out, [$row->pickup_address, $row->total]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
