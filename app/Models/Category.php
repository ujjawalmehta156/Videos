<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'parent_id', 'status','created_by','uuid'
    ];

    // Children relationship (subcategories)
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Parent relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Recursive: All nested children
    public function allSubcategories()
    {
        return $this->subcategories()->with('allSubcategories');
    }
}