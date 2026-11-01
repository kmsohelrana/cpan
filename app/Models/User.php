<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'mobile',
        'email',
        'password',
        'password_changed_at',
        'change_password',
        'allowed_ips',
        'password_history',
        'image',
        'fcm_token',
        'web_fcm_token',
        'language',
        'last_login_at',
        'status',
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
            'password_changed_at' => 'datetime',
            'change_password' => 'boolean',
            'password_history' => 'array',
            'status' => StatusEnum::class,
        ];
    }

    /**
     * Get the status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status->badge();
    }

    public function getImageAttribute($value)
    {
        return $value ? asset(Storage::url($value)) : null;
    }

    /**
     * Set the password history attribute.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordHistoryAttribute($value)
    {
        $passwordHistory = $this->password_history ?? [];
        array_unshift($passwordHistory, $value);
        $passwordHistory = array_slice($passwordHistory, 0, 3);
        $this->attributes['password_history'] = json_encode($passwordHistory);
    }

    /**
     * Get the user device associated with the account.
     */
    public function device(): HasOne
    {
        return $this->hasOne(UserDevice::class, 'user_id', 'id')
            ->where('model_type', self::class);
    }
}
