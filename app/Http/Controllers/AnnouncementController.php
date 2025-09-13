<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        if (in_array(Auth::user()->role, ['doctor', 'nurse', 'receptionist', 'pharmacist'])) {
            $announcements = Announcement::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->where(function ($query) {
                    $query->where('visibility', 'public')
                          ->orWhere('visibility', 'internal');
                })
                ->latest()
                ->paginate(10);
        } else {
            $announcements = Announcement::latest()->paginate(10);
        }

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('announcements.create');
    }

    public function store(Request $request, WhatsappService $whatsappService)
    {
        $this->authorizeAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:internal,public,staff,patients',
            'expires_at' => 'nullable|date|after:now',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        // Create announcement
        $announcement = Announcement::create([
            'created_by' => Auth::user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'attachment' => $attachmentPath,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        // Only send WhatsApp if admin checked the box
        $count = 0;
        if ($request->boolean('send_whatsapp')) {
            $count = $whatsappService->broadcastAnnouncement($announcement);
        }

        return redirect()->route('announcements.index')
            ->with('success', "Announcement created successfully." .
                ($count ? " WhatsApp message sent to {$count} staff." : ""));
    }

    public function show(Announcement $announcement)
    {
        if (in_array(Auth::user()->role, ['doctor', 'nurse', 'receptionist', 'pharmacist'])) {
            if (!$announcement->is_active || ($announcement->expires_at && $announcement->expires_at->isPast())) {
                abort(403, 'Unauthorized');
            }
        }

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorizeAdmin();
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement, WhatsappService $whatsappService)
    {
        $this->authorizeAdmin();

        if ($request->has('toggle_status')) {
            $announcement->is_active = !$announcement->is_active;
            $announcement->save();

            $status = $announcement->is_active ? 'activated' : 'deactivated';
            return redirect()->route('announcements.index')
                ->with('success', "Announcement {$status} successfully.");
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:internal,public,staff,patients',
            'expires_at' => 'nullable|date',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'send_whatsapp' => 'nullable|boolean',
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

        // Only send WhatsApp if admin checked the box
        $count = 0;
        if ($request->boolean('send_whatsapp')) {
            $count = $whatsappService->broadcastAnnouncement($announcement);
        }

        return redirect()->route('announcements.index')
            ->with('success', "Announcement updated successfully." .
                ($count ? " WhatsApp message sent to {$count} staff." : ""));
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeAdmin();

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

    // Restrict access to admins only.
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}
