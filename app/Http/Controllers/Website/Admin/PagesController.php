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
use App\Models\Website\Admin\pageItems;
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
            $pages = Page::select(['id', 'title', 'menuId', 'contentTitle', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($pages)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($page) {
                    return [
                        'display' => $page->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $page->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('menu', function ($page) {
                    $menu = $page->menu()->first();
                    return $menu->menu ?? null;
                })
                ->addColumn('contentTitle', function ($page) {
                    return $page->contentTitle ?? null;
                })

                ->addColumn('status', function ($page) {
                    $statusButton = '';
                    if ($page->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $page->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $page->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($page) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $page->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($page) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $page->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['menu', 'status', 'contentTitle', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'Page';
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
        $menuId =  $data['menu'];
        $menuName = '';
        $checkPageExist = Page::where('menuId', $menuId)->where('status', Status::ACTIVE->value)->first();
        if ($checkPageExist) {
            notyf()->warning('Page Exits Try Edit');
            return redirect()->back()->withInput();
        }

        $getPageItems = pageItems::where('menuId', $menuId)
            ->where('status', Status::ACTIVE->value)
            ->orderBy('contentSortOrder', 'asc')  // ASCENDING (smallest to largest)
            ->get() ?? [];

        if (!empty($data['menu'])) {
            $menu = Navigation::where('id', $data['menu'])->first();
            $menuName = $menu->menu ?? null;
        }
        $pageTitle =  $data['title'] ?? null;
        $metaDesc =  $data['metaDesc'] ?? null;
        $metaTags =  $data['metaTags'] ?? null;
        return view('WEBSITE.ADMIN.PAGE.continue', compact('title', 'menuId', 'pageTitle', 'metaDesc', 'metaTags', 'menuName', 'getPageItems'));
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
            'pageTitle' => 'required|string',
            'menuId' => 'required|string',
            'menuName' => 'required|string',
            'metaTags' => 'required|string',
            'metaDesc' => 'required|string',

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
        $pageTitle = $request->input('pageTitle', '');
        $menuId = $request->input('menuId', '');
        $menuName = $request->input('menuName', '');
        $metaTags = $request->input('metaTags', '');
        $metaDesc = $request->input('metaDesc', '');


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
            // here each item of each section to be saved as individual entry

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

    function getTemplate(Request $request)
    {
        $type = $request->query('type'); // <-- check if expecting a param

        if (!$type) {
            return response()->json(['success' => false, 'message' => 'Missing template type'], 400);
        }
        $html = (new Helpers())->addNewSectionModalInputs($type) ?? [];
        return response()->json(['success' => true, 'html' => $html ?? '']);
    }


    public function addPageElement(Request $request)
    {
        $formData = $request->all();
        $elementTitle = $request->title;
        $elementSortOrder = $request->sectionSortOrder;
        $menuId = $request->menuId;
        $elementType = $request->elementType;

        if (!$elementTitle) {
            return response()->json(['success' => false, 'message' => 'Element title is requiried'], 400);
        }
        if (!$elementSortOrder) {
            return response()->json(['success' => false, 'message' => 'Element sort order is requiried'], 400);
        }
        if (!$menuId) {
            return response()->json(['success' => false, 'message' => 'Menu issue fond please reload and try again'], 400);
        }

        $getExistingPageElements = pageItems::where('menuId', $menuId)->first();
        if (!empty($getExistingPageElements)) {
            if ($getExistingPageElements->contentSortOrder == $elementSortOrder) {
                return response()->json(['success' => false, 'message' => 'sort order exist please change order'], 400);
            }
        }
        // Exclude the specific fields you don't need
        $excludedFields = ['title', 'sectionSortOrder', 'menuId'];
        if($elementType == 'CONTENT'){
            //add excluding like video and images and other files here if required, files usually covers all
            $excludedFields = ['title', 'sectionSortOrder', 'menuId', 'files'];
        }
        // Filter out excluded fields from form data
        $filteredData = array_diff_key($formData, array_flip($excludedFields));

        // Optionally encode it to JSON
        $jsonData = json_encode($filteredData);
        $pageItems = pageItems::create(
            [
                'menuId' => $menuId,
                'contentTitle' => $elementTitle,
                'contentSortOrder' => $elementSortOrder,
                'content' => $jsonData,
                'elementType' => $elementType,
            ]
        );

        if ($request->files && $elementType != 'CONTENT') {
            foreach ($request->files as $key => $files) {
                $this->saveFile(
                    $files,
                    $filesfor = WebsiteFilesFor::MAIN->value,
                    $referenceId = $pageItems->id,
                    $fileId = null,
                    $linksrc = null,
                    $status = 'CREATE',
                    WebsiteFilesBelongsTo::PAGEELEMENT->value
                );
            }
        }
        return response()->json(['success' => true, 'message' => 'ELement Added']);
    }

    function deletePageElement(Request $request){
        $pageElementId = $request->pageElementId;
        $getExistingPageElement = pageItems::where('id', $pageElementId)->first();
        if ($getExistingPageElement) {
            $elementType = $getExistingPageElement->elementType;
            $getExistingPageElement->delete();

            return response()->json([
                'success' => true,
                'message' => $elementType . ' removed'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Element not found'
            ], 404);
        }
    }
}


