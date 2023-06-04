<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessGroup extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'processes_groups';

    public function process(){
        return $this->hasMany('App\Process','process_group_id','id');
    }
}
