<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Base query
        $query = Collection::with(['meta', 'videoStreams']);

        // 🔹 Apply filters dynamically
        if ($request->filled('cat_id')) {
            $query->where('cat_id', $request->cat_id);
        }

        if ($request->filled('sub_cat_id')) {
            $query->where('sub_cat_id', $request->sub_cat_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('meta', function ($metaQuery) use ($search) {
                      $metaQuery->where('meta_title', 'like', "%$search%");
                  });
            });
        }

        // 🔹 Pagination (default 10)
        $perPage = $request->get('per_page', 10);
        $collections =$query->latest()->paginate($perPage);

        // 🔹 Add meta info
        $meta = [
            'timestamp' => now()->toDateTimeString(),
            'total_collections' => $collections->total(),
            'per_page' => (int)$perPage,
            'current_page' => $collections->currentPage(),
            'total_pages' => $collections->lastPage(),
        ];

        return response()->json([
            'status' => true,
            'meta' => $meta,
            'collections' => $collections->items(),
        ]);
    }
}
