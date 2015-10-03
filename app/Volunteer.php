<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    protected $table = 'volunteers';
    protected $fillable = ['username', 'password', 'first_name', 'last_name', 
                            'birth_year', 'gender', 'city', 'address',
                            'phone_number', 'email', 'emergency_contact', 'emergency_phone',
                            'introduction'];
    protected $hidden = ['password', 'remember_token'];

    public function cities()
    {
        return $this->belongsToMany('App\City');
    }

    public function verificationToken()
    {
        return $this->hasOne('App\VerificationCode');
    }
}
