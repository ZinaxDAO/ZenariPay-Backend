<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function identity(Request $request)
    {
        try {
            //validate incoming requests firstly
        } catch (\Throwable $th) {
            return get_error_response($th->getMessage(), $th->getCode());
        }
    }
}
