<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Role;
use App\Image;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\UploadTrait;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:200',
            'email' => 'required|string|email|unique:users',
            'position' => 'required|string|max:200',
            'password' => 'required|string|min:8|confirmed',
            'biography' => 'required|string|max:2000',
            'dateOfBirth' => 'required|date|before:-18 years',
            'image.*' => 'image|nullable|mimes:png,jpg,jpeg,gif|max:2048'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $request = request();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'position' => $data['position'],
            'biography' => $data['biography'],
            'dateOfBirth' => $data['dateOfBirth'],
            'password' => Hash::make($data['password']),
        ]);

        if($request->hasFile('image')) {

            $imagePath = config('path.image.profile');

            $image = $request->file('image');
                
            // Make a image name based on uniqid and user name
            $imageName = uniqid() . '_' . $user->name . '.' . $image->getClientOriginalExtension();
            $path=$request->file('image')->storeAs($imagePath,$imageName);
            
            // Save image's name in database
            $Image = Image::create([
                'filename' => $imageName,
                'imageable_id' => $user->id,
                'imageable_type' => 'App\Profile'
            ]);

        }else{
            return 'Image not getting';
            exit;
        }

        $user->assignRole('user');

        return $user;
    }
}
