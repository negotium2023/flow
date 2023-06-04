<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
// use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cmgmyr\Messenger\Traits\Messagable;
// use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;
    /*
    use HasEncryptedAttributes;
    use EntrustUserTrait {
        SoftDeletes::restore insteadof EntrustUserTrait;
        EntrustUserTrait::restore insteadof SoftDeletes;
    }
    */

    protected $encrypted = ['first_name','last_name'];

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'avatar','last_login_at','last_login_ip'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $dates = ['deleted_at'];


    use Messagable;


    public function name()
    {
        if ($this->last_name == '') {
            return $this->first_name;
        } else {
            return $this->first_name . ' ' . $this->last_name;
        }
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function divisions()
    {
        return $this->belongsToMany('App\Division', 'division_user');
    }

    public function regions()
    {
        return $this->belongsToMany('App\Region', 'region_user');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Area', 'area_user');
    }

    public function all_areas(){
        $id = $this->id;

        $areas = Area::with('users')->get();

        dd($areas);
    }

    public function offices()
    {
        return $this->belongsToMany('App\Office', 'office_user');
    }

    public function notifications()
    {
        return $this->hasManyThrough('App\Notification','App\UserNotification', 'user_id', 'id', 'id', 'notification_id')->orderBy('created_at','desc');
    }

    public function domains()
    {
        //TODO; I need to finish this

        $divisions = $this->divisions()->with('regions.areas.offices');
        $regions = $this->regions()->setEagerLoads(['areas.offices']);
        $areas = $this->areas()->setEagerLoads(['offices']);

        dd($divisions);

        $office_array = [];
        foreach ($divisions as $division) {

            foreach ($division->regions as $regions) {
                foreach ($regions->areas as $area) {
                    foreach ($area->offices as $office) {
                        array_push($office_array,$office->id);
                    }
                }
            }
        }

        return $office_array;
    }

    public function domainCount()
    {
        return $this->divisions->count() + $this->regions->count() + $this->areas->count() + $this->offices->count();
    }

    public function activeOffice()
    {
        return $this->belongsTo('App\Office', 'active_office_id');
    }

    public function office()
    {
        if (is_null($this->active_office_id)) {
            return $this->offices()->first();
        } else {
            return $this->activeOffice;
        }
    }

    public function verifyUser()
    {
        return $this->belongsTo('App\VerifyUser');
    }

    public function is($roleName)
    {
        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == $roleName)
            {
                return true;
            }
        }

        return false;
    }

    public function isNot($roleName)
    {
        foreach ($this->roles()->get() as $role)
        {
            if ($role->name != $roleName)
            {
                return true;
            }
        }

        return false;
    }

    public function countUsersPerOffice(){
        $office_user = app('App\OfficeUser');
        $office_id = $office_user->whereUserId(auth()->id())->first();
        return $office_user->whereOfficeId($office_id->office_id)->get()->count();
    }

    public function isTrial(){
        return User::select('trial')->where('id',Auth::id())->first()->trial;
    }

    public function recordings()
    {
        return $this->hasMany('App\Recording', 'user_id');
    }
}
