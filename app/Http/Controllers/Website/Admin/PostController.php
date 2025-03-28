<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Post;
use App\Models\WebsiteFiles;
use App\Traits\websiteFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
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
            $posts = Post::select(['id', 'title', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($posts)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($post) {
                    return [
                        'display' => $post->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $post->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('status', function ($post) {
                    $statusButton = '';
                    if ($post->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $post->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $post->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($post) {
                    return '<a style="width:100%" class="btn btn-sm btn-primary action-btn" href="'. route('post.edit', [$post->id]) . '" ><i class="bi bi-pencil-square nav-icon"></i></a>';
                })
                ->addColumn('delete', function ($post) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $post->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'post';
        return view('WEBSITE.ADMIN.POSTS.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'title' => ['string'],
            'meta_tags' => ['string'],
            'meta_desc' => ['string'],
            'meta_title' => ['string'],
            'intro' => ['string'],
            // 'meta_keywords' => ['string'],
            'content' => ['string'],
            'file' => ['required'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'meta_tags.string' => 'Tags must be a string.',
            'meta_desc.string' => 'Desc must be a string.',
            'meta_title.string' => 'Meta Title must be a string.',
            'intro.string' => 'Intro must be a string.',
            // 'meta_keywords.string' => 'Keywords must be a string.',
            'content.string' => 'Content must be a string.',
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
        $post = Post::create([
            'title' => $data['title'],
            'meta_tags' => $data['meta_tags'],
            'meta_title' => $data['meta_title'],
            'meta_desc' => $data['meta_desc'],
            'intro' => $data['intro'],
            // 'meta_keywords' => $data['meta_keywords'],
            'content' => $data['content'],
        ]);

        if (isset($data['file']) && !empty($data['file'])) {
            $this->saveFile(
                $data['file'],
                $filesfor = WebsiteFilesFor::MAIN->value,
                $referenceId = $post->id,
                $fileId = null,
                $linksrc = null,
                $status = 'CREATE',
                WebsiteFilesBelongsTo::POST->value
            );
        }

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
            $post = Post::where('id', $id)->first();
            $title = 'Edit '.$post->title;
            return view('WEBSITE.ADMIN.POSTS.edit', compact('title','post'));
    }

    public function update($id)
    {

        // Perform validation
        $validator = Validator::make(request()->all(), [
            'title' => ['string'],
            'meta_tags' => ['string'],
            'meta_title' => ['string'],
            'meta_desc' => ['string'],
            'intro' => ['string'],
            // 'meta_keywords' => ['string'],
            'content' => ['string'],
            'file' => ['nullable'],
            'file.*' => ['mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'meta_tags.string' => 'Tags must be a string.',
            'meta_title.string' => 'Meta Title must be a string.',
            'meta_desc.string' => 'Desc must be a string.',
            'intro.string' => 'Intro must be a string.',
            // 'meta_keywords.string' => 'Keywords must be a string.',
            'content.string' => 'Content must be a string.',
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

        // Update the post record
        Post::where('id', $id)->update([
            'title' => $data['title'],
            'meta_tags' => $data['meta_tags'],
            'meta_desc' => $data['meta_desc'],
            'meta_title' => $data['meta_title'],
            'intro' => $data['intro'],
            // 'meta_keywords' => $data['meta_keywords'],
            'content' => $data['content'],
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
                WebsiteFilesBelongsTo::POST->value
            );
        }

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = Post::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::POST->value)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
            } else {

                $updated = Post::where('id', $id)->update([
                    'status' => Status::INACTIVE->value,
                ]);
                WebsiteFiles::where('reference_id', $id)->where('belongsTo', WebsiteFilesBelongsTo::POST->value)->update([
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
            $deleted = Post::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
