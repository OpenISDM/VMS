<?php

/**
 * The model corresponds to the cities model. It contains each city's name.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2015-10-21T10:24:15+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T15:22:39+08:00
 * @License: GPL-3
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Disable timestamps in the table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relationship with App\Volunteer model
     * It represents the city which many volunteers live.
     *
     * @return [type] [description]
     */
    public function volunteers()
    {
        return $this->hasMany('App\Volunteer');
    }

    /**
     * Relationship with App\Country
     * It represents that the city belongs to which country.
     *
     * @return [type] [description]
     */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
