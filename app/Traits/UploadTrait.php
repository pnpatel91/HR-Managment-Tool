<?php 
namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use File;

/**
 * 
 */
trait UploadTrait
{
    public function uploadFile(UploadedFile $uploadedFile, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : Str::random(25) . '.' . $uploadedFile->getClientOriginalExtension();

        $file = $uploadedFile->storeAs($folder, $name , $disk);

        return $file;
    }

    public function deleteUploadFile($folder = null, $filename = null, $disk = 'public')
    {
        if(Storage::disk($disk)->exists($folder .$filename)) {
            Storage::disk($disk)->delete($folder . $filename);
        }
    }
}
