<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status  = $request->query('status', 'all');
        $search  = $request->query('search', '');

        $query = ContactMessage::latest();
        if ($status !== 'all') $query->where('status', $status);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20)->withQueryString();
        $counts   = [
            'all'      => ContactMessage::count(),
            'new'      => ContactMessage::where('status','new')->count(),
            'read'     => ContactMessage::where('status','read')->count(),
            'replied'  => ContactMessage::where('status','replied')->count(),
            'archived' => ContactMessage::where('status','archived')->count(),
        ];

        return view('admin.messages.index', compact('messages', 'status', 'search', 'counts'));
    }

    public function show(int $id)
    {
        $message = ContactMessage::findOrFail($id);
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }
        return view('admin.messages.show', compact('message'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => ['required','in:new,read,replied,archived']]);
        $message = ContactMessage::findOrFail($id);
        $updates = ['status' => $request->status];
        if ($request->status === 'replied') {
            $updates['replied_at'] = now();
        }
        $message->update($updates);
        return back()->with('success', 'Message status updated.');
    }

    public function destroy(int $id)
    {
        ContactMessage::findOrFail($id)->delete();
        return redirect()->route('admin.messages')->with('success', 'Message deleted.');
    }
}