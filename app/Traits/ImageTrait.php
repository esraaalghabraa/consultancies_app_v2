<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait ImageTrait
{
    /**
     * Save the uploaded image to the public images directory
     * and return the path where the image is stored.
     *
     * @param UploadedFile $image The uploaded image file.
     * @return string The path to the saved image.
     */
    private function setImage($image)
    {
        // Generate a unique filename for the image using a unique ID and the original file extension
        $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
        // Define the path where the image will be stored
        $path = 'images/' . $fileName;
        // Move the uploaded image to the public images directory
        $image->move(public_path('images'), $fileName);
        return $path;
    }

    /**
     * Get the URL of the image stored in the assets/images directory.
     *
     * @param string|null $image The name of the image file.
     * @return string The URL of the image or an empty string if the image is not provided.
     */
    private function getImage($image)
    {
        // Return the URL of the image if the image name is provided, otherwise return an empty string
        return $image ? asset('assets/images/' . $image) : '';
    }
}
