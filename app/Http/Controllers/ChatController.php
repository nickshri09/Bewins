<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Chat;



class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::latest()->get();
        return view('layouts.menus.view_order_single', compact('chats'));
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'message' => 'required|string',
        ]);

        Chat::create([
            'name' => $request->name,
            'message' => $request->message,
        ]);

        return redirect()->back();
    }
}
