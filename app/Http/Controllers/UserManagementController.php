<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    private const ALLOWED_ROLES = ['super_admin', 'customer', 'gate_agent'];

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
        $adminsCount = User::query()->whereIn('role', ['super_admin', 'admin'])->count();

        return view('pages.admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roleOptions' => self::ALLOWED_ROLES,
            'createRoleOptions' => self::ALLOWED_ROLES,
            'isSuperAdmin' => (bool) $request->user()?->isSuperAdmin(),
            'totalUsers' => $totalUsers,
            'customersCount' => $customersCount,
            'adminsCount' => $adminsCount,
        ]);
    }

    public function storeAgent(Request $request)
    {
        $this->ensureSuperAdmin($request);

        $normalizedPhone = preg_replace('/(?!^)\+|[^\d+]/', '', trim((string) $request->input('phone')));
        if (str_starts_with((string) $normalizedPhone, '00')) {
            $normalizedPhone = '+' . substr((string) $normalizedPhone, 2);
        }

        $normalizedPassword = trim((string) $request->input('password'));

        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => blank($normalizedPhone) ? null : $normalizedPhone,
            'password' => blank($normalizedPassword) ? null : $normalizedPassword,
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)],
            'password' => ['nullable', 'string', 'max:72', Password::min(8)->mixedCase()->numbers()],
            'profile_image' => ['nullable', 'image', 'max:3072'],
        ], [
            'email.email' => 'Please enter a valid email address with a real domain.',
            'phone.regex' => 'Please enter a valid phone number in international format, for example +256700000000.',
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
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)],
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
