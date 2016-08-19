<?php

/**
 * The model corresponds to experiences table. It contains company, job_title,
 * start_year and end_year.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2015-10-21T10:24:15+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T16:06:08+08:00
 * @License: GPL-3
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'experiences';

    /**
     * The attributes are allowed to be mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company',
        'job_title',
        'start_year',
        'end_year'
    ];

    /**
     * The attributes that should be casted into native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'volunteer_id' => 'integer'
    ];

    /**
     * Relationship with App\App\Volunteer model
     * It represents that the experience belongs to volunteer model.
     *
     * @return [type] [description]
     */
    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
