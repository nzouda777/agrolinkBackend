<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::with('translations')->get();
    }

    public function show($id)
    {
        return Category::with('translations')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $category = Category::create($request->all());

        DB::transaction(function () use ($request, $category) {

            if ($request->has('translations')) {
                foreach ($request->translations as $translation) {
                    $category->translations()->create([
                        'locale' => $translation['locale'],
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        });

        return response()->json($category->load('translations'), 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($category, $request) {
            $category->update($request->all());

            if ($request->has('translations')) {
                $category->translations()->delete();
                foreach ($request->translations as $translation) {
                    $category->translations()->create([
                        'locale' => $translation['locale'],
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        });

        return response()->json($category->load('translations'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->noContent();
    }
}
