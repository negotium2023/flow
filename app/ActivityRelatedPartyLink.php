<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityRelatedPartyLink extends Model
{
    protected $dates = ['deleted_at'];
    protected $table = 'activities_related_parties_link';
}
