<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserTask extends Model
{
    use SoftDeletes;

    public function client(){
        return $this->belongsTo(Client::class, 'client_id')
            ->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'));
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')
            ->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'));
    }

    public function client_name($client_id){

        $name = '';

        $q = DB::select("select b.data as 'name' from form_input_texts a inner join form_input_text_data b on b.form_input_text_id = a.id where b.client_id = '" . $client_id . "' and a.id = '720' order by b.id desc limit 1");

        foreach ($q as $r){
            $name =  $r->name;
        }

        return $name;
    }
}
