<?php

namespace App\Http\Controllers;

use App\Models\Report as MonthlyReport;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.billing');
    }

    public function show($key)
    {
        if ($key == 'request') {
            return view('report.request');
        } elseif ($key == 'sms') {
            return view('report.sms_blast');
        } elseif ($key == 'billing') {
            return view('report.billing');
        } else {
            return view('report.log');
        }
    }

    /** Download the AI-generated monthly report PDF. */
    public function download(int $id)
    {
        $report = MonthlyReport::where('user_id', auth()->id())->findOrFail($id);

        if (! $report->isReady() || ! $report->file_path) {
            abort(404, 'Report not ready or file missing.');
        }

        if (! Storage::disk('local')->exists($report->file_path)) {
            abort(404, 'PDF file not found.');
        }

        return Storage::disk('local')->download(
            $report->file_path,
            'laporan-' . $report->month . '-' . $report->year . '.pdf'
        );
    }

    /** View the AI-generated monthly report PDF inline in browser. */
    public function view(int $id)
    {
        $report = MonthlyReport::where('user_id', auth()->id())->findOrFail($id);

        if (! $report->isReady() || ! $report->file_path) {
            abort(404, 'Report not ready or file missing.');
        }

        if (! Storage::disk('local')->exists($report->file_path)) {
            abort(404, 'PDF file not found.');
        }

        return response()->file(
            Storage::disk('local')->path($report->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }
}
