<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'telepon',
        'alamat',
        'peran',
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

    // Mutator & Accessor agar API tetap pakai field standar Laravel
    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }
    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['telepon'] = $value;
    }
    public function getPhoneAttribute()
    {
        return $this->attributes['telepon'] ?? null;
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['alamat'] = $value;
    }
    public function getAddressAttribute()
    {
        return $this->attributes['alamat'] ?? null;
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['peran'] = $value;
    }
    public function getRoleAttribute()
    {
        return $this->attributes['peran'] ?? null;
    }
}
