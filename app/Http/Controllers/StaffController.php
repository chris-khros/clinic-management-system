<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('user')->paginate(10);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $roles = ['admin', 'doctor', 'nurse', 'receptionist', 'pharmacist'];
        return view('staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,doctor,nurse,receptionist,pharmacist',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'hire_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'role' => $request->role,
            'is_active' => true,
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        // Create staff profile
        Staff::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP' . strtoupper(Str::random(8)),
            'full_name' => $request->full_name,
            'photo' => $photoPath,
            'phone' => $request->phone,
            'email' => $request->email,
            'qualifications' => $request->qualifications,
            'department' => $request->department,
            'position' => $request->position,
            'role' => $request->role,
            'hire_date' => $request->hire_date,
            'is_active' => true,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully.');
    }

    public function show(Staff $staff)
    {
        $staff->load('user');
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $staff->load('user');
        $roles = ['admin', 'doctor', 'nurse', 'receptionist', 'pharmacist'];
        return view('staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,doctor,nurse,receptionist,pharmacist',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'hire_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update user account
        $staff->user->update([
            'name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Handle photo upload
        $photoPath = $staff->photo;
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        // Update staff profile
        $staff->update([
            'full_name' => $request->full_name,
            'photo' => $photoPath,
            'phone' => $request->phone,
            'email' => $request->email,
            'qualifications' => $request->qualifications,
            'department' => $request->department,
            'position' => $request->position,
            'role' => $request->role,
            'hire_date' => $request->hire_date,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        // Delete photo
        if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
            Storage::disk('public')->delete($staff->photo);
        }

        // Delete user account
        $staff->user->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully.');
    }

    public function toggleStatus(Staff $staff)
    {
        $staff->update(['is_active' => !$staff->is_active]);
        $staff->user->update(['is_active' => $staff->is_active]);

        $status = $staff->is_active ? 'activated' : 'deactivated';
        return redirect()->route('staff.index')->with('success', "Staff member {$status} successfully.");
    }
}
