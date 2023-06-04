<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyComment extends Model
{
    protected $table = 'related_parties_comments';

    public function client()
    {
        return $this->belongsTo('App\RelatedParty', 'related_party_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
