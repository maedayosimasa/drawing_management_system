<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Meeting_log extends Model
{
    //リレーション設定 bim_drawing << meeting_log
    public function drawing(): BelongsTo
    {
        return $this->belongsTo(project_name::class, 'project_id');
    }

    use HasFactory;
    protected $fillable = [
        'project_name_id',
        'meeting_log_name',
        'meeting_log_view_path',
        'meeting_log_pdf_path',
        'delivery_documents_name',
        'delivery_documents_view_path',
        'delivery_documents_pdf_path',
        'bidding_documents_name',
        'bidding_documents_view_path',
        'bidding_documents_pdf_path',
        'archived_photo_name',
        'archived_photo_view_path',
        'archived_photo_pdf_path',
        'contract_name',
        'contract_view_path',
        'contract_pdf_path',
        'management_documents_name',
        'management_documents_view_path',
        'management_documents_pdf_path',
    ];
    public function scopeWithViewPath($query)
    {
        return $query->where('name', 'like', '%_view_path');
    }
}
