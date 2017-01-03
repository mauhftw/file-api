<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\File;

class FileController extends Controller {



    public function __construct() {

        $this->middleware('jwt.auth');
    }




    public function index() {

        $files = File::get(['id','name','mime_type','content']);

        if (!$files) {
            return resonse()->json([
                'error' => ['message' => 'There are not files stored!']
                ],404);
        }

        return response()->json([
                'message' => $files
            ],200);
        
    }


    public function store(Request $request) {

        $validator = Validator::make($request->all(),[
                'file' => 'required|file|unique:files,content',
            ]);


        if ($validator->fails()) {
            return response()->json([
                    'error' => ['message' => 'File is required']
                    ],400);
        }



        if ($request->file('file')->isValid()) {
            
            $hash_function = 'sha256';

            $file = $request->file->path();
            $content = hash_file($hash_function,$file);


            $file_exist = File::where('content',$content)->first();

            if ($file_exist) {
                return response()->json([
                    'error' => ['message' => 'The file content already exist!']
                    ],400);
            }


            $filename = $request->file->getClientOriginalName();
            $path = $request->file->storeAs('public/images',$filename);

            $file = new File();
            $file->name = $filename;
            $file->path = $path;
            $file->mime = $request->file->getMimeType();
            $file->content = $content;
            $file->save();

            return response()->json([
                    'message' => 'The file has been uploaded successfully!'
                ],200);



        } else {

            return response()->json([
                    'error' => ['message' => 'Corrupt file, please try again!']
                    ],400);
        }



    }


    public function destroy($id) {

       
    }

    public function download($id) {


    }



}
