<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomReportColumns extends Model
{
    protected $table = 'custom_report_columns';

    public function activity_name(){
        return $this->belongsTo('App\Activity','activity_id','id');
    }

    public function crm_name(){
        return $this->belongsTo('App\FormSectionInputs','activity_id','id');
    }
}
