<?php

namespace App\Http\Controllers;

use App\CsvData;
use Illuminate\Http\Request;

class CSVDataController extends Controller
{
    public function index(){
        $files = CsvData::paginate(env('PAGE_SIZE'));
        return view('csvdata.index', compact('files') );
    }
}
