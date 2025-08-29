<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $announcements = Announcement::with('creator')->paginate(10);
        } else {
            $announcements = Announcement::where('visibility', 'public')
                ->orWhere('visibility', 'internal')
                ->with('creator')
                ->paginate(10);
        }

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:internal,public',
            'expires_at' => 'nullable|date|after:now',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        // Create announcement
        Announcement::create([
            'created_by' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'attachment' => $attachmentPath,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('creator');
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:internal,public',
            'expires_at' => 'nullable|date',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Handle attachment upload
        $attachmentPath = $announcement->attachment;
        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        // Update announcement
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'attachment' => $attachmentPath,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        // Delete attachment
        if ($announcement->attachment && Storage::disk('public')->exists($announcement->attachment)) {
            Storage::disk('public')->delete($announcement->attachment);
        }

        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        
        $status = $announcement->is_active ? 'activated' : 'deactivated';
        return redirect()->route('announcements.index')->with('success', "Announcement {$status} successfully.");
    }

    public function publicIndex()
    {
        $announcements = Announcement::where('visibility', 'public')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('announcements.public', compact('announcements'));
    }
}
