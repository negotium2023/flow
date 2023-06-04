<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyTemplateEmail extends Model
{
    protected $table = 'related_party_template_emails';

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyTemplateEmailData');
    }
}
