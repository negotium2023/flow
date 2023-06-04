<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionableTemplateEmailData extends Model
{
    protected $table = 'actionable_template_email_data';

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
