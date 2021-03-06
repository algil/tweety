<?php

namespace App;

use App\Traits\Followable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, Followable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tweets()
    {
        return $this->hasMany(Tweet::class)->latest();
    }

    public function getAvatarAttribute($value)
    {
        return asset($value ?: '/images/default-avatar.jpeg');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function timeline()
    {
        $followingIds = $this->follows()->pluck('id');

        return Tweet::whereIn('user_id', $followingIds)
            ->orWhere('user_id', $this->id)
            ->withLikes()
            ->orderByDesc('id')
            ->paginate(50);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function profilePath($append = '')
    {
        $profilePath = route('profile', $this->username);
        return $append ? "$profilePath/$append" : $profilePath;
    }
}
