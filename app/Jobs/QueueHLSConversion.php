<?php

namespace App\Jobs;

use App\Actions\ConvertToHLS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Exception;

final class QueueHLSConversion implements ShouldQueue
{
    use Dispatchable, Queueable;

    public bool $failOnTimeout = false;
    public int $timeout = 0;

    public function __construct(public Model $model) {}

    public function handle(): void
    {
        try {
            // FFmpeg & FFprobe paths
            $ffmpegPath  = env('FFMPEG_PATH', 'C:/ffmpeg/bin/ffmpeg.exe');
            $ffprobePath = env('FFPROBE_PATH', 'C:/ffmpeg/bin/ffprobe.exe');

            if (!file_exists($ffmpegPath) || !file_exists($ffprobePath)) {
                throw new Exception("FFmpeg binaries not found!");
            }

            // -----------------------------
            // Original video path (from DB)
            // -----------------------------
            $videoPathFromDB = $this->model->getVideoPath();

            Log::info('DB value for video_path', [
                'model_id' => $this->model->id,
                'video_path' => $videoPathFromDB
            ]);

            if (empty($videoPathFromDB)) {
                 $this->model->status = 'failed';
                $this->model->saveQuietly();
                Log::error("🚫 Original video path is null or empty for Model ID: {$this->model->id}");
                return;
            }

            // -----------------------------
            // Relative path for FFMpeg
            // -----------------------------
            $storagePrefix = str_replace('\\', '/', storage_path('app/public') . '/');
            $absolutePath = str_replace('\\', '/', storage_path('app/public/' . $videoPathFromDB));
            
            if (!file_exists($absolutePath)) {
                Log::error("🚫 Original video file not found", ['path' => $absolutePath]);
                return;
            }

            // Use relative path for FFMpeg
            if (str_starts_with($absolutePath, $storagePrefix)) {
                $relativePath = substr($absolutePath, strlen($storagePrefix));
            } else {
                $relativePath = $videoPathFromDB;
            }

            // -----------------------------
            // HLS output folder
            // -----------------------------
            $folderName = $this->model->getHLSRootFolderPath();
            $hlsFolder  = storage_path('app/public/hls/' . $folderName);
            if (!is_dir($hlsFolder)) mkdir($hlsFolder, 0777, true);

            $masterPlaylist = $folderName;

            Log::info("🎬 Starting HLS conversion", [
                'input'  => $absolutePath,
                'output' => $masterPlaylist,
                 'folderName' => $folderName
            ]);

            // -----------------------------
            // Run HLS conversion
            // -----------------------------
            $ffmpeg = \FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => $ffprobePath,
                'timeout'          => 3600,
                'ffmpeg.threads'   => 2,
            ]);

            ConvertToHLS::convertToHLS($relativePath, $masterPlaylist, $this->model);

            // -----------------------------
            // Save DB path (relative)
            // -----------------------------
            $this->model->setHlsPath($masterPlaylist);
            $this->model->setProgress(100);
            $this->model->status = 'ready';
            $this->model->saveQuietly();

            Log::info("✅ HLS Conversion finished successfully", [
                'db_path' => $this->model->getHlsPath()
            ]);

        } catch (Exception $e) {
                $this->model->status = 'failed';
                $this->model->saveQuietly();
            Log::error("❌ HLS Conversion failed for Model ID {$this->model->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
