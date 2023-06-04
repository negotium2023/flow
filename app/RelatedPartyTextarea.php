<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyTextarea extends Model
{
    protected $table = 'related_party_textareas';

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyTextareaData');
    }
}
