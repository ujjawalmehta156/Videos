<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\QueueHLSConversion;
use Illuminate\Support\Facades\Log;
use Exception;

trait ConvertsToHLS
{
    /**
     * Boot the converts to HLS trait for a model.
     */
        public static function bootConvertsToHLS(): void
    {
        static::created(function ($model) {
            try {
                QueueHLSConversion::dispatch($model);
                Log::info("🎬 Custom HLS Job Dispatched for Model ID: {$model->id}");
            } catch (Exception $e) {
                Log::error("❌ HLS Dispatch Error for Model ID {$model->id}: " . $e->getMessage());
            }
        });
    }

    // ------------------------
    // Video Path Handling
    // ------------------------
  public function getVideoPath(): ?string
{
    $relativePath = $this->{$this->getVideoColumn()} ?? null;
    if (!$relativePath) return null;

    // Storage path के अंदर file का absolute path
    return storage_path('app/public/' . $relativePath);
}


    public function setVideoPath(?string $path = null): void
    {
        $this->{$this->getVideoColumn()} = $path;
    }

    public function getHlsPath(): ?string
    {
        return $this->{$this->getHlsColumn()} ?? null;
    }

    public function setHlsPath(?string $path = null): void
    {
        $this->{$this->getHlsColumn()} = $path;
    }

    public function getProgress(): int
    {
        return (int) ($this->{$this->getProgressColumn()} ?? 0);
    }

    public function setProgress(int $progress = 0): void
    {
        $this->{$this->getProgressColumn()} = $progress;
    }

    public function getVideoColumn(): string
    {
        return property_exists($this, 'videoColumn') ? $this->videoColumn : config('hls.video_column', 'video_path');
    }

    public function getHlsColumn(): string
    {
        return property_exists($this, 'hlsColumn') ? $this->hlsColumn : config('hls.hls_column', 'hls_path');
    }

    public function getProgressColumn(): string
    {
        return property_exists($this, 'progressColumn') ? $this->progressColumn : config('hls.progress_column', 'conversion_progress');
    }

    public function getVideoDisk(): string
    {
        return property_exists($this, 'videoDisk') ? $this->videoDisk : config('hls.video_disk', 'public');
    }

    public function getHlsDisk(): string
    {
        return property_exists($this, 'hlsDisk') ? $this->hlsDisk : config('hls.hls_disk', 'public');
    }

    public function getSecretsDisk(): string
    {
        return property_exists($this, 'secretsDisk') ? $this->secretsDisk : config('hls.secrets_disk', 'public');
    }

    public function getHLSOutputPath(): string
    {
        return property_exists($this, 'hlsOutputPath') ? $this->hlsOutputPath : config('hls.hls_output_path', 'hls');
    }

    public function getHLSSecretsOutputPath(): string
    {
        return property_exists($this, 'hlsSecretsOutputPath') ? $this->hlsSecretsOutputPath : config('hls.secrets_output_path', 'secrets');
    }

    public function getTempStorageOutputPath(): string
    {
        return property_exists($this, 'tempStorageOutputPath') ? $this->tempStorageOutputPath : config('hls.temp_storage_path', 'tmp');
    }

    public function getHLSResolutions(): array
    {
        return property_exists($this, 'hlsResolutions') ? $this->hlsResolutions : config('hls.resolutions');
    }

    public function getHLSRootFolderPath(): string
    {
        return uuid_create();
    }
}
