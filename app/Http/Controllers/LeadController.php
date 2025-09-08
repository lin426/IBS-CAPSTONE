<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Models\Client;

class LeadController extends Controller {
    public function index() {
        $leads = Lead::all();
        return view('leads.index', compact('leads'));
    }

    public function create() {
        return view('leads.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            'stage' => 'required|string',
            'value' => 'required|numeric|min:0',
            'status' => 'required|string|in:open,won,lost',
        ]);

        Lead::create($request->all());
        return redirect()->route('leads.index')->with('success', 'Lead added.');
    }

    public function edit(Lead $lead) {
        return view('leads.edit', compact('lead'));
    }

public function update(Request $request, Lead $lead)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'contact' => 'required|email|regex:/[a-zA-Z0-9._%+-]+@gmail\.com$/',
        'stage' => 'required|string',
        'value' => 'required|numeric|min:0',
        'status' => 'required|string|in:open,won,lost',
    ]);

    $lead->update($request->all());

    // Auto-convert to Client if closed + won
    if ($lead->stage === 'Closed' && $lead->status === 'won') {
        $existingClient = \App\Models\Client::where('email', $lead->contact)->first();
        if (!$existingClient) {
            \App\Models\Client::create([
                'name' => $lead->name,
                'email' => $lead->contact,
            ]);
        }
    }

    return redirect()->route('leads.index')->with('success', 'Lead updated.');
}



    public function destroy(Lead $lead) {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }

    public function chart() {
        $data = Lead::select('stage', \DB::raw('count(*) as total'))
                    ->groupBy('stage')
                    ->get();

        $labels = $data->pluck('stage');
        $totals = $data->pluck('total');

        return view('leads.chart', compact('labels', 'totals'));
    }
}
