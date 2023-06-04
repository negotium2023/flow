<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedPartiesTree extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'related_parties_tree';

    public function tree(){
        return $this->hasMany(RelatedParty::class, 'id','related_party_id');
    }
}
