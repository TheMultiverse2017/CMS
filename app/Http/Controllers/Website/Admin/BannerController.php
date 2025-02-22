<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Enums\WebsiteFilesType;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Banner;
use App\Models\Website\Admin\Navigation;
use App\Models\WebsiteFiles;
use App\Traits\websiteFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    use websiteFileTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $request = Request();
        if ($request->ajax()) {
            $banners = Banner::select(['id', 'title','menu', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($banners)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($banner) {
                    return [
                        'display' => $banner->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $banner->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('menu', function ($banner) {
                    $menu = $banner->menu()->first();
                    return $menu->menu ?? null;
                })
                ->addColumn('status', function ($banner) {
                    $statusButton = '';
                    if ($banner->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $banner->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $banner->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($banner) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $banner->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($banner) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $banner->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['menu','status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'Banner';
        return view('WEBSITE.ADMIN.BANNER.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'title' => ['nullable'],
            'menu' => ['string', 'max:255'],
            'file' => ['required'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
            'menu.max' => 'Menu cannot exceed 255 characters.',
            'file.required' => 'File is a required field.',
            'file.*.mimes' => 'File must be an image.',
            'file.*.max' => 'Image file size cannot exceed 2MB.',

        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        $banner = Banner::create([
            'title' => $data['title'],
            'menu' => $data['menu'],
        ]);

        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                $filesfor = WebsiteFilesFor::MAIN->value,
                $referenceId = $banner->id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::BANNERS->value
            );
        }

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $banners = Banner::where('id', $id)->first();
            $files = $banners->websitefiles()->where('belongsTo', WebsiteFilesBelongsTo::BANNERS->value)->get();
            return response()->json(['success' => true, 'data' => $banners, 'files' => $files]);
        }
    }

    public function update($id)
    {
        $request = Request();
        // Perform validation
        $validator = Validator::make(request()->all(), [
            'title' => ['nullable',],
            'menu' => ['string', 'max:255'],
            'file' => ['nullable'],
            'file.*' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
            'menu.max' => 'Menu cannot exceed 255 characters.',
            // 'file.required' => 'File is a required field.',
            'file.*.mimes' => 'File must be an image.',
            'file.*.max' => 'Image file size cannot exceed 2MB.',
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
        Banner::where('id', $id)->update([
            'title' => $data['title'],
            'menu' => $data['menu'],
        ]);

        // Save files if provided
        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                WebsiteFilesFor::MAIN->value,
                $referenceId = $id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::BANNERS->value
            );
        }

        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully.',
        ]);
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = Banner::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::BANNERS->value)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
            } else {

                $updated = Banner::where('id', $id)->update([
                    'status' => Status::INACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::BANNERS->value)->update([
                    'status' =>  Status::INACTIVE->value,
                ]);
            }

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }

    public function delete($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $deleted = Banner::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
