<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private function prefix()
    {
        return auth()->user()->hasRole('super-admin') ? 'super-admin' : 'admin';
    }

    public function index()
    {
        $data = Category::whereNull('parent_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.category.index', compact('data'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.category.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        // Duplicate check
        $exists = Category::where('name', $request->name)
            ->where('parent_id', $request->parent_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This category or subcategory already exists.');
        }

        // Slug generator
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        // Insert category
        Category::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => $slug,
            'created_by' => auth()->id(),
            'status' => 'active',
        ]);

        return redirect()
            ->route($this->prefix() . '.category.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit($uuid)
    {
        $data = Category::where('uuid', decrypt($uuid))->firstOrFail();

        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')->where('uuid', '!=', decrypt($uuid)) 
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.category.edit', compact('data', 'categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);
        // Duplicate check
        $exists = Category::where('name', $request->name)
            ->where('parent_id', $request->parent_id)
            ->where('uuid', '!=', $request->uuid)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Another category or subcategory with this name already exists.');
        }

        // Slug generator
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 1;
        while (Category::where('slug', $slug)
            ->where('uuid', '!=', $request->id)
            ->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        // Update category
        Category::where('uuid', $request->id)->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => $slug,
            'status' => $request->status,
        ]);

        return redirect()
            ->route($this->prefix() . '.category.index')
            ->with('info', 'Category updated successfully.');
    }

    public function destroy($uuid)
    {
        Category::where('uuid', decrypt($uuid))->delete();

        return redirect()
            ->route($this->prefix() . '.category.index')
            ->with('error', 'Category deleted successfully.');
    }

    public function getSubcategories($uuid)
    {
        $subcategories = Category::where('parent_id', $uuid)
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json($subcategories);
    }
}
