<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Structural_diagram extends Model
{
    //リレーション設定 structural_diagram  << drawing
     public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'structural_floor_plan_name',
        'structural_floor_plan_view_path',
        'structural_floor_plan_pdf_path',
        'structural_elevation_name',
        'structural_elevation_view_path',
        'structural_elevation_pdf_path',
        'structural_sectional_name',
        'structural_sectional_view_path',
        'structural_sectional_pdf_path',
        'structural_frame_diagram_name',
        'structural_frame_diagram_view_path',
        'structural_frame_diagram_pdf_path',
        'structural_diagram_all_name',
        'structural_diagram_all_view_path',
        'structural_diagram_all_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
