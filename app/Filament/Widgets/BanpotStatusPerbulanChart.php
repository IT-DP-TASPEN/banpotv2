<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\BanpotMaster;
use App\Models\BanpotMasterCompleted;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BanpotStatusPerbulanChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Status Banpot Per Bulan';

    protected static ?int $sort = 2;

    public function getColumnSpan(): int|string|array
    {
        return 2; // Lebar 2 kolom
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $canViewAll = $user->isSuperAdmin() || $user->isAdmin();

        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $statusList = [
            '1' => ['label' => 'Requested', 'model' => BanpotMaster::class],
            '2' => ['label' => 'Checked Mitra', 'model' => BanpotMaster::class],
            '3' => ['label' => 'Approved Mitra', 'model' => BanpotMasterCompleted::class],
            '4' => ['label' => 'Rejected Mitra', 'model' => BanpotMasterCompleted::class],
            '5' => ['label' => 'Canceled Mitra', 'model' => BanpotMasterCompleted::class],
            '6' => ['label' => 'Checked Bank', 'model' => BanpotMasterCompleted::class],
            '7' => ['label' => 'Approved Bank', 'model' => BanpotMasterCompleted::class],
            '8' => ['label' => 'Rejected Bank', 'model' => BanpotMasterCompleted::class],
            '9' => ['label' => 'On Process', 'model' => BanpotMasterCompleted::class],
            '10' => ['label' => 'Success', 'model' => BanpotMasterCompleted::class],
            '11' => ['label' => 'Failed', 'model' => BanpotMasterCompleted::class],
        ];

        $datasets = [];

        foreach ($statusList as $status => $config) {
            $counts = $months->map(function ($month) use ($status, $user, $canViewAll, $config) {
                $model = $config['model'];

                return $model::where('status_banpot', $status)
                    ->when(!$canViewAll, function ($q) use ($user) {
                        $q->whereHas('user', function ($query) use ($user) {
                            $query->where('mitra_id', $user->mitra_id);
                        });
                    })
                    ->whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2))
                    ->count();
            })->toArray();

            $datasets[] = [
                'label' => $config['label'],
                'data' => $counts,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Bisa diganti 'line' jika mau garis
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isStaffMitraPusat() || $user->isApprovalMitraPusat();
    }

    //test
}
