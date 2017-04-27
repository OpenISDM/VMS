<?php

/**
 * The model corresponds to volunteers table. It provides volunteer's information
 * like, profile, managing and attending projects.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2016-06-24T10:40:23+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T14:49:06+08:00
 * @License: GPL-3
 */

namespace App;

use App\Traits\ManageProjectTraits;
use App\Traits\UserInProjectTraits;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Volunteer extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    /*
     * Traits.
     *
     * Authenticatable:     It provides authentication for the model.
     * Authorizable:        It provides authorization for the model.
     * CanResetPassword:    It provides password resestting for the model.
     * UserInProjectTraits: It defines the relationship that the user attends projects.
     * ManageProjectTratis: It defines the relationship that the user manages projects.
     */
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        UserInProjectTraits,
        ManageProjectTraits;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'volunteers';

    /**
     * The attributes are allowed to be mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'birth_year',
        'gender',
        'city',
        'location',
        'phone_number',
        'email',
        'emergency_contact',
        'emergency_phone',
        'introduction',
        'avatar_path',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'username',
        'first_name',
        'last_name',
        'birth_year',
        'gender',
        'city',
        'location',
        'phone_number',
        'email',
        'emergency_contact',
        'emergency_phone',
        'introduction',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'is_actived' => 'boolean',
        'is_locked'  => 'boolean',
    ];

    /**
     * Relationship with App\City model.
     * It represents that the user lives in the city.
     *
     * @return [type] [description]
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    /**
     * Relationship with App\VerificationCode model.
     * It represents the user's email verification code.
     *
     * @return [type] [description]
     */
    public function verificationCode()
    {
        return $this->hasOne('App\VerificationCode');
    }

    /**
     * Relationship with App\Skill model.
     * It represents tht voluntee's skills.
     *
     * @return [type] [description]
     */
    public function skills()
    {
        return $this->belongsToMany('App\Skill');
    }

    /**
     * Relationship with App\Equipment model.
     * It represents the user's equipment.
     *
     * @return [type] [description]
     */
    public function equipment()
    {
        return $this->belongsToMany('App\Equipment');
    }

    /**
     * Relationship with App\Education model.
     * It represents the user's educations.
     *
     * @return [type] [description]
     */
    public function educations()
    {
        return $this->hasMany('App\Education');
    }

    /**
     * Relationship with App\Education model.
     * It represents the user's experiences.
     *
     * @return [type] [description]
     */
    public function experiences()
    {
        return $this->hasMany('App\Experience');
    }

    /**
     * The user's identifier for storing in JWT.
     *
     * @return mixed user's identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * The custom data for storing in JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
