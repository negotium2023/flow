<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClientForm extends Model
{
    protected $table = 'client_forms';
    protected $touches = ['client'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function type()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function size()
    {
        try {
            $bytes = Storage::size('crf/' . $this->file);
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), [0, 0, 2, 2, 3][$i]) . ['B', 'kB', 'MB', 'GB', 'TB'][$i];
        } catch (\Exception $e) {
            return "LStat";
        }
    }

    public function client()
    {
        return $this->belongsTo('App\Client','client_id');
    }
}
