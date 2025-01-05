<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting_log extends Model
{
    //リレーション設定 bim_drawing << meeting_log
    public function project_name()
    {
        return $this->belongsTo(project_name::class, 'project_id');
    }

    use HasFactory;
    protected $fillable = [
        'project_name_id',
        'meeting_log_name',
        'meeting_log_view_path',
        'meeting_log_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
