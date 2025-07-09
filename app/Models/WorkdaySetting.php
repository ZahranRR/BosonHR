<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkdaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'effective_days', // Menyimpan hari kerja dalam format JSON
        'monthly_workdays' // Menyimpan jumlah total hari kerja efektif per bulan
    ];

    /**
     * Akses langsung daftar hari kerja efektif sebagai array.
     * 
     * @return array
     */
    public function getEffectiveDaysAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set daftar hari kerja efektif dalam bentuk JSON.
     * 
     * @param array $value
     */
    public function setEffectiveDaysAttribute($value)
    {
        $this->attributes['effective_days'] = json_encode($value);
    }

    
}
