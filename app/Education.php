<?php

/**
 * The model corresponds to educations table. It represents a volunteer's
 * education including school, degree, field_of_study, start_year and end_year.
 *
 * @Author: Yi-Ming, Huang <aming>
 * @Date:   2015-10-21T10:24:15+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-07-29T15:33:24+08:00
 * @License: GPL-3
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'educations';

    /**
     * The attributes are allowed to be mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school',
        'degree',
        'field_of_study',
        'start_year',
        'end_year',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'volunteer_id' => 'integer',
    ];

    /**
     * Relationship with App\Volunteer.
     * It represents that the education belongs to volunteer model.
     *
     * @return [type] [description]
     */
    public function volunteer()
    {
        return $this->belongsTo('App\Volunteer');
    }
}
