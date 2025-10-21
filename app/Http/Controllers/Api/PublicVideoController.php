<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CollectionResource;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class PublicVideoController extends Controller
{
    /**
     * API 1: Get all categories & subcategories
     * GET /api/categories?include_videos=false
     */
    public function getCategories(Request $request)
    {
        $includeVideos = $request->boolean('include_videos', false);

        $categories = Category::whereNull('parent_id')
            ->with('subcategories')
            ->when($includeVideos, function ($query) {
                $query->withCount(['collections' => function ($q) {
                    $q->where('visibility', 'public')->where('status', 'active');
                }]);
            })
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * API 2: Get single category details + subcategories
     * GET /api/categories/{uuid}?include_videos=true
     */
    public function getCategoryDetails($uuid, Request $request)
    {
        $includeVideos = $request->boolean('include_videos', false);

        $category = Category::with('subcategories')
            ->when($includeVideos, function ($query) {
                $query->with(['collections' => function ($q) {
                    $q->where('visibility', 'public')
                        ->where('status', 'active')
                        ->with(['meta', 'videoStreams'])
                        ->latest()
                        ->limit(10);
                }]);
            })
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Category details retrieved successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * API 3: Get all videos under a category
     * GET /api/categories/{uuid}/videos?page=1&limit=20&sort=latest
     */
    public function getCategoryVideos($uuid, Request $request)
    {
        $limit = $request->get('limit', 20);
        $sort = $request->get('sort', 'latest');

        $category = Category::where(['uuid' => $uuid, 'status' => 'active'])->firstOrFail();

        $query = Collection::where('cat_id', $category->id)
            ->where('visibility', 'public')
            ->where('video_status', 'active')
            ->with(['meta', 'videoStreams', 'category', 'subcategory']);

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
        }

        $videos = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'Videos retrieved successfully',
            'meta' => [
                'current_page' => $videos->currentPage(),
                'total_pages' => $videos->lastPage(),
                'per_page' => (int)$limit,
                'total_videos' => $videos->total(),
                'from' => $videos->firstItem(),
                'to' => $videos->lastItem(),
            ],
            'data' => CollectionResource::collection($videos)
        ]);
    }

    /**
     * API 4: Get all public videos
     * GET /api/videos?category_id=1&subcategory_id=2&sort=latest&page=1&limit=20
     */
    public function getAllVideos(Request $request)
    {
        $limit = $request->get('limit', 20);
        $sort = $request->get('sort', 'latest');

        $query = Collection::where('visibility', 'public')
            ->with(['meta', 'videoStreams', 'category', 'subcategory']);

        if ($request->filled('category_id')) {
            $query->where('cat_id', $request->category_id);
        }

        if ($request->filled('subcategory_id')) {
            $query->where('sub_cat_id', $request->subcategory_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhereHas('meta', function ($metaQuery) use ($search) {
                        $metaQuery->where('meta_title', 'like', "%$search%")
                            ->orWhere('meta_description', 'like', "%$search%");
                    });
            });
        }

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
        }

        $videos = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'Videos retrieved successfully',
            'meta' => [
                'current_page' => $videos->currentPage(),
                'total_pages' => $videos->lastPage(),
                'per_page' => (int)$limit,
                'total_videos' => $videos->total(),
                'from' => $videos->firstItem(),
                'to' => $videos->lastItem(),
            ],
            'data' => CollectionResource::collection($videos)
        ]);
    }

    /**
     * API 5: Get single video details + HLS URLs
     * GET /api/videos/{uuid}
     */
    public function getVideoDetails($uuid)
    {
        $video = Collection::where('uuid', $uuid)
            ->where('visibility', 'public')
            ->where('video_status', 'active')
            ->with(['meta', 'videoStreams', 'category', 'subcategory'])
            ->first();

        if (!$video) {
            return response()->json([
                'status' => false,
                'message' => 'Video not found',
            ], 404);
        }

        $video->increment('views');

        return response()->json([
            'status' => true,
            'message' => 'Video details retrieved successfully',
            'data' => new CollectionResource($video)
        ]);
    }
}
