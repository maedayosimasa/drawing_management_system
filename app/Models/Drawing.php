<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drawing extends Model
{
    //リレーション設定 drawing << project_name
    public function project_name(): BelongsTo
    {
        return $this->belongsTo(project_name::class);
    }
    //リレーション設定 drawing << design_drawing
    public function design_drawing(): HasOne
    {
        return $this->hasOne(Design_drawing::class, 'drawing_id');
    }

    //リレーション設定  drawing  << structual_daiagram
    public function structural_diagram(): HasOne
    {
        return $this->hasOne(Structural_diagram::class, 'drawing_id');
    }

    //リレーション設定  drawing  << equipment_diagram
    public function equipment_diagram(): HasOne
    {
        return $this->hasOne(Equipment_diagram::class, 'drawing_id');
    }

    //リレーション設定  drawing  << bim_drawing
    public function bim_drawing(): HasOne
    {
        return $this->hasOne(Bim_drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'project_name_id',
    ];
}
