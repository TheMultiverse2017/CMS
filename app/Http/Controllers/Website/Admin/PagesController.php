<?php

namespace App\Http\Controllers\Website\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Status;
use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Enums\WebsiteFilesType;
use App\Helpers\Helpers;
use App\Models\Website\Admin\Banner;
use App\Models\Website\Admin\Navigation;
use App\Models\Website\Admin\Page;
use App\Models\WebsiteFiles;
use App\Traits\websiteFileTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class PagesController extends Controller
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
            $banners = Banner::select(['id', 'title', 'menu', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
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
                ->rawColumns(['menu', 'status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'Banner';
        return view('WEBSITE.ADMIN.PAGE.index', compact('title'));
    }

    public function create()
    {
        $title = 'Create';
        return view('WEBSITE.ADMIN.PAGE.create', compact('title'));
    }

    public function continue()
    {
        $title = 'Create - Continue';
        $validator = Validator::make(request()->all(), [
            'title' => ['string', 'max:255'],
            'menu' => ['string', 'max:255'],
            'metaDesc' => ['nullable'],
            'metaTags' => ['nullable'],
        ], [
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
            'menu.max' => 'Menu cannot exceed 255 characters.',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        $menu =  $data['menu'];
        $pageTitle =  $data['title'] ?? null;
        $metaDesc =  $data['metaDesc'] ?? null;
        $metaTags =  $data['metaTags'] ?? null;
        return view('WEBSITE.ADMIN.PAGE.continue', compact('title', 'menu', 'pageTitle', 'metaDesc', 'metaTags'));
    }

    public function edit($id)
    {
        $post = Page::where('id', $id)->first();
        $title = 'Edit ' . $post->title;
        return view('WEBSITE.ADMIN.PAGE.edit', compact('title', 'post'));
    }

    public function save(Request $request)
    {
        // Step 1: Validate request
        $validator = Validator::make($request->all(), [
            'sectionTitle' => 'required|array',
            'sectionTitle.*' => 'required|string|max:255',
            'section' => 'required|array',
            'section.*' => 'required|string',
            'sectionSortOrder' => 'required|array',
            'sectionSortOrder.*' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 2: Retrieve inputs
        $titles = $request->input('sectionTitle', []);
        $contents = $request->input('section', []);
        $sortOrders = $request->input('sectionSortOrder', []);

        $sections = [];

        // Step 3: Combine and sanitize
        for ($i = 0; $i < count($titles); $i++) {
            $sections[] = [
                'title' => $titles[$i] ?? '',
                'content' => $contents[$i] ?? '',
                'sort_order' => (int)($sortOrders[$i] ?? 0),
            ];
        }

        // Step 4: Save to database (optional - you can customize this)
        foreach ($sections as $section) {
            // PageSection::create([
            //     'title' => $section['title'],
            //     'content' => $section['content'],
            //     'sort_order' => $section['sort_order'],
            //     // Add other required fields like 'page_id' if needed
            // ]);
        }

        // Step 5: Return success
        return response()->json([
            'success' => true,
            'message' => 'Page sections saved successfully!',
            'data' => $sections
        ]);
    }
}
