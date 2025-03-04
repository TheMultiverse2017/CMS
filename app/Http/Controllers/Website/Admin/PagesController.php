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
        return view('WEBSITE.ADMIN.PAGE.index', compact('title'));
    }

    public function create()
    {
            $title = 'Create';
            return view('WEBSITE.ADMIN.PAGE.create', compact('title'));
    }
    public function edit($id)
    {
            $post = Page::where('id', $id)->first();
            $title = 'Edit '.$post->title;
            return view('WEBSITE.ADMIN.PAGE.edit', compact('title','post'));
    }

    function getContainers(){
        $request = Request();
        if ($request->ajax()) {
            $data = (New Helpers())->allContainers();
            return response()->json(['success' => true, 'data' => $data]);
        }else {
            return response()->json(['success' => false, 'message' => 'Error.'], 500);
        }
    }

    function getComponents(){
        $request = Request();
        if ($request->ajax()) {
            $data = (New Helpers())->allComponents();
            return response()->json(['success' => true, 'data' => $data]);
        }else {
            return response()->json(['success' => false, 'message' => 'Error.'], 500);
        }
    }

    function getClasses(){
        $request = Request();
        if ($request->ajax()) {
            $data = (New Helpers())->allClasses();
            return response()->json(['success' => true, 'data' => $data]);
        }else {
            return response()->json(['success' => false, 'message' => 'Error.'], 500);
        }
    }

    function getValue(){
        $request = Request();
        $key = request()->input('key', null);
        if ($request->ajax()) {
            if(!empty($key)){
                $allClasses = (New Helpers())->allClasses($key);
                $allComponents = (New Helpers())->allComponents($key);
                $allContainers = (New Helpers())->allContainers($key);
                if($allClasses != null){
                    $data = $allClasses ;
                }else if($allComponents != null){
                    $data = $allComponents;
                }else if($allContainers != null){
                    $data = $allContainers;
                }
                $data = $allClasses ?? $allComponents ?? $allContainers;
            }
            return response()->json(['success' => true, 'data' => $data]);
        }else {
            return response()->json(['success' => false, 'message' => 'Error.'], 500);
        }

    }
}
