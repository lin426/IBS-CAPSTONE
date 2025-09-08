<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('project')->get();
        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('messages.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Message::create($request->all());
        return redirect()->route('messages.index');
    }

    public function show(Message $message)
    {
        return view('messages.show', compact('message'));
    }

    public function edit(Message $message)
    {
        $projects = Project::all();
        return view('messages.edit', compact('message', 'projects'));
    }

    public function update(Request $request, Message $message)
    {
        $message->update($request->all());
        return redirect()->route('messages.index');
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index');
    }
}

