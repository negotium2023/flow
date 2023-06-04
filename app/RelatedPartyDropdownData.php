<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedPartyDropdownData extends Model
{
    protected $table = 'related_party_dropdown_data';

    public function item()
    {
        return $this->belongsTo('App\RelatedPartyDropdownItem',  'related_party_dropdown_item_id');
    }
}
