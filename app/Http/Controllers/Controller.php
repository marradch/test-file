<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $filename = $request->input('name');

        if ($request->has('chunk') && $request->has('chunks')) {
            $chunkNumber = $request->input('chunk');
            $totalChunks = $request->input('chunks');

            $file->move(storage_path("app/uploads"), "{$filename}.part{$chunkNumber}");

            if ($chunkNumber == $totalChunks - 1) {
                $this->mergeChunks($filename, $totalChunks);
                return response()->json(['message' => 'File uploaded successfully.']);
            }

            return response()->json(['message' => 'Chunk uploaded successfully.']);
        } else {
            $file->move(storage_path("app/uploads"), $filename);
            return response()->json(['message' => 'File uploaded successfully.']);
        }
    }

    private function mergeChunks($filename, $totalChunks)
    {
        $outputPath = storage_path("app/uploads/{$filename}");
        $outputFile = fopen($outputPath, 'ab');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunk = file_get_contents(storage_path("app/uploads/{$filename}.part{$i}"));
            fwrite($outputFile, $chunk);
            unlink(storage_path("app/uploads/{$filename}.part{$i}"));
        }

        fclose($outputFile);
    }
}
