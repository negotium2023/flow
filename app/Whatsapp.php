<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Whatsapp extends Model
{
    protected $table = 'whatsapps';

    protected $fillable = [
        'client_id', 'message',
    ];

    public function clients()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
