<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    private function isAdmin(): bool
    {
        return Auth::user()->isAdmin();
    }

    /*
    |--------------------------------------------------------------------------
    | index — split admins / residents with stats
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | edit — render the user edit form
    |--------------------------------------------------------------------------
    */
    public function edit(User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'Cannot edit your own account here.');
        return view('users.edit', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | update — name and email only (role changes go through changeRole)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        try {
            $user->update($validated);

            ActivityLog::record('user_updated', $user,
                "User \"{$user->name}\" profile updated by " . Auth::user()->name
            );

            Log::info('User profile updated', ['user_id' => $user->id, 'admin_id' => Auth::id()]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
            }
            return redirect()->route('users.show', $user->id)->with('success', 'Profile updated successfully.');

        } catch (\Throwable $e) {
            Log::error('UserManagementController@update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to update profile.'], 500);
            return back()->with('error', 'Failed to update profile.')->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | show — profile details + report history
    |--------------------------------------------------------------------------
    */
    public function show(User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'Cannot view your own account here.');

        $user->loadCount('reports');
        $reports     = $user->reports()->with('category')->latest()->paginate(10);
        $reportStats = [
            'total'       => $user->reports()->count(),
            'pending'     => $user->reports()->where('status', 'Pending')->count(),
            'in_progress' => $user->reports()->where('status', 'In Progress')->count(),
            'resolved'    => $user->reports()->where('status', 'Resolved')->count(),
        ];
        $lastActive = $user->reports()->latest()->value('created_at');
        $roles      = Role::orderBy('name')->get();

        return view('users.show', compact('user', 'reports', 'reportStats', 'lastActive', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | suspend
    |--------------------------------------------------------------------------
    */
    public function suspend(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'You cannot suspend yourself.');

        // Prevent suspending other admins
        if ($user->isAdmin()) {
            if ($request->ajax()) return response()->json(['error' => 'Admin accounts cannot be suspended.'], 403);
            return back()->with('error', 'Admin accounts cannot be suspended.');
        }

        $request->validate(['suspension_reason' => 'nullable|string|max:500']);

        try {
            $user->update([
                'is_suspended'      => true,
                'suspended_at'      => now(),
                'suspension_reason' => $request->input('suspension_reason'),
            ]);

            ActivityLog::record(
                'user_suspended',
                $user,
                "User \"{$user->name}\" suspended by " . Auth::user()->name .
                ($request->suspension_reason ? ". Reason: {$request->suspension_reason}" : ''),
            );

            NotificationService::userSuspended($user, $request->input('suspension_reason'));
            Log::info('User suspended', ['user_id' => $user->id, 'admin_id' => Auth::id()]);

            if ($request->ajax()) return response()->json(['success' => true, 'message' => "{$user->name} has been suspended."]);
            return back()->with('success', "{$user->name} has been suspended.");

        } catch (\Throwable $e) {
            Log::error('UserManagementController@suspend failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to suspend user.'], 500);
            return back()->with('error', 'Failed to suspend user.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | activate — lift a suspension
    |--------------------------------------------------------------------------
    */
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

            NotificationService::userActivated($user);
            Log::info('User activated', ['user_id' => $user->id, 'admin_id' => Auth::id()]);

            if ($request->ajax()) return response()->json(['success' => true, 'message' => "{$user->name}'s account has been reactivated."]);
            return back()->with('success', "{$user->name}'s account has been reactivated.");

        } catch (\Throwable $e) {
            Log::error('UserManagementController@activate failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to reactivate user.'], 500);
            return back()->with('error', 'Failed to reactivate user.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | changeRole — deliberate appointment/demotion with required reason
    |
    | Guards:
    |  - Cannot change your own role
    |  - Cannot demote the last admin (system must always have at least one)
    |  - Reason is required so there is always an audit trail
    |--------------------------------------------------------------------------
    */
    public function changeRole(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        if ($user->id === Auth::id()) {
            if ($request->ajax()) return response()->json(['error' => 'You cannot change your own role.'], 403);
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'reason'  => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $newRole = Role::findOrFail($validated['role_id']);
        $oldRole = $user->role_relation;
        $oldRoleName = $oldRole->name ?? 'Resident';
        $newRoleName = $newRole->name;

        if ($user->role_id === (int) $validated['role_id']) {
            if ($request->ajax()) return response()->json(['error' => "{$user->name} is already a {$newRoleName}."]);
            return back()->with('error', "{$user->name} is already a {$newRoleName}.");
        }

        $adminRole = Role::where('name', 'Admin')->first();
        $isBeingDemoted = $user->role_id === $adminRole?->id && $newRole->id !== $adminRole?->id;

        if ($isBeingDemoted) {
            $adminCount = User::where('role_id', $adminRole->id)->count();
            if ($adminCount <= 1) {
                $msg = 'Cannot demote the last admin. Appoint another admin first.';
                if ($request->ajax()) return response()->json(['error' => $msg], 403);
                return back()->with('error', $msg);
            }
        }

        $user->update(['role_id' => $newRole->id]);

        $adminName = Auth::user()->name;
        $action = $isBeingDemoted ? 'demoted to' : 'appointed as';
        $description = "User \"{$user->name}\" {$action} {$newRoleName} by {$adminName}. Reason: {$validated['reason']}";

        try { ActivityLog::record('user_role_changed', $user, $description, ['old_role' => $oldRoleName, 'new_role' => $newRoleName, 'reason' => $validated['reason'], 'changed_by' => $adminName]); } catch (\Throwable $e) { /* silent */ }

        try { NotificationService::roleChanged($user, $newRoleName); } catch (\Throwable $e) { /* silent */ }

        $message = strtolower($newRoleName) === 'admin'
            ? "{$user->name} has been appointed as Admin."
            : "{$user->name} has been moved back to Resident.";

        if ($request->ajax()) return response()->json(['success' => true, 'message' => $message]);
        return back()->with('success', $message);
    }
}