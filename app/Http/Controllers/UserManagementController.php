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
        $role = Auth::user()->role;
        return strtolower(is_object($role) ? $role->name : ($role ?? 'resident')) === 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper — resolve a readable role name from a user
    | Works whether role is a loaded relationship object or a raw string
    |--------------------------------------------------------------------------
    */
    private function resolveRoleName(User $user): string
    {
        // Reload the role relationship fresh from the DB to avoid stale cache
        $user->load('role');
        $role = $user->role;
        return is_object($role) ? $role->name : ($role ?? 'Unknown');
    }

    /*
    |--------------------------------------------------------------------------
    | index
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
    | show
    |--------------------------------------------------------------------------
    */
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
        $roles = Role::all();

        return view('users.show', compact('user', 'reports', 'reportStats', 'lastActive', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | edit
    |--------------------------------------------------------------------------
    */
    public function edit(User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'Cannot edit your own account here.');

        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | update — captures old role BEFORE update, new role AFTER, logs correctly
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');
        if ($user->id === Auth::id()) abort(403, 'Cannot edit your own account here.');

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Capture old values BEFORE the update
            $oldName     = $user->name;
            $oldRoleName = $this->resolveRoleName($user);
            $oldEmail    = $user->email;

            // Perform update
            $user->update($request->only(['name', 'email', 'role_id']));

            // Resolve new role name AFTER update (fresh load)
            $newRoleName = $this->resolveRoleName($user);

            // Build a specific description
            $changes = [];
            if ($oldName  !== $user->name)  $changes[] = "name from \"{$oldName}\" to \"{$user->name}\"";
            if ($oldEmail !== $user->email)  $changes[] = "email from \"{$oldEmail}\" to \"{$user->email}\"";
            if ($oldRoleName !== $newRoleName) $changes[] = "role from \"{$oldRoleName}\" to \"{$newRoleName}\"";

            $description = count($changes)
                ? "User \"{$user->name}\" updated by " . Auth::user()->name . ': ' . implode(', ', $changes)
                : "User \"{$user->name}\" profile saved by " . Auth::user()->name . ' (no changes)';

            ActivityLog::record(
                'user_updated',
                $user,
                $description,
                [
                    'old_name'  => $oldName,
                    'new_name'  => $user->name,
                    'old_email' => $oldEmail,
                    'new_email' => $user->email,
                    'old_role'  => $oldRoleName,
                    'new_role'  => $newRoleName,
                ]
            );

            Log::info('User updated', [
                'target_user_id' => $user->id,
                'admin_id'       => Auth::id(),
                'old_role'       => $oldRoleName,
                'new_role'       => $newRoleName,
            ]);

            return redirect()->route('users.show', $user->id)
                             ->with('success', 'User updated successfully.');

        } catch (\Throwable $e) {
            Log::error('UserManagementController@update failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to update user.')->withInput();
        }
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

            // Send notification
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
    | activate
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

            ActivityLog::record(
                'user_activated',
                $user,
                "User \"{$user->name}\" account reactivated by " . Auth::user()->name,
            );

            // Send notification
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
    | changeRole
    |--------------------------------------------------------------------------
    */
    public function changeRole(Request $request, User $user)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        try {
            $oldRole = $user->role->name ?? 'Resident';
            $newRole = Role::findOrFail($validated['role_id'])->name;

            $user->update(['role_id' => $validated['role_id']]);

            ActivityLog::record(
                'user_role_changed',
                $user,
                "User \"{$user->name}\" role changed from {$oldRole} to {$newRole} by " . Auth::user()->name,
            );

            // Send notification
            NotificationService::roleChanged($user, $newRole);

            Log::info('User role changed', ['user_id' => $user->id, 'old_role' => $oldRole, 'new_role' => $newRole, 'admin_id' => Auth::id()]);

            if ($request->ajax()) return response()->json(['success' => true, 'message' => "{$user->name}'s role changed to {$newRole}."]);
            return back()->with('success', "{$user->name}'s role changed to {$newRole}.");

        } catch (\Throwable $e) {
            Log::error('UserManagementController@changeRole failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to change role.'], 500);
            return back()->with('error', 'Failed to change role.');
        }
    }
}