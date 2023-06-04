<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StepsRelatedPartiesLink extends Model
{
    protected $dates = ['deleted_at'];
    protected $table = 'steps_related_parties_link';
}
