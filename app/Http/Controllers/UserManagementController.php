<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $totalUsers = User::query()->count();
        $customersCount = User::query()->where('role', 'customer')->count();
        $adminsCount = User::query()->whereIn('role', ['admin', 'super_admin'])->count();

        return view('pages.admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roleOptions' => ['customer', 'agent', 'gate', 'gate_agent', 'verification_officer', 'event_owner', 'admin'],
            'createRoleOptions' => ['customer', 'agent', 'gate', 'gate_agent', 'verification_officer', 'event_owner', 'admin'],
            'isSuperAdmin' => (bool) $request->user()?->isSuperAdmin(),
            'totalUsers' => $totalUsers,
            'customersCount' => $customersCount,
            'adminsCount' => $adminsCount,
        ]);
    }

    public function storeAgent(Request $request)
    {
        $this->ensureSuperAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', Rule::in(['customer', 'agent', 'gate', 'gate_agent', 'verification_officer', 'event_owner', 'admin'])],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_image' => ['nullable', 'image', 'max:3072'],
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('users/profile-images', 'public');
        }

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'profile_image_path' => $profileImagePath,
            'role' => $data['role'],
            'password' => $data['password'] ?? Str::random(12),
        ]);

        return back()->with('success', 'Account created successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $this->ensureSuperAdmin($request);

        $data = $request->validate([
            'role' => ['required', 'string', Rule::in(['customer', 'agent', 'gate', 'gate_agent', 'verification_officer', 'event_owner', 'admin'])],
        ]);

        $user->forceFill([
            'role' => $data['role'],
        ])->save();

        return back()->with('success', 'User role updated successfully.');
    }

    protected function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin() || $request->user()?->isSuperAdmin(), 403);
    }

    protected function ensureSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
    }
}
