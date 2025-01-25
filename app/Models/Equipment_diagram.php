<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment_diagram extends Model
{
    //リレーション設定 equipment_diagram << drawing
    public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'machinery_equipment_diagram_all_name',
        'machinery_equipment_diagram_all_view_path',
        'machinery_equipment_diagram_all_pdf_path',
        'electrical_equipment_diagram_all_name',
        'electrical_equipment_diagram_all_view_path',
        'electrical_equipment_diagram_all_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
