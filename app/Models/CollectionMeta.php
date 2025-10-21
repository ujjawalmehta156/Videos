<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionMeta extends Model
{
    use HasFactory;

    protected $table = 'collection_meta';
    protected $fillable = [
        'collection_id', 'meta_title', 'meta_keywords', 'meta_description'
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
