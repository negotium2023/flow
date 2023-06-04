<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityInClientBasket extends Model
{
    protected $fillable = ['client_id', 'activity_id', 'in_client_basket'];

    public function activity()
    {
        return $this->belongsTo('App\Activity', 'activity_id');
    }
}
