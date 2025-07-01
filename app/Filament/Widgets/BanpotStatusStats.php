<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\BanpotMaster;
use App\Models\BanpotMasterCompleted;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BanpotStatusStats extends BaseWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isApprovalMitraPusat() || $user->isStaffMitraPusat();
    }

    protected function getCards(): array
    {
        $user = Auth::user();

        // Superadmin dan Admin bisa lihat semua data
        $canViewAll = $user->isSuperAdmin() || $user->isAdmin();

        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $requestedData = $months->map(function ($month) use ($user, $canViewAll) {
            return BanpotMaster::where('status_banpot', '1')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('mitra_id', $user->mitra_id);
                    });
                })
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
        });

        $approvedData = $months->map(function ($month) use ($user, $canViewAll) {
            return BanpotMaster::where('status_banpot', '1')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('mitra_id', $user->mitra_id);
                    });
                })
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
        });

        $finishedData = $months->map(function ($month) use ($user, $canViewAll) {
            return BanpotMasterCompleted::where('status_banpot', '10')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('mitra_id', $user->mitra_id);
                    });
                })
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
        });

        $failedData = $months->map(function ($month) use ($user, $canViewAll) {
            return BanpotMasterCompleted::where('status_banpot', '11')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('mitra_id', $user->mitra_id);
                    });
                })
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
        });

        return [
            Card::make(
                'Banpot Requested',
                BanpotMaster::where('status_banpot', '1')
                    ->when(!$canViewAll, function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('mitra_id', $user->mitra_id);
                        });
                    })
                    ->count()
            )
                ->color('primary')
                ->chart($requestedData->toArray()),

            Card::make(
                'Banpot Need Approve',
                BanpotMaster::where('status_banpot', '1')
                    ->when(!$canViewAll, function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('mitra_id', $user->mitra_id);
                        });
                    })
                    ->count()
            )
                ->color('info')
                ->chart($approvedData->toArray()),

            Card::make(
                'Banpot Success',
                BanpotMasterCompleted::where('status_banpot', '10')
                    ->when(!$canViewAll, function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('mitra_id', $user->mitra_id);
                        });
                    })
                    ->count()
            )
                ->color('success')
                ->chart($finishedData->toArray()),

            Card::make(
                'Banpot Failed',
                BanpotMasterCompleted::where('status_banpot', '11')
                    ->when(!$canViewAll, function ($q) use ($user) {
                        $q->whereHas('user', function ($q2) use ($user) {
                            $q2->where('mitra_id', $user->mitra_id);
                        });
                    })
                    ->count()
            )
                ->color('danger')
                ->chart($failedData->toArray()),
        ];
    }
}
