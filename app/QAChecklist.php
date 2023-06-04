<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QAChecklist extends Model
{
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
