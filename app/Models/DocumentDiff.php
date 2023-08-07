<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDiff extends Model
{
    use HasFactory;
    protected $table = 'document_diff';
    protected $fillable = ['document_user_id', 'version', 'body_diff', 'tags_diff'];

    public function documentUser()
    {
        return $this->belongsTo(DocumentUser::class);
    }
}
