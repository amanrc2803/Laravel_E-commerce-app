<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        // Validate the uploaded image
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image type and size (2MB max)
        ]);

        if ($validator->fails()) {
            // Return validation errors if image does not meet criteria
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle the uploaded image
        $image = $request->file('image');

        if ($image) {
            $ext = $image->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            // Save the temporary image record
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            try {
                // Move the uploaded image to the temporary folder
                $image->move(public_path('temp'), $newName);

                return response()->json([
                    'status' => true,
                    'image_id' => $tempImage->id,
                    'message' => 'Image uploaded successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'No image uploaded'
        ], 400);
    }
}
