<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Symfony\Component\CssSelector\Node\HashNode;

class project_name extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_name',
        'address',
        'client',
        'construction_period_start',
        'construction_period_end',
        'completion_date',
        'constract_amount',
        'use',
        'site_area',
        'building_area',
        'total_floor_area',
        'strural',
        'floor_number_underground',
        'floor_number_ground',
    ];

    //リレーション設定
    // public function User() {
    //     return $this->belongsTo(User::class);
    // }


    //リレーション設定project_name << drawing
        public function drawing():HasOne
    {
        return $this->hasOne(Drawing::class);
    }

    //リレーション設定project_name << meeting_log
    public function meeting_log()
    {
        return $this->hasOne(Meeting_log::class, 'project_id');
    }
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }

}
