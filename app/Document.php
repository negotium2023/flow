<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $touches = ['client'];

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    public function type()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    public function size()
    {
        try {
            $bytes = (!File::exists(public_path('storage/documents/processed_applications/'.$this->file)) ? Storage::size('documents/' . $this->file) : File::exists(public_path('storage/documents/processed_applications/'.$this->file)));
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), [0, 0, 2, 2, 3][$i]) . ['B', 'kB', 'MB', 'GB', 'TB'][$i];
        } catch (\Exception $e) {
            return "LStat";
        }
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
