<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
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
        ];
    }

    public function user_detail()
    {
        return $this->hasOne(UserDetail::class, 'id_user', 'id');
    }

     public function sender()
    {
        return $this->hasMany(UserDetail::class, 'sender_id', 'id');
    }

    public function pushSubscriptions()
{
    // HasMany karena 1 user bisa punya banyak device (Laptop, HP, dll)
    return $this->hasMany(PushSubscription::class, 'user_id', 'id');
}

    public function sendPasswordResetNotification($token)
{
    // gunakan nama user untuk dynamic FROM NAME
    $dynamicName = $this->name ?? 'Admin';

    $this->notify(new CustomResetPassword($token, $dynamicName));
}
}
