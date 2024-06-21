<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:5|max:100',
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => "Categoria creada exitosamente!",
            'category' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category, 200);
        } else {
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if ($category) {
            $request->validate([
                'name' => 'required|string|min:5|max:100',
            ]);

            $category->update($request->all());

            return response()->json($category, 200);
        } else {
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if ($category) {
            $categoryName = $category->name;
            $category->delete();
            return response()->json([
                'message' => "Categoria '{$categoryName}' eliminada con éxito."], 200);
        } else {
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        }
    }
}
