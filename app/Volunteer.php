<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserInProjectTraits;
use App\Traits\ManageProjectTraits;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Volunteer extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        UserInProjectTraits,
        ManageProjectTraits;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'volunteers';

    /**
     * The attributes are allowed to be mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'first_name', 'last_name',
                            'birth_year', 'gender', 'city', 'address',
                            'phone_number', 'email', 'emergency_contact', 'emergency_phone',
                            'introduction'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['id', 'username', 'password', 'first_name', 'last_name',
                            'birth_year', 'gender', 'city', 'address',
                            'phone_number', 'email', 'emergency_contact', 'emergency_phone',
                            'introduction'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_actived' => 'boolean',
        'is_locked' => 'boolean'
    ];

    /**
     * Relationship with `App\City` model
     *
     * @return [type] [description]
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    /**
     * Relationship with `App\VerificationCode` model
     *
     * @return [type] [description]
     */
    public function verificationCode()
    {
        return $this->hasOne('App\VerificationCode');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Skill');
    }

    public function equipment()
    {
        return $this->belongsToMany('App\Equipment');
    }

    public function educations()
    {
        return $this->hasMany('App\Education');
    }

    public function experiences()
    {
        return $this->hasMany('App\Experience');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
