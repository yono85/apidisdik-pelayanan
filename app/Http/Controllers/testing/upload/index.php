<?php
namespace App\Http\Controllers\testing\upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class index extends Controller
{
    //
    public function image(Request $request)
    {

        $path = 'images/';
        $file = $request->file('image');
        $name = $request->name;
        $upload = Storage::disk('local')
        ->put($path . $name, file_get_contents($file));

        //
        $data = [
            'message'       =>  'Image success di upload',
            'name'          =>  $request->name,
            'file'          =>  $request->file('image')
        ];


        return Response()->json($data, 200);
    }
}