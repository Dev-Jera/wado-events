<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
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

    public function gateAssignedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_gate_agent', 'user_id', 'event_id')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGateAgent(): bool
    {
        return in_array($this->role, ['gate_agent', 'agent', 'gate', 'verification_officer'], true);
    }

    public function isGateStaff(): bool
    {
        return $this->isSuperAdmin() || $this->isGateAgent();
    }

    public function isVerificationOfficer(): bool
    {
        return $this->isGateAgent();
    }

    public function isEventOwner(): bool
    {
        return $this->role === 'event_owner';
    }

    public function canViewOperationsDashboard(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canAccessOperationsPanel(): bool
    {
        return $this->canViewOperationsDashboard() || $this->isGateStaff();
    }

    public function canAccessGateEvent(int $eventId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->isGateAgent()) {
            return false;
        }

        return $this->gateAssignedEvents()->where('events.id', $eventId)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->canAccessOperationsPanel();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->profile_image_path) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->profile_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_image_path);
    }
}
