<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoStream extends Model
{
    use HasFactory;

    protected $table = 'video_streams';
    protected $fillable = [
        'collection_id',
        'resolution',
        'bitrate_kbps',
        'codec',
        'hls_url',
        'file_size_mb',
    ];

    // Relationship with Collection
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
