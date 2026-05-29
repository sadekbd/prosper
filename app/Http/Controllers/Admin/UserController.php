<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role   = $request->query('role', 'all');
        $status = $request->query('status', 'all');

        $query = AdminUser::latest();
        if ($role   !== 'all') $query->where('role',   $role);
        if ($status !== 'all') $query->where('status', $status);

        $users  = $query->paginate(20)->withQueryString();
        $counts = [
            'total'          => AdminUser::count(),
            'super_admin'    => AdminUser::where('role','super_admin')->count(),
            'admin'          => AdminUser::where('role','admin')->count(),
            'article_writer' => AdminUser::where('role','article_writer')->count(),
            'pending'        => AdminUser::where('status','pending')->count(),
            'active'         => AdminUser::where('status','active')->count(),
            'blocked'        => AdminUser::where('status','blocked')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'status', 'counts'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => ['required','in:active,pending,blocked']]);
        $user = AdminUser::findOrFail($id);

        // Protect super admin from being blocked
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot modify super admin status.');
        }

        $user->update(['status' => $request->status]);
        ActivityLog::log('updated_user_status', 'AdminUser', $id, "Status set to: {$request->status}");

        $label = ucfirst($request->status);
        return back()->with('success', "{$user->full_name} — status set to {$label}.");
    }

    public function updateRole(Request $request, int $id)
    {
        $request->validate(['role' => ['required','in:super_admin,admin,article_writer']]);
        $user = AdminUser::findOrFail($id);

        if ($user->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);
        ActivityLog::log('updated_user_role', 'AdminUser', $id, "Role set to: {$request->role}");
        return back()->with('success', "Role updated to " . ucfirst(str_replace('_',' ',$request->role)) . ".");
    }

    public function destroy(int $id)
    {
        $user = AdminUser::findOrFail($id);
        if ($user->id === auth('admin')->id() || $user->role === 'super_admin') {
            return back()->with('error', 'This account cannot be deleted.');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }
}