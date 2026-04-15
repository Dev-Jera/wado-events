<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_image_path',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(EventBookmark::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGateAgent(): bool
    {
        return in_array($this->role, ['agent', 'gate', 'gate_agent'], true);
    }

    public function isGateStaff(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isGateAgent() || $this->isVerificationOfficer();
    }

    public function isVerificationOfficer(): bool
    {
        return $this->role === 'verification_officer';
    }

    public function isEventOwner(): bool
    {
        return $this->role === 'event_owner';
    }

    public function canViewOperationsDashboard(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isVerificationOfficer() || $this->isEventOwner();
    }

    public function canAccessOperationsPanel(): bool
    {
        return $this->canViewOperationsDashboard() || $this->isGateStaff();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->canAccessOperationsPanel();
    }
}
