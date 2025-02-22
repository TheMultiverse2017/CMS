<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Testimonial;
use App\Models\WebsiteFiles;
use App\Traits\websiteFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialsController extends Controller
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
            $testimonials = Testimonial::select(['id', 'name', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($testimonials)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($testimonial) {
                    return [
                        'display' => $testimonial->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $testimonial->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('status', function ($testimonial) {
                    $statusButton = '';
                    if ($testimonial->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $testimonial->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $testimonial->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($testimonial) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $testimonial->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($testimonial) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $testimonial->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'testimonial';
        return view('WEBSITE.ADMIN.TESTIMONIAL.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['string', 'max:255'],
            'testimonial' => ['string'],
            'file' => ['required'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'testimonial.required' => 'Testimonial is a required field.',
            'testimonial.string' => 'Testimonial must be a string.',
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
        $testimonial = Testimonial::create([
            'name' => $data['name'],
            'testimonial' => $data['testimonial'],
        ]);

        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                $filesfor = WebsiteFilesFor::MAIN->value,
                $referenceId = $testimonial->id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::TESTIMONIALS->value
            );
        }

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $testimonials = Testimonial::where('id', $id)->first();
            $files = $testimonials->websitefiles()->where('belongsTo', WebsiteFilesBelongsTo::TESTIMONIALS->value)->get();
            return response()->json(['success' => true, 'data' => $testimonials, 'files' => $files]);
        }
    }

    public function update($id)
    {
        $request = Request();
        // Perform validation
        $validator = Validator::make(request()->all(), [
            'name' => ['string', 'max:255'],
            'testimonial' => ['string'],
            'file' => ['nullable'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'testimonial.required' => 'Testimonial is a required field.',
            'testimonial.string' => 'Testimonial must be a string.',
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

        // Update the testimonial record
        Testimonial::where('id', $id)->update([
            'name' => $data['name'],
            'testimonial' => $data['testimonial'],
        ]);

        // Save files if provided
        if (isset($data['file']) && !empty($data['file'])) {
            $websiteFile = WebsiteFiles::where('reference_id',$id)
            ->where('belongsTo',WebsiteFilesBelongsTo::TESTIMONIALS->value)
            ->first();
            $this->saveFile(
                $data['file'],
                WebsiteFilesFor::MAIN->value,
                $referenceId = $id,
                $fileId = $websiteFile->id,
                $linksrc = null,
                $status = 'UPDATE',
                WebsiteFilesBelongsTo::TESTIMONIALS->value
            );
        }

        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'testimonial updated successfully.',
        ]);
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = Testimonial::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::TESTIMONIALS->value)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
            } else {

                $updated = Testimonial::where('id', $id)->update([
                    'status' => Status::INACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::TESTIMONIALS->value)->update([
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
            $deleted = Testimonial::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
