<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSignature extends Model
{
    use SoftDeletes;

    protected $table = 'email_signature';
    protected $dates = ['deleted_at'];
}
