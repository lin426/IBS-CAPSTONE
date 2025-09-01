<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
   public function index()
{
    $staff = Staff::all();
    return view('staff.index', compact('staff'));
}

public function create()
{
    return view('staff.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'position' => 'nullable|string',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    Staff::create($request->all());
    return redirect()->route('staff.index')->with('success', 'Staff added!');
}

public function edit(Staff $staff)
{
    return view('staff.edit', compact('staff'));
}

public function update(Request $request, Staff $staff)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'position' => 'nullable|string',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    $staff->update($request->all());
    return redirect()->route('staff.index')->with('success', 'Staff updated!');
}

public function destroy(Staff $staff)
{
    $staff->delete();
    return redirect()->route('staff.index')->with('success', 'Staff deleted!');
}
}
