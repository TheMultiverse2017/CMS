<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\SEO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SEOController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $request = Request();
        if ($request->ajax()) {
            $seos = SEO::select(['id', 'menu', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($seos)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($seo) {
                    return [
                        'display' => $seo->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $seo->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('menu', function ($seo) {
                    $menu = $seo->menu()->first();
                    return $menu->menu ?? null;
                })
                ->addColumn('status', function ($seo) {
                    $statusButton = '';
                    if ($seo->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $seo->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $seo->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($seo) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $seo->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($seo) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $seo->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['menu','status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'SEO';
        return view('WEBSITE.ADMIN.SEO.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'menu' => ['string', 'max:255'],
            // 'metaTitle' => ['string', 'max:255'],
            'metaTags' => ['string', 'max:255'],
            'metaDesc' => ['string'],

        ], [
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        $getSeo = SEO::where('menu',$data['menu'])->first();
        if(!$getSeo){
        $seo = SEO::create([
            'menu' => $data['menu'],
            // 'metaTitle' => $data['metaTitle'],
            'metaTags' => $data['metaTags'],
            'metaDesc' => $data['metaDesc']

        ]);
    }else{
        SEO::where('menu', $data['menu'])->update([
            // 'metaTitle' => $data['metaTitle'],
            'metaTags' => $data['metaTags'],
            'metaDesc' => $data['metaDesc']
        ]);
    }
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $seo = SEO::where('id', $id)->first();
            return response()->json(['success' => true, 'data' => $seo]);
        }
    }

    public function update($id)
    {
        $request = Request();
        // Perform validation
        $validator = Validator::make(request()->all(), [
            'menu' => ['string', 'max:255'],
            // 'metaTitle' => ['string', 'max:255'],
            'metaTags' => ['string', 'max:255'],
            'metaDesc' => ['string'],
        ], [
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
            'menu.max' => 'Menu cannot exceed 255 characters.',
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
        SEO::where('id', $id)->update([
            'menu' => $data['menu'],
            // 'metaTitle' => $data['metaTitle'],
            'metaTags' => $data['metaTags'],
            'metaDesc' => $data['metaDesc']
        ]);
        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'SEO updated successfully.',
        ]);
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = SEO::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
            } else {

                $updated = SEO::where('id', $id)->update([
                    'status' => Status::INACTIVE->value,
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
            $deleted = SEO::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
