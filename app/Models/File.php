<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    // テーブル名（デフォルトは 'files' だが、もし異なる場合は指定）
    protected $table = 'files';

    // マスアサインメント可能なカラムを指定
    protected $fillable = [
        'project_name',
        'finishing_table_name',
        'floor_plan_name',
        'machinery_equipment_diagram_all_name',
        'bim_drawing_name',
        'meeting_log_name',
    ];
}
