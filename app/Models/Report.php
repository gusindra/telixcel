<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'month', 'year', 'status',
        'file_path', 'metadata', 'error_message',
    ];

    protected $casts = [
        'month' => 'integer', 'year' => 'integer', 'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function absolutePath(): ?string
    {
        if (! $this->file_path) return null;
        return storage_path('app/' . $this->file_path);
    }

    public function label(): string
    {
        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $monthName = $months[$this->month] ?? $this->month;
        $typeLabel = $this->type === 'admin' ? 'Admin' : 'Personal';
        return "Laporan {$typeLabel} — {$monthName} {$this->year}";
    }
}
