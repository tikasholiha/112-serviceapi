<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function success_json($message, $data)
    {
        return response()->json([
            'message' => $message,
            'success'  => true,
            'data'    => $data
        ], Response::HTTP_OK);
    }

    protected function error_json($message, $data, $code)
    {
        return response()->json([
            'message' => $message,
            'success'  => false,
            'data'    => $data
        ], $code);
    }
}
