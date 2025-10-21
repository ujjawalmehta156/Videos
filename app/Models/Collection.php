<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\ConvertsToHLS;

class Collection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, ConvertsToHls;

    protected $table = 'collections';

    protected $fillable = [
        'title',
        'description',
        'uploader_id',
        'cat_id',
        'sub_cat_id',
        'file_format',
        'hls_master_url',
        'thumbnail_url',
        'status',
        'visibility',
        'created_by',
        'uuid',
        'video_path', // ✅ relative path ke liye
    ];
protected $appends = ['full_hls_master_url'];

    // -----------------------------
    // Relationships
    // -----------------------------
    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }
    public function SubCategory()
    {
        return $this->belongsTo(Category::class, 'sub_cat_id');
    }

    public function meta()
    {
        return $this->hasOne(CollectionMeta::class, 'collection_id');
    }

    public function videoStream()
    {
        return $this->hasOne(VideoStream::class, 'collection_id');
    }

    // -----------------------------
    // Video Path Accessors
    // -----------------------------
    public function getVideoPath(): ?string
    {
        return $this->video_path; // ✅ DB me relative path hi store
    }

    public function setVideoPath(string $relativePath): void
    {
        $this->video_path = $relativePath; // ✅ sirf relative path
    }

    // HLS folder ke liye unique folder name
    public function getHLSRootFolderPath(): string
    {
        return (string) \Str::uuid(); // har conversion ke liye unique folder
    }

    public function getHlsPath(): ?string
    {
        return $this->hls_master_url;
    }

    public function setHlsPath(string $relativePath): void
    {
        $this->hls_master_url = $relativePath;
    }

    // ✅ 2. Video streams relationship
    public function videoStreams()
    {
        return $this->hasMany(VideoStream::class, 'collection_id');
    }
    public function getFullHlsMasterUrlAttribute()
{
    if (!empty($this->hls_master_url)) {
        return $this->hls_master_url . '/hls/playlist.m3u8';
    }
    return null;
}
}
