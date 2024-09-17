<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SubCategoryController extends Controller
{
    // Display the create form with available categories
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('admin.sub_category.create', ['categories' => $categories]);
    }

    // Handle the request to store a new sub-category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:sub_categories,slug',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create a new instance of SubCategory
            $subCategory = new SubCategory();
            $subCategory->name = $request->input('name');
            $subCategory->slug = $request->input('slug');
            $subCategory->category_id = $request->input('category_id');
            $subCategory->status = $request->input('status');

            // Save the sub-category using the save() method
            $subCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'Sub-category created successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the error message for debugging purposes

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the sub-category.',
            ], 500);
        }
    }

    public function index(Request $request)
{
    // Initialize the query with the Category model
    $categories = SubCategory::latest();

    // Check if the keyword is not empty and apply a search filter
    if (!empty($request->get('keyword'))) {
        $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
    }

    // Paginate the results with 10 items per page
    $categories = $categories->paginate(10);

    // Return the view with the paginated categories
    return view('admin.sub_category.sublist', compact('categories'));
}


public function edit($id)
{
    $subcategory = SubCategory::findOrFail($id);
    $categories = Category::all(); // Fetch all categories

    return view('admin.sub_category.subedit', compact('subcategory', 'categories'));
}


}
