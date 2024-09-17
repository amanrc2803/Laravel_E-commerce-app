<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use flash;
use Intervention\Image\ImageManager;
//use Intervention\Image\Laravel as image;
use Intervention\Image\Drivers\Imagick\Driver;
//use Intervention\Image\ImageManagerStatic as Image;

class CategoryController extends Controller
{

    public function index(Request $request)
{
    // Initialize the query with the Category model
    $categories = Category::latest();

    // Check if the keyword is not empty and apply a search filter
    if (!empty($request->get('keyword'))) {
        $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
    }

    // Paginate the results with 10 items per page
    $categories = $categories->paginate(10);

    // Return the view with the paginated categories
    return view('admin.category.list', compact('categories'));
}

    public function create()
    {
        return view('admin.category.create');
    }
   

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'status' => 'required|boolean',
            'image_id' => 'nullable|exists:temp_images,id', // Validate that image_id exists in TempImage table
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Save the category if validation passes
        $category = new Category();
        $category->name = $request->input('name');
        $category->slug = $request->input('slug');
        $category->status = $request->input('status');
        //$manager = new ImageManager(['driver' => 'gd']);
        $manager = new ImageManager(new Driver());

    
        // Handle image if image_id is provided
        if ($request->filled('image_id')) {
            $tempImage = TempImage::find($request->input('image_id'));
    
            if ($tempImage) {
                $ext = pathinfo($tempImage->name, PATHINFO_EXTENSION);
                $newImageName = $category->id . '.' . $ext;
                $sourcePath = public_path('temp/' . $tempImage->name);
                $destinationPath = public_path('uploads/category/' . $newImageName);
    
                // Ensure the directory exists
                if (!File::exists(public_path('uploads/category'))) {
                    File::makeDirectory(public_path('uploads/category'), 0755, true);
                }
    
                // Check if the source file exists and copy it
                if (File::exists($sourcePath)) {
                    // Move the original image
                    File::move($sourcePath, $destinationPath);
    
                    // Resize and save the thumbnail
                    $thumbnailPath = public_path('uploads/category/thumb_' . $newImageName);
    
                    // Initialize the Image Manager with GD or Imagick
                    
                    
                    $img = ImageManager::gd()->read($destinationPath);
                    //$img = $manager->make($destinationPath);
                    
                    // Resize the image and maintain the aspect ratio
                    $img->resize(300, 200);
                    
                    
                    // Save the thumbnail image
                    $img->save($thumbnailPath);
    
                    // Save the image name to the category
                    $category->image = $newImageName;
                }
            }
        }
    
        $category->save();
    
        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Category created successfully.'
        ]);
    }
    
   // Generate slug based on the name input
   public function generateSlug(Request $request)
   {
       $name = $request->input('name');
       $slug = Str::slug($name);

       // Check if the generated slug already exists in the database
       $slugExists = Category::where('slug', $slug)->exists();

       // If slug exists, append a unique identifier
       if ($slugExists) {
           $count = 1;
           $originalSlug = $slug;
           while (Category::where('slug', $slug)->exists()) {
               $slug = "{$originalSlug}-{$count}";
               $count++;
           }
       }

       // Return the generated or modified slug
       return response()->json([
           'status' => true,
           'slug' => $slug,
       ]);
   }
    
    public function edit($id)
    {
        // Implement the logic for editing categories if needed
        $category = Category::findOrFail($id); // Fetch the category by ID
    return view('admin.category.edit', compact('category')); // Return the edit view with the category data

    }

    public function update(Request $request, $id)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:categories,slug,' . $id, // Ensure unique slug for the specific category
        'status' => 'required|boolean',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the image
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        // Return errors back as JSON with status 422 for AJAX handling
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Find the category by its ID
    $category = Category::findOrFail($id);

    // Update the category with the new data
    $category->name = $request->input('name');
    $category->slug = $request->input('slug');
    $category->status = $request->input('status');

    // Handle image upload if a new image is provided
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        // Upload new image
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension(); // Get the original extension
        $newImageName = $category->id . '.' . $ext; // Create new image name
        $destinationPath = public_path('uploads/category/' . $newImageName);

        // Move the uploaded file to the destination path
        try {
            $image->move(public_path('uploads/category'), $newImageName);
            $category->image = 'uploads/category/' . $newImageName; // Save relative path
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to upload the image: ' . $e->getMessage()
            ], 500);
        }
    }

    // Save the updated category
    $category->save();

    // Return success response
    return response()->json([
        'status' => true,
        'message' => 'Category updated successfully.'
    ]);
}

  

public function destroy($id)
{
    // Find the category by its ID
    $category = Category::findOrFail($id);

    try {
        // Delete the associated image if it exists
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        // Attempt to delete the category
        $category->delete();

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.'
        ]);
    } catch (\Exception $e) {
        // Handle any errors that occur during deletion
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while deleting the category: ' . $e->getMessage()
        ], 500);
    }
}
}
