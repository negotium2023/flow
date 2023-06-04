<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyMultipleAttachment extends Model
{
    protected $table = 'related_party_multiple_attachments';

    public function activity()
    {
        return $this->morphOne('App\Activity', 'actionable');
    }

    public function data()
    {
        return $this->hasMany('App\RelatedPartyMultipleAttachmentData', 'related_party_ma_id', 'id');
    }
}
