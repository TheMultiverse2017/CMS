<?php

namespace App\Http\Controllers\Website\Admin;

use App\Enums\WebsiteFilesBelongsTo;
use App\Enums\WebsiteFilesFor;
use App\Http\Controllers\Controller;
use App\Models\WebsiteFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WebsiteFilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deleteFile($id)
    {
        $request = Request();
        $requestContainer = 'files';
        if ($request->ajax()) {
            $WebsiteFile = WebsiteFiles::where('id', $id)->first();
            $fileToDelete = public_path($WebsiteFile->filesrc);
            if (File::exists($fileToDelete)) {
                File::delete($fileToDelete);
            }
            $WebsiteFile->delete();
            $belongsTo = [
                WebsiteFilesBelongsTo::GALLERY->value,
                // WebsiteFilesBelongsTo::NEWSANDEVENTS->value
            ];
            if (in_array($WebsiteFile->belongsTo, $belongsTo)  && $WebsiteFile->filesfor == WebsiteFilesFor::MAIN->value) {
                $requestContainer = 'thumbnailFiles';
            }
            // dd($WebsiteFile);
            $files =  WebsiteFiles::where('reference_id', $WebsiteFile->reference_id)
                ->where('belongsTo', $WebsiteFile->belongsTo)
                ->where('filesfor', $WebsiteFile->filesfor)
                ->get();


            return response()->json(['success' => true, 'message' => 'Your request was processed successfully.', 'files' => $files, 'requestContainer' => $requestContainer]);
        }else{
            $WebsiteFile = WebsiteFiles::where('id', $id)->first();
            $fileToDelete = public_path($WebsiteFile->filesrc);
            if (File::exists($fileToDelete)) {
                File::delete($fileToDelete);
            }
            $WebsiteFile->delete();
            $belongsTo = [
                WebsiteFilesBelongsTo::GALLERY->value,
                // WebsiteFilesBelongsTo::NEWSANDEVENTS->value
            ];
            if (in_array($WebsiteFile->belongsTo, $belongsTo)  && $WebsiteFile->filesfor == WebsiteFilesFor::MAIN->value) {
                $requestContainer = 'thumbnailFiles';
            }
            // dd($WebsiteFile);
            $files =  WebsiteFiles::where('reference_id', $WebsiteFile->reference_id)
                ->where('belongsTo', $WebsiteFile->belongsTo)
                ->where('filesfor', $WebsiteFile->filesfor)
                ->get();

                notyf()->success('Your request was processed successfully.');
                return redirect()->back();
        }
    }
}
