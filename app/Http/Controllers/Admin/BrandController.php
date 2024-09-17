<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function create()
    {
        return view('admin.brands.create');
    }

    public function edit($id)
    {
        // Find the brand by ID
        $brand = Brand::findOrFail($id);

        // Return the edit view with the brand data
        return view('admin.brands.edit', compact('brand'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:brands,slug',
            'status' => 'required|boolean', // Added validation for status
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            // Create a new instance of Brand
            $brand = new Brand();
            $brand->name = $request->input('name');
            $brand->slug = $request->input('slug');
            $brand->status = $request->input('status');
    
            // Save the brand using the save() method
            $brand->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Brand created successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the full exception message and details
            Log::error('Error creating brand: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
    
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the brand.',
            ], 500);
        }
    }
    

    public function index(Request $request)
    {
        // Initialize the query with the Brand model
        $brands = Brand::latest();

        // Check if the keyword is not empty and apply a search filter
        if (!empty($request->get('keyword'))) {
            $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // Paginate the results with 10 items per page
        $brands = $brands->paginate(10);

        // Return the view with the paginated brands
        return view('admin.brands.brandlist', compact('brands'));
    }

    // Handle the request to update an existing brand
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:brands,slug,' . $id,
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Find the brand by ID
            $brand = Brand::findOrFail($id);
            $brand->name = $request->input('name');
            $brand->slug = $request->input('slug');
            $brand->status = $request->input('status');

            // Save the updated brand data
            $brand->save();

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the full exception message and details
            Log::error('Error updating brand: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the brand.',
            ], 500);
        }
    }

     // Handle the request to delete a brand
     public function destroy($id)
     {
         try {
             // Find the brand by ID
             $brand = Brand::findOrFail($id);
 
             // Delete the brand
             $brand->delete();
 
             // Return success response
             return response()->json([
                 'status' => true,
                 'message' => 'Brand deleted successfully!',
             ]);
         } catch (\Exception $e) {
             // Log the full exception message and details
             Log::error('Error deleting brand: ' . $e->getMessage());
             Log::error($e->getTraceAsString());
 
             // Return error response
             return response()->json([
                 'status' => false,
                 'message' => 'An error occurred while deleting the brand.',
             ], 500);
         }
     }
}
