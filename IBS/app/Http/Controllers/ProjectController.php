<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::all();
        return view('projects.create', compact('clients'));
    }

public function store(Request $request)
{
    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'name' => 'required|string',
        'result' => 'nullable|string',
        'created_at' => 'required|date',
        'created_at.required' => 'Please select a creation date.',


    ]);

Project::create([
    'client_id' => $request->client_id,
    'name' => $request->name,
    'result' => $request->result,
    'created_at' => $request->created_at,
    'updated_at' => now()
    
]);

    return redirect()->route('projects.index')->with('success', 'Project created.');
}


    public function edit(Project $project)
    {
        $clients = Client::all();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string',
            'result' => 'nullable|string'

        ]);

        $project->update($request->all());
        
        

        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
