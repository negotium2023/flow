<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'area_id');
    }

    public function office()
    {
        return $this->belongsTo('App\Office', 'office_id');
    }

    public function pgroup()
    {
        return $this->belongsTo('App\ProcessGroup', 'process_group_id','id');
    }

    public function process_area(){
        return $this->hasMany('App\ProcessArea', 'process_id','id');
    }

    public function step($id)
    {
        return $this->hasMany('App\Step', 'process_id')->where('id', $id)->orderBy('order');
    }

    public function steps()
    {
        return $this->hasMany('App\Step', 'process_id')->orderBy('order')->withTrashed();
    }

    public function steps2()
    {
        return $this->hasMany('App\Step', 'process_id')->orderBy('order');
    }

    public function activities()
    {
        return $this->hasManyThrough('App\Activity', 'App\Step')->orderBy('order');
    }

    public function getStageHex($stage, $with_opacity = true)
    {

        $opacity = dechex(0.25 * 255);

        switch ($stage) {
            case 0:
                //return '#' . $this->not_started_colour . (($with_opacity) ? $opacity : '');
                /*return $this->not_started_colour;*/
                return 'rgba(242,99,91,.7)';
                //return '#dfdfdf';
                break;
            case 1:
                //return '#' . $this->started_colour . (($with_opacity) ? $opacity : '');
                /*return $this->started_colour;*/
                return 'rgba(252,182,61,.7)';
                //return '#ababab';
                break;
            case 2:
                //return '#' . $this->completed_colour . (($with_opacity) ? $opacity : '');
                /*return $this->completed_colour;*/
                return 'rgba(50,193,75,.7)';
                //return '#838383';
                break;
        }
    }

    public function getStageHex2($stage, $with_opacity = true)
    {

        $opacity = dechex(0.25 * 255);

        switch ($stage) {
            case 0:
                //return '#' . $this->not_started_colour . (($with_opacity) ? $opacity : '');
                /*return $this->not_started_colour;*/
                //return 'rgba(242,99,91,.7)';
                return '#dfdfdf';
                break;
            case 1:
                //return '#' . $this->started_colour . (($with_opacity) ? $opacity : '');
                /*return $this->started_colour;*/
                //return 'rgba(252,182,61,.7)';
                return '#ababab';
                break;
            case 2:
                //return '#' . $this->completed_colour . (($with_opacity) ? $opacity : '');
                /*return $this->completed_colour;*/
                //return 'rgba(50,193,75,.7)';
                return '#838383';
                break;
        }
    }
}