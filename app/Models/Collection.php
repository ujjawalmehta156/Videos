<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\ConvertsToHLS;
use Illuminate\Support\Str;

class Collection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, ConvertsToHls;

    protected $table = 'collections';

    /**
     * ✅ Primary Key Configuration
     */
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * ✅ Mass assignable fields
     */
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'uploader_id',
        'cat_id',
        'sub_cat_id',
        'file_format',
        'video_path',
        'hls_master_url',
        'thumbnail_url',
        'status',
        'visibility',
        'video_status',
        'views',
        'created_by',
    ];

    /**
     * ✅ Appended attributes
     */
    protected $appends = ['full_hls_master_url'];

    /**
     * ✅ Auto-create UUID when inserting
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // -----------------------------
    // ✅ Relationships
    // -----------------------------
    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id','uuid');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_cat_id','uuid');
    }

    public function meta()
    {
        return $this->hasOne(CollectionMeta::class, 'collection_id', 'uuid');
    }

    public function videoStream()
    {
        return $this->hasOne(VideoStream::class, 'collection_id', 'uuid');
    }

    public function videoStreams()
    {
        return $this->hasMany(VideoStream::class, 'collection_id', 'uuid');
    }

    // -----------------------------
    // ✅ Accessors / Mutators
    // -----------------------------
    public function getVideoPath(): ?string
    {
        return $this->video_path;
    }

    public function setVideoPath(string $relativePath): void
    {
        $this->video_path = $relativePath;
    }

    public function getHlsPath(): ?string
    {
        return $this->hls_master_url;
    }

    public function setHlsPath(string $relativePath): void
    {
        $this->hls_master_url = $relativePath;
    }

    public function getHLSRootFolderPath(): string
    {
        return (string) Str::uuid();
    }
public function getKeyType()
{
    return 'string';
}

public function getIncrementing()
{
    return false;
}

    /**
     * ✅ Full HLS URL accessor
     */
    public function getFullHlsMasterUrlAttribute()
    {
        if (!empty($this->hls_master_url)) {
            return $this->hls_master_url . '/hls/playlist.m3u8';
        }
        return null;
    }
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
}
