<?php

namespace App\Traits;

use App\Enums\WebsiteFilesFor;
use App\Enums\WebsiteFilesType;
use App\Models\websitefiles;
use Illuminate\Support\Facades\File;

trait websiteFileTrait
{
    // , $filetype = WebsiteFilesType::IMAGE->value
    public function saveFile($files = [], $filesfor = WebsiteFilesFor::MAIN->value, $referenceId = null, $fileId = null, $linksrc = null, $status = 'CREATE', $belongsTo = null)
    {
        if (!empty($belongsTo) || $belongsTo != null) {
            if (isset($files) && count($files) > 0) {
                if ($status == 'CREATE') {
                    $this->createImage($filesfor, $referenceId, $files, $belongsTo);
                } else if ($status == 'UPDATE') {
                    $this->updateImage($filesfor, $files, $fileId, $referenceId, $belongsTo);
                } else if ($status == 'DELETE') {
                    $this->deleteFIle($referenceId, $fileId);
                }
            }
        }
    }

    function createImage($filesfor, $referenceId, $files, $belongsTo)
    {
        $path = 'ASSETS/backend_assets/images/' . $referenceId . '/' . $filesfor . '/';
        $imageArray = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'bmp', 'BMP', 'webp', 'WEBP', 'tiff', 'TIFF'];
        $videoArray = ['mp4', 'm4a', 'm4v', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'webm', 'mpeg', '3gp'];
        foreach ($files as $file) {
            if (in_array($file->getClientOriginalExtension(), $imageArray)) {
                $filetype =  WebsiteFilesType::IMAGE->value;
            } else if ($file->getClientOriginalExtension() == 'pdf') {
                $filetype =  WebsiteFilesType::PDF->value;
            } else if ($file->getClientOriginalExtension() == 'xls' || $file->getClientOriginalExtension() == 'xlsx') {
                $filetype =  WebsiteFilesType::EXCEL->value;
            } else if ($file->getClientOriginalExtension() == 'csv') {
                $filetype =  WebsiteFilesType::CSV->value;
            }
            if (in_array($file->getClientOriginalExtension(), $videoArray)) {
                $filetype =  WebsiteFilesType::VIDEO->value;
            } else {
                $filetype =  WebsiteFilesType::OTHER->value;
            }
            $fileName = $file->getClientOriginalName(); // Use original filename
            websitefiles::create([
                'reference_id' => $referenceId,
                'filename' => $fileName,
                'filetype' => $filetype,
                'filextension' => $file->getClientOriginalExtension(),
                'filesrc' => $path . $fileName,
                'filesfor' => $filesfor,
                'belongsTo' => $belongsTo,
            ]);
            $file->move(public_path($path), $fileName);
        }
    }

    function updateImage($filesfor, $files, $fileId, $referenceId, $belongsTo)
    {
        $path = 'ASSETS/backend_assets/images/' . $referenceId . '/' . $filesfor . '/';
        $imageArray = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'bmp', 'BMP', 'webp', 'WEBP', 'tiff', 'TIFF'];
        $videoArray = ['mp4', 'm4a', 'm4v', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'webm', 'mpeg', '3gp'];
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            if (in_array($file->getClientOriginalExtension(), $imageArray)) {
                $filetype =  WebsiteFilesType::IMAGE->value;
            } else if ($file->getClientOriginalExtension() == 'pdf') {
                $filetype =  WebsiteFilesType::PDF->value;
            } else if ($file->getClientOriginalExtension() == 'xls' || $file->getClientOriginalExtension() == 'xlsx') {
                $filetype =  WebsiteFilesType::EXCEL->value;
            } else if ($file->getClientOriginalExtension() == 'csv') {
                $filetype =  WebsiteFilesType::CSV->value;
            }
            if (in_array($file->getClientOriginalExtension(), $videoArray)) {
                $filetype =  WebsiteFilesType::VIDEO->value;
            } else {
                $filetype =  WebsiteFilesType::OTHER->value;
            }

            WebsiteFiles::where([
                'id' => $fileId
            ])->update([
                'filename' => $fileName,
                'filetype' => $filetype,
                'filextension' => $file->getClientOriginalExtension(),
                'filesrc' => $path . $fileName,
                'belongsTo' => $belongsTo,
            ]);
            $file->move(public_path($path), $fileName);
        }
    }

    function deleteFIle($referenceId, $fileId)
    {
        $WebsiteFiles = websitefiles::where('id', $fileId)->where('reference_id', $referenceId)->get();
        foreach ($WebsiteFiles as $WebsiteFile) {
            $fileToDelete = public_path($WebsiteFile->filesrc);
            if (File::exists($fileToDelete)) {
                File::delete($fileToDelete);
            }
            $WebsiteFile->delete();
        }
    }
}
