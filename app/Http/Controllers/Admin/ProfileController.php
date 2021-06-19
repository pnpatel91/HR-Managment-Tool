<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Traits\UploadTrait;
use File;

class ProfileController extends Controller
{
    use UploadTrait;

    public function update(Request $request)
    {
        $id = auth()->user()->id;

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|string|email|unique:users,email,'.$id.',id',
            'position' => 'required|string|max:200',
            'biography' => 'required|string|max:2000',
            'dateOfBirth' => 'required|date|before:-18 years'
        ]);

        auth()->user()->update($request->only('name', 'email', 'position', 'biography' , 'dateOfBirth'));
        return redirect()->route('admin.profile.index');
    }

    public function updateProfileImage(Request $request)
    {
        $id = auth()->user()->id;
        $name = auth()->user()->name;

        $request->validate([
            'image.*' => 'image|nullable|mimes:png,jpg,jpeg,gif|max:2048'
        ]);

        if($request->hasFile('image')) {
            
            $oldImage = Image::where('imageable_id', $id)->first();
            if(isset($oldImage->filename)) {
                // Define image path
                $imagePath = config('path.image.storageprofile');

                // Delete old images
                $this->deleteUploadFile($imagePath, $oldImage->filename);
                $oldImage->delete();
            }

            $imagePath = config('path.image.profile');
            $image = $request->file('image');
                
            // Make a image name based on uniqid and user name
            $imageName = uniqid() . '_' . $name . '.' . $image->getClientOriginalExtension();
            $path=$request->file('image')->storeAs($imagePath,$imageName);
            
            // Save image's name in database
            $Image = Image::create([
                'filename' => $imageName,
                'imageable_id' => $id,
                'imageable_type' => 'App\Profile'
            ]);

        }else{
            return 'Image not getting';
            exit;
        }

        return redirect()->route('admin.profile.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ]);
        $user = User::findOrFail(auth()->id());
        // Validate old password form db and request
        if(!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors('The old password does not match.');
        }
        if($user->update(['password' => bcrypt($request->new_password)])) {
            return redirect()->route('admin.profile.index')
            ->with('success', 'The password was changed.');
        }
        return redirect()->route('admin.profile.index')
            ->withErrors('The password changing Fail.');
    }
}
