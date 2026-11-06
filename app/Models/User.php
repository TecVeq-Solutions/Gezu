<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin' 
            || $this->email === 'admin@gezux.io' 
            || $this->email === 'admin@gezux.com';
    }

    public function isBrand(): bool
    {
        return strtolower($this->role ?? '') === 'brand';
    }

    public function isClipper(): bool
    {
        return strtolower($this->role ?? '') === 'clipper';
    }

    public function isStreamer(): bool
    {
        return strtolower($this->role ?? '') === 'streamer';
    }

    public function getRedirectUrl(): string
    {
        if ($this->isAdmin()) {
            return '/admin';
        }
        if ($this->isBrand()) {
            return '/brand';
        }
        if ($this->isStreamer()) {
            return '/streamer';
        }
        if ($this->isClipper()) {
            return '/clipper';
        }
        return '/clipper';
    }

    public function verificationCodes()
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function brand()
    {
        return $this->hasOne(Brand::class);
    }

    public function creatorProfile()
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function submissions()
    {
        return $this->hasMany(ClipSubmission::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function participations()
    {
        return $this->hasMany(CampaignParticipation::class);
    }

    public function savedCampaigns()
    {
        return $this->hasMany(SavedCampaign::class);
    }

    public function joinedCampaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_participations')
            ->withPivot(['status', 'joined_at', 'completed_at'])
            ->withTimestamps();
    }
}
