<?php

namespace App\Http\Controllers\Website\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $title = 'Profile';
        return view('WEBSITE.ADMIN.profile', compact('title'));
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore(Auth::user()->id, 'id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::user()->id, 'id')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Use `nullable` for optional password updates
        ], [
            'name.required' => 'The Name field is required.',
            'name.string' => 'The Name must be a string.',
            'name.unique' => 'The Name has already been taken.',
            'email.required' => 'The Email field is required.',
            'email.email' => 'The Email must be a valid email address.',
            'email.unique' => 'The Email has already been taken.',
            'password.required' => 'The Password field is required.',
            'password.string' => 'The Password must be a string.',
            'password.min' => 'The Password must be at least 8 characters.',
            'password.confirmed' => 'The Password confirmation does not match.',
        ]);

        $validator->validate();

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();

        // Find the user by ID
        $user = User::findOrFail(Auth::user()->id);

        // Update the user's data
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password, // Retain old password if not updating
        ]);
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    function logo(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'file' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,gif,svg,webp'],
        ], [
            'file.required' => 'File is a required field.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        if (isset($data['file']) && !empty($data['file'])) {
            $file = $data['file'];
            $path = public_path('assets/default/'); // Ensure it's public_path for accessibility
            $extension = $file->getClientOriginalExtension();
            $newFileName = 'logo.' . $extension;

            // Delete existing logo file irrespective of extension
            $existingFiles = glob($path . 'logo.*');
            foreach ($existingFiles as $existingFile) {
                File::delete($existingFile);
            }

            // Move the new file
            $file->move($path, $newFileName);
        }
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    function favicon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'max:2048', 'mimes:ico'],
        ], [
            'file.required' => 'Favicon is required.',
            'file.mimes' => 'Only .ico files are allowed.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $file = $request->file('file');
        $path = public_path(''); // Ensure it's in the public directory
        $newFileName = 'favicon.ico';

        // Delete existing favicon
        if (File::exists($path . $newFileName)) {
            File::delete($path . $newFileName);
        }

        // Move the new favicon
        $file->move($path, $newFileName);

        notyf()->success('Favicon updated successfully.');
        return redirect()->back();
    }
}
