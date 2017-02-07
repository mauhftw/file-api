<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\File;
use Storage;


class FileController extends Controller {



    public function __construct() {

        $this->middleware('jwt.auth');
    }



    public function index() {

        $files = File::get(['id','name','mime_type']);

        if (!$files->count()) {
            return response()->json([
                'error' => ['message' => 'There are not stored files']
                ],404);
        }

        return response()->json([
                'data' => $files
            ],200);
        
    }


    public function store(Request $request) {

        $validator = Validator::make($request->all(),[
                'file' => 'required|file',
            ]);


        if ($validator->fails()) {
            return response()->json([
                    'error' => ['message' => 'File is required']
                    ],400);
        }


        if ($request->file('file')->isValid()) {
            
            $hash_function = config('custom.hash_function');  

            $file = $request->file->path();
            $content = hash_file($hash_function,$file);
            $filename = $request->file->getClientOriginalName();

            // Has the same filename
            $filename_exist = File::where('name',$filename)->first();

            if ($filename_exist) {
                return response()->json([
                    'error' => ['message' => 'The filename is already in use']
                    ],422);    
            }

            // Has the same content
            $file_exist = File::where('content',$content)->first();

            if ($file_exist) {
                return response()->json([
                    'error' => ['message' => 'The content already exist in file: '.$file_exist->name]
                    ],409);
            }


            $path = $request->file->storeAs('public/files',$filename);

            $file = new File();
            $file->name = $filename;
            $file->path = $path;
            $file->mime_type = $request->file->getMimeType();
            $file->content = $content;
            $file->save();

            return response()->json([
                    'message' => 'The file has been uploaded successfully'
                ],200);


        } else {

            return response()->json([
                    'error' => ['message' => 'Corrupt file, please try again']
                    ],419);
        }



    }

    public function destroy($id) {      

        $file = File::find($id);

        if (!$file) {
            return response()->json([
                'error' => ['message' => 'File not found']
                ],404);
        }


        Storage::delete($file->path);
        $file->delete();

        return response()->json([
               'message' => 'The file has been removed successfully'
                ],200);

       
    }
  

    public function download($id) {     

        $file = File::find($id);
        
        if (!$file) {
            return response()->json([
                'error' => ['message' => 'File not found']
                ],404);
        }

        $path = storage_path().'/app'.'/'.$file->path;

        if (!$path) {
            return response()->json([
                'error' => ['message' => 'File not found']
                ],404);
        }


        return response()->download($path);

    }

}


