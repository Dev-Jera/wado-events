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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        'provider',
        'provider_id',
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
        return $this->isAdmin();
    }

    public function canAccessOperationsPanel(): bool
    {
        return $this->canViewOperationsDashboard() || $this->isGateStaff() || $this->isEventOwner();
    }

    public function canAccessGateEvent(int $eventId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->isGateAgent()) {
            return false;
        }

        return Event::query()
            ->whereKey($eventId)
            ->where(function ($query): void {
                $query->where('user_id', $this->id)
                    ->orWhereHas('gateAgents', fn ($assigned) => $assigned->where('users.id', $this->id));
            })
            ->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->canAccessOperationsPanel();
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));
        $name     = $this->name ?: 'there';

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family:sans-serif;background:#f4f4f4;margin:0;padding:24px;">
          <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:10px;padding:32px 28px;">
            <h2 style="color:#0a1525;margin:0 0 8px;">Reset your password</h2>
            <p style="color:#555;margin:0 0 24px;">Hi {$name}, we received a request to reset your WADO Tickets password. Click the button below. This link expires in 60 minutes.</p>
            <a href="{$resetUrl}" style="display:inline-block;background:#e8241a;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;font-size:15px;">Reset password</a>
            <p style="color:#999;font-size:13px;margin:24px 0 0;">If you didn't request this, ignore this email — your password won't change.</p>
            <p style="color:#ccc;font-size:12px;margin:8px 0 0;">Or copy this link: {$resetUrl}</p>
          </div>
        </body>
        </html>
        HTML;

        $apiKey = (string) config('services.brevo.api_key', '');

        if ($apiKey === '') {
            Log::error('Password reset email failed: BREVO_API_KEY not configured');
            return;
        }

        $response = Http::timeout(15)
            ->withHeaders(['api-key' => $apiKey, 'Accept' => 'application/json'])
            ->post('https://api.brevo.com/v3/smtp/email', [
                'sender'      => ['name' => 'WADO Tickets', 'email' => config('mail.from.address')],
                'to'          => [['email' => $this->email, 'name' => $this->name]],
                'subject'     => 'Reset your WADO Tickets password',
                'htmlContent' => $html,
            ]);

        if (! $response->successful()) {
            Log::error('Password reset email failed via Brevo API', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        }
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
