<?php

/**
 * The model corresponds to skills table. It contains skill name.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2015-11-19T14:59:59+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T15:57:08+08:00
 * @License: GPL-3
 */

namespace App;

use App\Traits\CandidateKeywordsTrait;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /*
     * CandidateKeywordsTrait queries candidiated equipment keyword.
     */
    use CandidateKeywordsTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'skills';

    /**
     * The attributes are allowd to be mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The attribtes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Relationship with App/Volunteer model.
     * It represents that the skill belongs to volunteers.
     *
     * @return [type] [description]
     */
    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer');
    }
}
