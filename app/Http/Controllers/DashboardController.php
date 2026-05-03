<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function isAdmin(): bool
    {
        $role = Auth::user()->role;
        return strtolower(is_object($role) ? $role->name : ($role ?? 'resident')) === 'admin';
    }

    public function index()
    {
        if ($this->isAdmin()) {
            // Admin: system-wide metrics
            $totalUsers       = User::count();
            $totalReports     = Report::count();
            $reportsToday     = Report::whereDate('created_at', today())->count();
            $attachmentsCount = Attachment::count();
            $pendingCount     = Report::where('status', 'Pending')->count();
            $inProgressCount  = Report::where('status', 'In Progress')->count();
            $resolvedCount    = Report::where('status', 'Resolved')->count();

            $recentReports = Report::with(['user', 'category'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'totalUsers', 'totalReports', 'reportsToday', 'attachmentsCount',
                'pendingCount', 'inProgressCount', 'resolvedCount',
                'recentReports'
            ));
        }

        // Resident: scoped to their own reports
        $userId           = Auth::id();
        $myTotal          = Report::where('user_id', $userId)->count();
        $myPending        = Report::where('user_id', $userId)->where('status', 'Pending')->count();
        $myInProgress     = Report::where('user_id', $userId)->where('status', 'In Progress')->count();
        $myResolved       = Report::where('user_id', $userId)->where('status', 'Resolved')->count();
        $myToday          = Report::where('user_id', $userId)->whereDate('created_at', today())->count();

        $recentReports = Report::with(['category'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'myTotal', 'myPending', 'myInProgress', 'myResolved', 'myToday',
            'recentReports'
        ));
    }
}
