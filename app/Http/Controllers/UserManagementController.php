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

        return view('pages.admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roleOptions' => ['customer', 'agent', 'gate', 'gate_agent', 'admin'],
            'isSuperAdmin' => (bool) $request->user()?->isSuperAdmin(),
        ]);
    }

    public function storeAgent(Request $request)
    {
        $this->ensureSuperAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', Rule::in(['agent', 'gate', 'gate_agent'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => $data['password'] ?? Str::random(12),
        ]);

        return back()->with('success', 'Agent account created successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $this->ensureSuperAdmin($request);

        $data = $request->validate([
            'role' => ['required', 'string', Rule::in(['customer', 'agent', 'gate', 'gate_agent', 'admin'])],
        ]);

        $user->forceFill([
            'role' => $data['role'],
        ])->save();

        return back()->with('success', 'User role updated successfully.');
    }

    protected function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    protected function ensureSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
    }
}
