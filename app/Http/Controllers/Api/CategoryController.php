<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 🔹 Get all main categories (parent_id = null) with subcategories
    public function index()
    {
        // Load parent categories with nested subcategories
    $data = Category::whereNull('parent_id')
        ->with(['subcategories.subcategories']) // Load 2 levels deep
        ->orderBy('id', 'DESC')
        ->get();
        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    // 🔹 Get subcategories for a specific category ID
    public function getSubcategories($categoryId)
    {
        $subcategories = Category::where('parent_id', $categoryId)->get();

        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }
}
