<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;
    protected $fillable = ['document_id', 'version', 'body_content', 'tags_content'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
