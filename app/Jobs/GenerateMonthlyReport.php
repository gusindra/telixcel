<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $reportId) {}

    public function handle(ReportService $service): void
    {
        $report = Report::find($this->reportId);
        if (! $report) return;

        $user = User::find($report->user_id);
        if (! $user) {
            $report->update(['status' => 'failed', 'error_message' => 'User not found.']);
            return;
        }

        try {
            $report->update(['status' => 'processing']);
            $data = $service->generate($user, $report->month, $report->year);

            $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
            $monthName = $months[$report->month] ?? $report->month;
            $label = $data['type'] === 'admin'
                ? "Laporan Admin — {$monthName} {$report->year}"
                : "Laporan {$data['user_name']} — {$monthName} {$report->year}";

            $html = view('reports.monthly', compact('data', 'label'))->render();
            $pdf = Pdf::loadHTML($html)->setPaper('a4');

            $filename = sprintf('reports/report-%s-%04d-%02d-%d.pdf', $data['type'], $report->year, $report->month, $report->user_id);
            Storage::disk('local')->put($filename, $pdf->output());

            $report->update(['status' => 'ready', 'file_path' => $filename, 'metadata' => $data['summary']]);
        } catch (\Throwable $e) {
            $report->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
