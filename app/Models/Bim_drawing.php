<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bim_drawing extends Model
{

    //リレーション設定 bim_drawing << drawing
    public function drawing()
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'bim_drawing_name',
        'bim_drawing_view_path',
        'bim_drawing_pdf_path',
        'meeting_log_view_path',
        'meeting_log_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
