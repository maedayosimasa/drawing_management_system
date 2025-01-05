<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Design_drawing extends Model
{
    //リレーション設定 design_drawing << drawing
    public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }

    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'finishing_table_name',
        'finishing_table_view_path',
        'finishing_table_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
