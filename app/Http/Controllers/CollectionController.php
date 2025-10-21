<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Jobs\QueueHLSConversion;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CollectionMeta;
use App\Models\VideoStream;
  use getID3;
use Illuminate\Support\Facades\Auth;
class CollectionController extends Controller
{
    protected $imageService;

public function __construct(ImageService $imageService)
{
    $this->imageService = $imageService;

    $this->middleware(function ($request, $next) {
        if (Auth::user()->hasRole('super-admin')) {
            $categories = Category::where('status','active')->whereNull('parent_id')->get();
        } else {
            $categories = Category::where('status','active')->where('created_by', Auth::id())
                                  ->whereNull('parent_id')->get();
        }

        view()->share('category', $categories);

        return $next($request);
    });
}

    private function prefix()
    {
        return auth()->user()->hasRole('super-admin') ? 'super-admin' : 'admin';
    }

    public function index()
    {
        if (Auth::user()->hasRole('super-admin')) {
            $data = Collection::orderBy('id', 'DESC')->get();
        }else{
            $data = Collection::where('created_by', Auth::id())->orderBy('id', 'DESC')->get();
        }
        return view('admin.collection.index', compact('data'));
    }

    public function create()
    {
        return view('admin.collection.create');
    }


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
        'category' => 'required',
        'subcategory' => 'nullable',
        'video' => 'required|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-matroska,video/x-flv,video/webm,video/3gpp',
        'meta_title' => 'nullable|string',
        'meta_keywords' => 'nullable|string',
        'meta_description' => 'nullable|string',
    ]);

    $collection = Collection::create([
        'title' => $request->name,
        'cat_id' => $request->category,
        'sub_cat_id' => $request->subcategory,
        'uuid' => Str::uuid(),
        'created_by' => auth()->id(),
        'uploader_id' => auth()->id(),
    ]);

   if ($request->hasFile('video')) {
        $mediaItem = $collection->addMediaFromRequest('video')
                                ->toMediaCollection('videos');

        // Absolute path ko normalize karo (forward slash)
        $absolutePath = str_replace('\\', '/', $mediaItem->getPath());

        // Relative path safe
        $storagePrefix = str_replace('\\','/', storage_path('app/public')) . '/';
        if (str_starts_with($absolutePath, $storagePrefix)) {
            $relativePath = substr($absolutePath, strlen($storagePrefix));
        } else {
            $relativePath = $absolutePath; // fallback
        }

        $collection->getProgressColumn();
        $collection->setVideoPath($relativePath);
        $collection->save();

        // Queue me HLS conversion job dispatch karo
        QueueHLSConversion::dispatch($collection);

        // Metadata extract (absolute path se)
        $fileSizeMb = round(filesize($absolutePath)/1024/1024, 2);
        $getID3 = new \getID3;
        $videoInfo = $getID3->analyze($absolutePath);

        $resolution = 'unknown';    
        $bitrate_kbps = 0;
        $codec = 'unknown';

        if (!empty($videoInfo['video'])) {
            $resolution = ($videoInfo['video']['resolution_x'] ?? 'unknown') . 'x' . ($videoInfo['video']['resolution_y'] ?? 'unknown');
            $bitrate_kbps = isset($videoInfo['bitrate']) ? intval($videoInfo['bitrate']) / 1000 : 0;
            $codec = $videoInfo['video']['codec'] ?? 'unknown';
        }

        VideoStream::create([
            'collection_id' => $collection->id,
            'resolution' => $resolution,
            'bitrate_kbps' => $bitrate_kbps,
            'codec' => $codec,
            'file_size_mb' => $fileSizeMb,
            'hls_url' => null,
        ]);
    }


    // Meta save करो
    CollectionMeta::create([
        'collection_id' => $collection->id,
        'meta_title' => $request->meta_title,
        'meta_keywords' => $request->meta_keywords,
        'meta_description' => $request->meta_description,
    ]);

    return redirect()->route($this->prefix() . '.collection.index')
        ->with('success', 'Collection created successfully. HLS conversion will run in background.');
}



    public function edit($id)
    {
        $collection = Collection::where('id', decrypt($id))->first();
        $meta = $collection->meta;
        $videoStream = $collection->videoStream;
        return view('admin.collection.edit', compact('collection', 'meta'));
    }

public function update(Request $request)
{
    $request->validate([
        'edit_id' => 'required|exists:collections,id',
        'name' => 'required|max:255',
        'category' => 'required',
        'subcategory' => 'nullable',
        'video' => 'nullable|mimetypes:video/mp4,video/mpeg,video/quicktime',
        'meta_title' => 'nullable|string',
        'meta_keywords' => 'nullable|string',
        'meta_description' => 'nullable|string',
    ]);

    $collection = Collection::find($request->edit_id);
    $collection->title = $request->name;
    $collection->sub_cat_id = $request->subcategory;
    $collection->cat_id = $request->category;
     $collection->video_status = $request->video_status;
    $collection->save();

    // Video handling
    if ($request->hasFile('video')) {
        // Remove old video (optional)
        $collection->clearMediaCollection('videos');

        // Add new video
        $mediaItem = $collection->addMediaFromRequest('video')->toMediaCollection('videos');

        // Absolute path normalize (forward slash)
        $absolutePath = str_replace('\\', '/', $mediaItem->getPath());

        // Relative path safe
        $storagePrefix = str_replace('\\','/', storage_path('app/public')) . '/';
        if (str_starts_with($absolutePath, $storagePrefix)) {
            $relativePath = substr($absolutePath, strlen($storagePrefix));
        } else {
            $relativePath = $absolutePath; // fallback
        }

        $collection->setVideoPath($relativePath);
        $collection->save();

        // Queue HLS conversion
        QueueHLSConversion::dispatch($collection);

        // Metadata extract
        $getID3 = new \getID3;
        $videoInfo = $getID3->analyze($absolutePath);
        $fileSizeMb = round(filesize($absolutePath)/1024/1024, 2);

        $resolution = 'unknown';
        $bitrate_kbps = 0;
        $codec = 'unknown';

        if (!empty($videoInfo['video'])) {
            $resolution = ($videoInfo['video']['resolution_x'] ?? 'unknown') . 'x' . ($videoInfo['video']['resolution_y'] ?? 'unknown');
            $bitrate_kbps = isset($videoInfo['bitrate']) ? intval($videoInfo['bitrate']) / 1000 : 0;
            $codec = $videoInfo['video']['codec'] ?? 'unknown';
        }

        VideoStream::updateOrCreate(
            ['collection_id' => $collection->id],
            [
                'resolution' => $resolution,
                'bitrate_kbps' => $bitrate_kbps,
                'codec' => $codec,
                'file_size_mb' => $fileSizeMb,
                'hls_url' => null, // HLS conversion ke baad update karna
            ]
        );
           // Meta update
    CollectionMeta::updateOrCreate(
        ['collection_id' => $collection->id],
        [
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]
    );

    }

 
    return redirect()->route($this->prefix() . '.collection.index')
        ->with('info', 'Collection updated successfully. HLS conversion running in background.');
}




    public function destroy($id)
    {
        $collection = Collection::where('id', decrypt($id))->first();

        $oldImage = $collection->image;
        $image_path = public_path('collection-image/' . $oldImage);
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $collection->delete();
        return redirect()->route($this->prefix() .'.collection.index')->with('error', 'Collection deleted successfully.');
    }
}
