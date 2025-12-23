<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $pendingUsers = User::where('status', 'pending')->count();

        return view('admin.dashboard', compact('totalUsers', 'activeUsers', 'pendingUsers'));
    }

    public function allUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function pendingUsers()
    {
        $users = User::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.pending', compact('users'));
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->back()->with('success', 'User activated successfully.');
    }

    public function deactivateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);

        return redirect()->back()->with('success', 'User deactivated successfully.');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        // Update settings logic
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
