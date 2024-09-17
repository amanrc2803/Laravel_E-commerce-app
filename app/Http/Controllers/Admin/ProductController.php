<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product; // Import the Product model
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all(); // Fetch all products
        return view('admin.products.index', compact('products')); // Return the index view with products data
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all(); // Fetch all categories
    $subCategories = SubCategory::all(); // Fetch all sub-categories
    $brands = Brand::all(); // Fetch all brands
        return view('admin.products.create',compact('categories', 'subCategories', 'brands')); // Return the create view
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|between:0,99999999.99',
            'compare_price' => 'nullable|numeric|between:0,99999999.99',
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:sub_categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
            'is_featured' => 'required|in:Yes,No',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'required|in:Yes,No',
            'qty' => 'nullable|integer|min:0',
            'status' => 'required|integer|in:0,1', // assuming status is either active (1) or blocked (0)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create a new instance of Product
            $product = new Product();
            $product->title = $request->input('title');
            $product->slug = $request->input('slug');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->compare_price = $request->input('compare_price');
            $product->category_id = $request->input('category_id');
            $product->sub_category_id = $request->input('sub_category_id');
            $product->brand_id = $request->input('brand_id');
            $product->is_featured = $request->input('is_featured');
            $product->sku = $request->input('sku');
            $product->barcode = $request->input('barcode');
            $product->track_qty = $request->input('track_qty');
            $product->qty = $request->input('qty');
            $product->status = $request->input('status');

            // Save the product using the save() method
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the full exception message and details
            Log::error('Error creating product: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the product.',
            ], 500);
        }
    }
    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id); // Fetch the product by ID
        return view('admin.products.edit', compact('product')); // Return the edit view with product data
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'is_featured' => 'required|in:Yes,No',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'required|in:Yes,No',
            'qty' => 'nullable|integer|min:0',
            'status' => 'required|integer|in:0,1',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all()); // Update the product

        return redirect()->route('products.index')->with('success', 'Product updated successfully.'); // Redirect with success message
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Delete the product

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.'); // Redirect with success message
    }
}
