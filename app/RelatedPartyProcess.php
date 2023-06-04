<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedPartyProcess extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function process()
    {
        return $this->belongsTo('App\Process', 'process_id');
    }
}
