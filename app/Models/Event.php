<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Menggunakan event_id sebagai primary key
    protected $primaryKey = 'event_id';
    public $incrementing = true;
    protected $keyType = 'int';


    public function getRouteKeyName()
    {
        return 'event_id';
    }

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'category',
    ];
}
