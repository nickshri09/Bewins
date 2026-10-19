<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\EmailNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\EmailNotification;

class EmailNotificationController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('emails.index', compact('users'));
    }

    public function send(Request $request)
    {

        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'users' => 'required|array',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('email_notifications');
        }

        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new EmailNotificationMail($request->title, $request->message, $attachmentPath));
        }

        EmailNotification::create([
            'title' => $request->title,
            'message' => $request->message,
            'user_ids' => json_encode($request->users),
            'attachment' => $attachmentPath
        ]);

        return back()->with('success', 'Email Notification sent successfully!');
    }
}

