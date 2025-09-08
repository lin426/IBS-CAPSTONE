<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('user')->get();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        // NOTE: allow linking to an existing user by email (no unique rule here)
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email'],
            'position' => ['nullable', 'string'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'password' => ['nullable', 'string', 'min:6'],  // optional; default to 123456
        ]);

        $password = $data['password'] ?? '123456';

        // Find or create the loginable user
        $user = User::where('email', $data['email'])->first();
        $createdNewUser = false;

        if (!$user) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($password),
                'role'     => 'staff',
            ]);
            $createdNewUser = true;
        } else {
            // Convert / ensure role and update name; set password if provided
            $user->name = $data['name'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->role = 'staff';
            $user->save();
        }

        // Create staff profile and link user_id
        Staff::create([
            'name'     => $data['name'],
            'position' => $data['position'] ?? null,
            'rating'   => $data['rating'],
            'user_id'  => $user->id,   // make sure Staff::$fillable includes 'user_id'
        ]);

        $msg = $createdNewUser && empty($data['password'])
            ? 'Staff added (default password: 123456).'
            : 'Staff added.';
        return redirect()->route('staff.index')->with('success', $msg);
    }

    public function edit(Staff $staff)
    {
        $staff->load('user');
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $staff->load('user');

        // If staff isn't linked yet, and the email already belongs to a user,
        // allow using that email by ignoring that user's id in the unique rule.
        $incomingEmail   = (string) $request->input('email', '');
        $existingByEmail = $incomingEmail
            ? User::where('email', $incomingEmail)->first()
            : null;

        $ignoreId = optional($staff->user)->id ?? optional($existingByEmail)->id;

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($ignoreId)],
            'position' => ['nullable', 'string'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'password' => ['nullable', 'string', 'min:6'], // optional; only updates if provided
        ]);

        // Update staff profile
        $staff->update([
            'name'     => $data['name'],
            'position' => $data['position'] ?? null,
            'rating'   => $data['rating'],
        ]);

        // Link or create the user for this staff
        $user = $staff->user ?: $existingByEmail;

        if (!$user) {
            // No existing user with this email → create a new one
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password'] ?? '123456'),
                'role'     => 'staff',
            ]);

            $staff->user_id = $user->id;  // requires Staff::$fillable includes 'user_id'
            $staff->save();
        } else {
            // Update/convert existing user and ensure linkage
            $user->name  = $data['name'];
            $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->role = 'staff';
            $user->save();

            if (!$staff->user_id) {
                $staff->user_id = $user->id;
                $staff->save();
            }
        }

        return redirect()->route('staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(Staff $staff)
    {
        // Optional: also delete the linked user (comment in if desired)
        // if ($staff->user) { $staff->user->delete(); }

        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted!');
    }
}
