<?php
namespace App\Helpers;
class ResponseHelper{
    public static function success($msg='', $data=[]){
        return response()->json([
            'message' => $msg,
            'status' => true,
            'data' => $data,
            'code' => 200
        ], 200);
    }

    public static function error($msg='', $data=null, $code=403){
        return response()->json([
            'message' => $msg,
            'status' => false,
            'data' => $data,
            'code' => $code
        ], $code);
    }
}
