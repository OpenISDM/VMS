<?php

/**
 * The model corresponds to countries table. It contains each country name.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2015-10-21T10:24:15+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T15:00:40+08:00
 * @License: GPL-3
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\City;

class Country extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Disable timestamps in the model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relationship with City.
     * It represents that the country has cities.
     *
     * @return [type] [description]
     */
    public function cities()
    {
        return $this->hasMany('App\City');
    }
}
