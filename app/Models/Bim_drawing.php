<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bim_drawing extends Model
{

    //リレーション設定 bim_drawing << drawing
    public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'bim_drawing_name',
        'bim_drawing_view_path',
        'bim_drawing_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
