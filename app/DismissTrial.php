<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DismissTrial extends Model
{
    public function getTrialExpiryDateAttribute($date)
    {
        return Carbon::parse($date);
    }
}
