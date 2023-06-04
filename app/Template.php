<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function type()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function type2()
    {
        return pathinfo($this->file2, PATHINFO_EXTENSION);
    }

    public function size()
    {
        try {
            $bytes = Storage::size('templates/' . $this->file);
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), [0, 0, 2, 2, 3][$i]) . ['B', 'kB', 'MB', 'GB', 'TB'][$i];
        } catch (\Exception $e) {
            return "LStat";
        }
    }
}
