<?php
namespace App\Http\Controllers\upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class index extends Controller
{
    //
    public function main($request)
    {
        $path = $request['path'];
        $file = $request['file'];
        $name = $request['name'];

        
        //
        $upload = Storage::disk('local')
        ->put($path . $name, file_get_contents($file));

        //
        $data = [
            'message'       =>  'Upload success...'
        ];
        return $data;
    }

    public function data($request)
    {
        // create
        $file = $request['file'];
        $name = $request['name'];
        $path = 'upload/' . ($request['ext'] === 'pdf' ? 'pdf' : ($request['ext'] === 'docx' ? 'doc' : 'images') );

        //upload file
        $this->main([
            'file'      =>  $file,
            'name'      =>  $name,
            'path'      =>  $path
        ]);
    }

    public function test(Request $request)
    {

        $file = $request->file('file');
        $type = $file->getClientOriginalExtension();
        // path
        $upload = $this->main([
            'name'          =>  $request->name . '.' . $type,
            'file'          =>  $file,
            'path'          =>  $request->path
        ]);

        return $upload;
    }

}