<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientCRFForm extends Model
{
    protected $table = 'client_crf_form';
    protected $primaryKey = 'id';
    protected $touches = ['client'];

    public function client()
    {
        return $this->belongsTo('App\Client','client_id');
    }


}
