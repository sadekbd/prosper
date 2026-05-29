<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $query  = NewsletterSubscriber::latest('subscribed_at');
        if ($status !== 'all') $query->where('status', $status);

        $subscribers = $query->paginate(25)->withQueryString();
        $counts = [
            'active'       => NewsletterSubscriber::where('status','active')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status','unsubscribed')->count(),
        ];

        return view('admin.newsletter.index', compact('subscribers', 'status', 'counts'));
    }

    public function destroy(int $id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        return back()->with('success', 'Subscriber removed.');
    }
}