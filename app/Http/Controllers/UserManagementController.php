<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    private function isAdmin(): bool
    {
        $role = Auth::user()->role;
        return strtolower(is_object($role) ? $role->name : ($role ?? 'resident')) === 'admin';
    }

    public function index(Request $request)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        try {
            $roles        = Role::orderBy('name')->get();
            $adminRole    = $roles->first(fn($r) => strtolower($r->name) === 'admin');
            $residentRole = $roles->first(fn($r) => strtolower($r->name) === 'resident');

            $applySearch = function ($query) use ($request) {
                if ($search = $request->input('search')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name',  'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
                return $query;
            };

            $adminQuery = User::withCount('reports')
                ->where('id', '!=', Auth::id())
                ->when($adminRole, fn($q) => $q->where('role_id', $adminRole->id))
                ->latest();

            $residentQuery = User::withCount('reports')
                ->where('id', '!=', Auth::id())
                ->when($residentRole, fn($q) => $q->where('role_id', $residentRole->id))
                ->latest();

            $admins    = $applySearch($adminQuery)->paginate(10, ['*'], 'admin_page')->withQueryString();
            $residents = $applySearch($residentQuery)->paginate(10, ['*'], 'resident_page')->withQueryString();

            $residentStats = [
                'total'     => User::where('id', '!=', Auth::id())->when($residentRole, fn($q) => $q->where('role_id', $residentRole->id))->count(),
                'active'    => User::where('id', '!=', Auth::id())->when($residentRole, fn($q) => $q->where('role_id', $residentRole->id))->where('is_suspended', false)->count(),
                'suspended' => User::where('id', '!=', Auth::id())->when($residentRole, fn($q) => $q->where('role_id', $residentRole->id))->where('is_suspended', true)->count(),
            ];

            return view('users.index', compact('admins', 'residents', 'roles', 'residentStats'));

        } catch (\Throwable $e) {
            Log::error('UserManagementController@index failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to load users.');
        }
    }

    public function show(User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'Cannot view your own account here.');

        $user->loadCount('reports');

        $reports = $user->reports()->with('category')->latest()->paginate(10);

        $reportStats = [
            'total'       => $user->reports()->count(),
            'pending'     => $user->reports()->where('status', 'Pending')->count(),
            'in_progress' => $user->reports()->where('status', 'In Progress')->count(),
            'resolved'    => $user->reports()->where('status', 'Resolved')->count(),
        ];

        $lastActive = $user->reports()->latest()->value('created_at');

        return view('users.show', compact('user', 'reports', 'reportStats', 'lastActive'));
    }

    public function suspend(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'You cannot suspend yourself.');

        $request->validate(['suspension_reason' => 'nullable|string|max:500']);

        try {
            $user->update([
                'is_suspended'      => true,
                'suspended_at'      => now(),
                'suspension_reason' => $request->input('suspension_reason'),
            ]);

            ActivityLog::record('user_suspended', $user,
                "User \"{$user->name}\" suspended by " . Auth::user()->name .
                ($request->suspension_reason ? ". Reason: {$request->suspension_reason}" : ''),
            );

            if ($request->ajax()) return response()->json(['success' => true, 'message' => "{$user->name} has been suspended."]);
            return back()->with('success', "{$user->name} has been suspended.");

        } catch (\Throwable $e) {
            Log::error('UserManagementController@suspend failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to suspend user.'], 500);
            return back()->with('error', 'Failed to suspend user.');
        }
    }

    public function activate(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        try {
            $user->update([
                'is_suspended'      => false,
                'suspended_at'      => null,
                'suspension_reason' => null,
            ]);

            ActivityLog::record('user_activated', $user,
                "User \"{$user->name}\" account reactivated by " . Auth::user()->name,
            );

            if ($request->ajax()) return response()->json(['success' => true, 'message' => "{$user->name}'s account has been reactivated."]);
            return back()->with('success', "{$user->name}'s account has been reactivated.");

        } catch (\Throwable $e) {
            Log::error('UserManagementController@activate failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to reactivate user.'], 500);
            return back()->with('error', 'Failed to reactivate user.');
        }
    }
}