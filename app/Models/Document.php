<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'current_version', 'status'];

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function documentUsers()
    {
        return $this->hasMany(DocumentUser::class);
    }
}
