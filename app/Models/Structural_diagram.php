<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structural_diagram extends Model
{
    //リレーション設定 structural_diagram  << drawing
    public function drawing()
    {
        return $this->belongsTo(Drawing::class, 'drawing_id');
    }


    use HasFactory;

    protected $fillable = [
        'drawing_id',
        'floor_plan_name',
    ];
}
