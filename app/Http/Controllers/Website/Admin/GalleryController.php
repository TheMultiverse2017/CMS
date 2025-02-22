<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Gallery;
use App\Models\WebsiteFiles;
use App\Traits\websiteFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
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
            $gallerys = Gallery::select(['id', 'title', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($gallerys)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($gallery) {
                    return [
                        'display' => $gallery->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $gallery->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('status', function ($gallery) {
                    $statusButton = '';
                    if ($gallery->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $gallery->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $gallery->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($gallery) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $gallery->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($gallery) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $gallery->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }
        $title = 'Gallery';
        return view('WEBSITE.ADMIN.GALLERY.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'title' => ['string', 'max:255'],
            'thumbFile' => ['required'],
            'thumbFile.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:1024'],
            'file' => ['required'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'thumbFile.required' => 'Thumbnail is a required field.',
            'thumbFile.*.mimes' => 'Thumbnail must be an image.',
            'thumbFile.*.max' => 'Thumbnail file size cannot exceed 1MB.',
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
        $gallery = Gallery::create([
            'title' => $data['title'],
        ]);

        // Save files if provided
        if (isset($data['thumbFile']) && !empty($data['thumbFile'])) {
            $this->saveFile(
                $data['thumbFile'],
                WebsiteFilesFor::MAIN->value,
                $referenceId = $gallery->id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::GALLERY->value
            );
        }
        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                $filesfor = WebsiteFilesFor::SUB->value,
                $referenceId = $gallery->id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::GALLERY->value
            );
        }

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $gallerys = Gallery::where('id', $id)->first();
            $thumbnail = $gallerys->websitefiles()
            ->where('belongsTo', WebsiteFilesBelongsTo::GALLERY->value)
            ->where('filesfor',WebsiteFilesFor::MAIN->value)
            ->get();
            $files = $gallerys->websitefiles()
            ->where('belongsTo', WebsiteFilesBelongsTo::GALLERY->value)
            ->where('filesfor',WebsiteFilesFor::SUB->value)
            ->get();
            return response()->json(['success' => true, 'data' => $gallerys, 'files' => $files,'thumbnail' => $thumbnail]);
        }
    }

    public function update($id)
    {
        $request = Request();
        // Perform validation
        $validator = Validator::make(request()->all(), [
            'title' => ['string', 'max:255'],
            'thumbFile' => ['nullable'],
            'thumbFile.*' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg', 'max:1024'],
            'file' => ['nullable'],
            'file.*' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'thumbFile.*.mimes' => 'Thumbnail must be an image.',
            'thumbFile.*.max' => 'Thumbnail file size cannot exceed 1MB.',
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

        // Update the gallery record
        Gallery::where('id', $id)->update([
            'title' => $data['title'],
        ]);

        // Save files if provided
        if (isset($data['thumbFile']) && !empty($data['thumbFile'])) {
            $websiteFile = WebsiteFiles::where('reference_id',$id)
            ->where('filesfor',WebsiteFilesFor::MAIN->value)
            ->where('belongsTo',WebsiteFilesBelongsTo::GALLERY->value)
            ->first();

            if(!empty( $websiteFile)){
                $this->saveFile(
                    $data['thumbFile'],
                    WebsiteFilesFor::MAIN->value,
                    $referenceId = $id,
                    $fileId = $websiteFile->id,
                    $linksrc = null,
                    $status = 'UPDATE',
                    WebsiteFilesBelongsTo::GALLERY->value
                );
            }else{
                $this->saveFile(
                    $data['thumbFile'],
                    WebsiteFilesFor::MAIN->value,
                    $referenceId = $id,
                    $fileId = null,
                    $linksrc = null,
                    $status = 'CREATE',
                    WebsiteFilesBelongsTo::GALLERY->value
                );

            }
        }
        // Save files if provided
        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                WebsiteFilesFor::SUB->value,
                $referenceId = $id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::GALLERY->value
            );
        }

        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Gallery updated successfully.',
        ]);
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = Gallery::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::GALLERY->value)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);

            } else {

                $updated = Gallery::where('id', $id)->update([
                    'status' => Status::INACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::GALLERY->value)->update([
                    'status' =>  Status::ACTIVE->value,
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
            $deleted = Gallery::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
