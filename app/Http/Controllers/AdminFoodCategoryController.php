<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminFoodCategoryController extends Controller
{
    public function index()
    {
        $categories = FoodCategory::withCount('listings')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('food_categories', 'name'),
            ],
        ]);

        FoodCategory::create([
            'name' => trim($data['name']),
            'is_active' => true,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, FoodCategory $category)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['activate', 'deactivate'])],
        ]);

        $category->update([
            'is_active' => $data['action'] === 'activate',
        ]);

        $message = $data['action'] === 'activate'
            ? 'Category activated.'
            : 'Category deactivated. It will not appear on new listings.';

        return back()->with('success', $message);
    }
}
