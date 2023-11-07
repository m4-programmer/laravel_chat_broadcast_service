<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function success($data, string $message, int $status=200){
        return response()->json(['status'=>$status,'message'=>$message,'data'=>$data],$status);
    }

    public function error($message, int $status=500){
        return response()->json(['status'=>$status,'message'=>$message],$status);
    }
}
