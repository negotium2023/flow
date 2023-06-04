<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomReport extends Model
{
    protected $table = 'custom_report';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    public function custom_report_columns(){
        return $this->hasMany('App\CustomReportColumns','custom_report_id','id');
    }
}
