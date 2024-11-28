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
    ];
}
