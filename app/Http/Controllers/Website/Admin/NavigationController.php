<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Website\Admin\Navigation;
use App\Traits\websiteFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NavigationController extends Controller
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
            $navigationMenus = Navigation::select(['id', 'menu', 'updated_at', 'status'])->orderBy('updated_at', 'desc')->get();
            return datatables()->of($navigationMenus)
                ->addIndexColumn() // Adds SL No.
                ->addColumn('updated_at', function ($navigationMenu) {
                    return [
                        'display' => $navigationMenu->updated_at->format('d M Y, h:i A'), // User-friendly format
                        'timestamp' => $navigationMenu->updated_at->timestamp, // For sorting
                    ];
                })
                ->addColumn('status', function ($navigationMenu) {
                    $statusButton = '';
                    if ($navigationMenu->status == 1) {
                        // If status is 1, show "Deactivate" button
                        $statusButton = '<button style="width:100%" data-status="disable" data-id="' . $navigationMenu->id . '" type="button" class="mx-2 col btn btn-danger btnDeActivate">Active - Deactivate</button>';
                    } else {
                        // If status is 0, show "Activate" button
                        $statusButton = '<button style="width:100%" data-status="enable" data-id="' . $navigationMenu->id . '" type="button" class="mx-2 col btn btn-success btnActivate">InActive - Activate</button>';
                    }
                    return $statusButton;
                })
                ->addColumn('action', function ($navigationMenu) {
                    return '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnEditModal" class="btn btn-sm btn-primary action-btn btnEditModal" data-id="' . $navigationMenu->id . '" ><i class="bi bi-pencil-square nav-icon"></i></button>';
                })
                ->addColumn('delete', function ($navigationMenu) {
                    $deleteButton = '<button style="width:100%" data-bs-toggle="modal" data-bs-target="#btnDeleteModal" data-status="delete" data-id="' . $navigationMenu->id . '" type="button" class="mx-2 col btn btn-danger btnDeleteModal "><i class="bi bi-archive-fill nav-icon"></i></button>';
                    return $deleteButton;
                })
                ->rawColumns(['status', 'action', 'delete']) // Render HTML in these columns
                ->make(true);
        }

        $title = 'navigationMenu';
        return view('WEBSITE.ADMIN.NAVIGATION.index', compact('title'));
    }

    public function save()
    {
        $validator = Validator::make(request()->all(), [
            'menu' => ['string', 'max:255'],
            'submenu' => ['string', 'nullable'],
        ], [
            'menu.required' => 'Menu is a required field.',
            'menu.string' => 'Menu must be a string.',
            'menu.max' => 'Menu cannot exceed 255 characters.',
            'submenu.string' => 'Sub Menu must be a string.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                notyf()->warning($error);
            }
            return redirect()->back()->withInput();
        }

        $data = $validator->validated();
        $checkMenu = Navigation::where('menu',$data['menu'])->first();
        if(!empty($checkMenu)){
            notyf()->success('Menu Exist.');
            return redirect()->back();
        }
        $navigationMenu = Navigation::create([
            'menu' => $data['menu'],
        ]);

        if (!empty($data['submenu'])) {
            $subMenus = explode(',', $data['submenu']) ?? [];
            foreach ($subMenus as $subMenu) {
                $checkMenu = Navigation::where('menu',$subMenu)->first();
                if(!empty($checkMenu)){
                    notyf()->success('Menu Exist.');
                    return redirect()->back();
                }

                Navigation::create([
                    'menu' => $subMenu,
                    'reference_id' => $navigationMenu->id,
                ]);
            }
        }
        notyf()->success('Your request was processed successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $request = Request();
        if ($request->ajax()) {
            $getMenu = Navigation::where('id', $id)->first();
            $mainMenu = false;
            if ($getMenu->reference_id == null) {
                //main menu
                $mainMenu = true;
                $navigationMenus = Navigation::where('id', $id)->first();
            } else {
                //submenu
                $navigationMenus = Navigation::where('id', $getMenu->reference_id)->first();
                $navigationSUbMenus = Navigation::where('id', $id)->first() ?? null;
            }

            $menus = [
                'navigationMenus' => $navigationMenus,
                'navigationSUbMenus' => $navigationSUbMenus ?? null,
                'mainMenu' => $mainMenu,
            ];
            return response()->json(['success' => true, 'data' => $menus]);
        }
    }

    public function update($id)
    {
        // Perform validation\
        $request = Request();
        $validator = Validator::make(request()->all(), [
            'menu' => ['nullable', 'max:255'],
            'menudropdown' => ['nullable', 'max:255'],
            'submenu' => ['string', 'nullable'],
        ]);

        $footer = false;
        if ($request->has('footer')) {
            $footer = true;
        }

        if ($validator->fails()) {
            // For AJAX requests, return JSON with validation errors
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        // Validation passed
        $data = $validator->validated();
        $checkMenu = Navigation::where('id', $id)->first();

        if ($checkMenu->reference_id == null) {
            $checkMenuExist = Navigation::where('id' ,'!=',$id)->where('menu',$data['menu'])->first();
            if(!empty($checkMenuExist)){
                return response()->json([
                    'success' => true,
                    'message' => 'Menu Exist.',
                ]);
            }
            // Update the navigationMenu record
            $navigationMenu = Navigation::where('id', $id)->where('reference_id', null)->update([
                'menu' => $data['menu'],
                'footer' => $footer ?? false,
            ]);
        } else {
            $getMainMenu = Navigation::where('id', $data['menudropdown'])->first();
            if ($getMainMenu->menu == $data['submenu']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu and Sub Menu Cannot be Same.',
                ]);
            }
            $checkMenuExist = Navigation::where('id' ,'!=',$id)->where('menu',$data['submenu'])->first();
            if(!empty($checkMenuExist)){
                return response()->json([
                    'success' => true,
                    'message' => 'Menu Exist.',
                ]);
            }
            Navigation::where('id', $id)->update([
                'menu' => $data['submenu'],
                'reference_id' => $getMainMenu->id,
                'footer' => $footer ?? false,
            ]);
        }
        // Return success response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'navigationMenu updated successfully.',
        ]);
    }


    public function status($status, $id)
    {
        $request = Request();
        if ($request->ajax()) {
            if ($status == 'enable') {
                $updated = Navigation::where('id', $id)->update([
                    'status' =>  Status::ACTIVE->value,
                ]);
            } else {

                $updated = Navigation::where('id', $id)->update([
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
            $deleted = Navigation::where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Your request was processed successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update the status.'], 500);
            }
        }
    }
}
