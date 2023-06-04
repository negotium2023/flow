<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeUser extends Model
{
    protected $table = 'office_user';

    public function roles()
    {
        return $this->hasMany('App\RoleUser', 'user_id','user_id');
    }
}
