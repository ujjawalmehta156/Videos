<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Detect route/view prefix based on user role.
     */
    private function prefix()
    {
        return auth()->user()->hasRole('super-admin') ? 'super-admin' : 'admin';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::whereNull('parent_id') // sirf main categories
            ->orderBy('id', 'DESC')
            ->get();
    
        return view('admin.category.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where(["status"=>'active'])->whereNull('parent_id')->orderBy('id', 'DESC')->get(); 
        return view('admin.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
    ]);

    // Check if the category already exists
    $exists = Category::where('name', $request->name)
                ->where('parent_id', $request->parent_id) 
                ->exists();

    if ($exists) {
        return redirect()
            ->back()
            ->with('error', 'This category or subcategory already exists.');
    }

    // Generate unique slug
    $baseSlug = Str::slug($request->name);
    $uniqueSlug = $baseSlug;
    $counter = 1;

    while (Category::where('slug', $uniqueSlug)->exists()) {
        $uniqueSlug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Insert category
    Category::create([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
        'uuid' => Str::uuid(),
        'created_by' => auth()->id(),
        'slug' => $uniqueSlug,
    ]);

    return redirect()
        ->route($this->prefix() . '.category.index')
        ->with('success', 'Category created successfully.');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($category)
    {
        $data = Category::where('id', decrypt($category))->first();
        $categories = Category::where(["status"=>'active'])->whereNull('parent_id')->orderBy('id', 'DESC')->get();
        return view('admin.category.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
    ]);

    // Check if another category with the same name and parent exists
    $exists = Category::where('name', $request->name)
                ->where('parent_id', $request->parent_id)
                ->where('id', '!=', $request->id) // exclude current
                ->exists();

    if ($exists) {
        return redirect()
            ->back()
            ->with('error', 'Another category or subcategory with this name already exists.');
    }

    // Generate unique slug
    $baseSlug = Str::slug($request->name);
    $uniqueSlug = $baseSlug;
    $counter = 1;

    while (Category::where('slug', $uniqueSlug)
        ->where('id', '!=', $request->id)
        ->exists()) {
        $uniqueSlug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Update the category
    Category::where('id', $request->id)->update([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
        'slug' => $uniqueSlug,
        'status' => $request->status,
    ]);

    return redirect()
        ->route($this->prefix() . '.category.index')
        ->with('info', 'Category updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Category::where('id', decrypt($id))->delete();

        return redirect()
            ->route($this->prefix() . '.category.index')
            ->with('error', 'Category deleted successfully.');
    }

    /**
     * Get all subcategories of a specific category.
     */
    public function getSubcategories($id)
    {
        $subcategories = Category::where('parent_id', $id)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json($subcategories);
    }
}
