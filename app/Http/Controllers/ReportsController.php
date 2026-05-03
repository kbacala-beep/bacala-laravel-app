<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Attachment;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    private function roleName(): string
    {
        $role = Auth::user()->role;
        return strtolower(is_object($role) ? $role->name : ($role ?? 'resident'));
    }

    private function isAdmin(): bool
    {
        return $this->roleName() === 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | index — with search, status filter, and category filter
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        try {
            $categories = Category::orderBy('name')->get();

            // Build a reusable filter closure
            $applyFilters = function ($query) use ($request) {
                if ($search = $request->input('search')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('resident_name', 'like', "%{$search}%")
                          ->orWhere('subject',      'like', "%{$search}%")
                          ->orWhere('description',  'like', "%{$search}%");
                    });
                }
                if ($status = $request->input('status')) {
                    $query->where('status', $status);
                }
                if ($categoryId = $request->input('category_id')) {
                    $query->where('category_id', $categoryId);
                }
                return $query;
            };

            if ($this->isAdmin()) {
                // Admin: single flat list of all reports
                $query   = Report::with(['user', 'attachments', 'category'])->latest();
                $reports = $applyFilters($query)->paginate(10)->withQueryString();

                return view('reports.index', compact('reports', 'categories'));
            }

            // Resident: split into own reports (pinned) and others
            $userId = Auth::id();

            $myQuery    = Report::with(['user', 'attachments', 'category'])
                                ->where('user_id', $userId)
                                ->latest();
            $otherQuery = Report::with(['user', 'attachments', 'category'])
                                ->where('user_id', '!=', $userId)
                                ->latest();

            $myReports    = $applyFilters($myQuery)->paginate(10, ['*'], 'my_page')->withQueryString();
            $otherReports = $applyFilters($otherQuery)->paginate(10, ['*'], 'other_page')->withQueryString();

            return view('reports.index', compact('myReports', 'otherReports', 'categories'));

        } catch (\Throwable $e) {
            Log::error('ReportsController@index failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Unable to load reports. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        if ($this->isAdmin()) abort(403, 'Admins cannot submit reports.');

        $categories = Category::orderBy('name')->get();
        return view('reports.create', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | store
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        if ($this->isAdmin()) abort(403, 'Admins cannot submit reports.');

        $request->validate([
            'resident_name' => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'subject'       => 'required|string|max:255',
            'description'   => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            $report = Report::create([
                'user_id'       => Auth::id(),
                'category_id'   => $request->category_id,
                'resident_name' => $request->resident_name,
                'subject'       => $request->subject,
                'description'   => $request->description,
                'status'        => 'Pending',
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    Attachment::create([
                        'report_id' => $report->id,
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            ActivityLog::record('report_created', $report,
                "Report #{$report->id} \"{$report->subject}\" submitted by " . Auth::user()->name
            );

            Log::info('Report created', ['report_id' => $report->id, 'user_id' => Auth::id()]);

            return redirect()->route('reports.index')
                             ->with('success', 'Report submitted successfully!');

        } catch (\Throwable $e) {
            Log::error('ReportsController@store failed', [
                'user_id' => Auth::id(), 'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to submit report. Please try again.')->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | show
    |--------------------------------------------------------------------------
    */
    public function show(Report $report)
    {
        try {
            $report->load(['user', 'attachments', 'category']);
            return view('reports.show', compact('report'));
        } catch (\Throwable $e) {
            Log::error('ReportsController@show failed', ['report_id' => $report->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Unable to load this report.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | edit
    |--------------------------------------------------------------------------
    */
    public function edit(Report $report)
    {
        if (!$this->isAdmin() && $report->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own reports.');
        }

        $categories = Category::orderBy('name')->get();
        return view('reports.edit', compact('report', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | update
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Report $report)
    {
        if (!$this->isAdmin() && $report->user_id !== Auth::id()) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthorized.'], 403);
            abort(403, 'You can only edit your own reports.');
        }

        try {
            if ($this->isAdmin()) {
                $request->validate([
                    'resident_name' => 'required|string|max:255',
                    'status'        => 'required|in:Pending,In Progress,Resolved',
                ]);

                $oldStatus = $report->status;
                $report->update($request->only(['resident_name', 'status']));

                ActivityLog::record('status_updated', $report,
                    "Report #{$report->id} status changed from \"{$oldStatus}\" to \"{$report->status}\" by " . Auth::user()->name,
                    ['old_status' => $oldStatus, 'new_status' => $report->status]
                );

                Log::info('Report status updated', [
                    'report_id' => $report->id, 'admin_id' => Auth::id(),
                    'old_status' => $oldStatus, 'new_status' => $report->status,
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status updated successfully.',
                        'status'  => $report->status,
                    ]);
                }

            } else {
                $request->validate([
                    'resident_name' => 'required|string|max:255',
                    'category_id'   => 'nullable|exists:categories,id',
                    'subject'       => 'required|string|max:255',
                    'description'   => 'required|string',
                    'status'        => 'required|in:Pending,In Progress,Resolved',
                ]);

                $report->update($request->only([
                    'resident_name', 'category_id', 'subject', 'description', 'status'
                ]));

                ActivityLog::record('report_updated', $report,
                    "Report #{$report->id} edited by " . Auth::user()->name
                );
            }

            return redirect()->route('reports.index')->with('success', 'Report updated successfully!');

        } catch (\Throwable $e) {
            Log::error('ReportsController@update failed', ['report_id' => $report->id, 'error' => $e->getMessage()]);

            if ($request->ajax()) return response()->json(['error' => 'Failed to update. Please try again.'], 500);
            return back()->with('error', 'Failed to update report. Please try again.')->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | destroy
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Report $report)
    {
        if (!$this->isAdmin() && $report->user_id !== Auth::id()) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthorized.'], 403);
            abort(403, 'You can only delete your own reports.');
        }

        try {
            $reportId = $report->id; $reportSubject = $report->subject;
            $report->delete();

            ActivityLog::record('report_archived', $report,
                "Report #{$reportId} \"{$reportSubject}\" archived by " . Auth::user()->name
            );

            Log::info('Report archived', ['report_id' => $reportId, 'user_id' => Auth::id()]);

            if ($request->ajax()) return response()->json(['success' => true, 'message' => 'Report archived successfully.']);
            return redirect()->route('reports.index')->with('success', 'Report archived successfully.');

        } catch (\Throwable $e) {
            Log::error('ReportsController@destroy failed', ['report_id' => $report->id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to archive report.'], 500);
            return back()->with('error', 'Failed to archive report. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | archive
    |--------------------------------------------------------------------------
    */
    public function archive()
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        try {
            $reports = Report::onlyTrashed()->with(['user', 'category'])->latest('deleted_at')->paginate(10);
            return view('reports.archive', compact('reports'));
        } catch (\Throwable $e) {
            Log::error('ReportsController@archive failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to load archived reports.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | restore
    |--------------------------------------------------------------------------
    */
    public function restore(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthorized.'], 403);
            abort(403, 'Admins only.');
        }

        try {
            $report = Report::onlyTrashed()->findOrFail($id);
            $report->restore();

            ActivityLog::record('report_restored', $report,
                "Report #{$report->id} \"{$report->subject}\" restored by " . Auth::user()->name
            );

            if ($request->ajax()) return response()->json(['success' => true, 'message' => 'Report restored successfully.']);
            return redirect()->route('reports.archive')->with('success', 'Report restored successfully.');

        } catch (\Throwable $e) {
            Log::error('ReportsController@restore failed', ['report_id' => $id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to restore report.'], 500);
            return back()->with('error', 'Failed to restore report. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | forceDelete
    |--------------------------------------------------------------------------
    */
    public function forceDelete(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthorized.'], 403);
            abort(403, 'Admins only.');
        }

        try {
            $report = Report::onlyTrashed()->findOrFail($id);
            $reportId = $report->id; $reportSubject = $report->subject;

            foreach ($report->attachments as $attachment) {
                \Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $report->forceDelete();

            ActivityLog::create([
                'user_id' => Auth::id(), 'action' => 'report_permanently_deleted',
                'entity_type' => 'Report', 'entity_id' => $reportId,
                'description' => "Report #{$reportId} \"{$reportSubject}\" permanently deleted by " . Auth::user()->name,
                'ip_address' => $request->ip(),
            ]);

            Log::warning('Report permanently deleted', ['report_id' => $reportId, 'admin_id' => Auth::id()]);

            if ($request->ajax()) return response()->json(['success' => true, 'message' => 'Report permanently deleted.']);
            return redirect()->route('reports.archive')->with('success', 'Report permanently deleted.');

        } catch (\Throwable $e) {
            Log::error('ReportsController@forceDelete failed', ['report_id' => $id, 'error' => $e->getMessage()]);
            if ($request->ajax()) return response()->json(['error' => 'Failed to delete report.'], 500);
            return back()->with('error', 'Failed to delete report. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | activityLog — admin only
    |--------------------------------------------------------------------------
    */
    public function activityLog(Request $request)
    {
        if (!$this->isAdmin()) abort(403, 'Admins only.');

        try {
            $query = ActivityLog::with('user')->latest();

            // Filter by action type
            if ($action = $request->input('action')) {
                $query->where('action', $action);
            }

            // Filter by user
            if ($userId = $request->input('user_id')) {
                $query->where('user_id', $userId);
            }

            // Filter by date range
            if ($from = $request->input('from')) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to = $request->input('to')) {
                $query->whereDate('created_at', '<=', $to);
            }

            $logs        = $query->paginate(20)->withQueryString();
            $actionTypes = ActivityLog::select('action')->distinct()->pluck('action');
            $users       = \App\Models\User::orderBy('name')->get(['id', 'name']);

            return view('reports.activity-log', compact('logs', 'actionTypes', 'users'));

        } catch (\Throwable $e) {
            Log::error('ReportsController@activityLog failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to load activity log.');
        }
    }
}