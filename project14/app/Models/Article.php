<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public function articleType()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
