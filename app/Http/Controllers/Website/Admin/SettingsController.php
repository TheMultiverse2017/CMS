<?php

namespace App\Http\Controllers\Website\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $title = 'Settings';
        $settings = Settings::get();
        return view('WEBSITE.ADMIN.SETTINGS.index', compact('title', 'settings'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['string', 'max:255'],
            'value' => ['string', 'max:255'],
        ], [
            'name.required' => 'Name is a required field.',
            'name.string' => 'Name must be a string.',
            // 'value.required' => 'value is a required field.',
            // 'value.string' => 'value must be a string.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        $settings = Settings::create([
            'name' => $data['name'],
            'value' => $data['value'],
        ]);
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function update($id)
    {
        $request = Request();
        // Perform validation
        $validator = Validator::make(request()->all(), [
            'name' => ['string', 'max:255'],
            'value' => ['string', 'max:255'],
        ], [
            'name.required' => 'Name is a required field.',
            'name.string' => 'Name must be a string.',

        ]);

        if ($validator->fails()) {
            // For AJAX requests, return JSON with validation errors
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        // Validation passed
        $data = $validator->validated();

        // Update the Banner record
        Settings::where('id', $id)->update([
            'name' => $data['name'],
            'value' => $data['value'],
        ]);
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function delete($id)
    {
        $request = Request();
        Settings::where('id', $id)->delete();
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }
}
