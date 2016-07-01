<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class DataExportController extends Controller
{
    public function index()
    {
        return view("excel_export.index");
    }

    public function export(Request $request)
    {
        
        return view("excel_export.index");
    }
}
