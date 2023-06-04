<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyDocument extends Model
{
    protected $table = 'related_party_documents';

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyDocumentData');
    }
}
