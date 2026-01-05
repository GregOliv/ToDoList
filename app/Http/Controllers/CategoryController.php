<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for the authenticated user.
     */
    public function index(Request $request)
    {
        $categories = $request->user()->categories()->orderBy('name')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7', // Hex color
        ]);

        $category = $request->user()->categories()->create($validated);

        return response()->json($category, 201);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, Category $category)
    {
        if ($request->user()->id !== $category->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200);
    }
}
