<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\FooterSection;

class FooterController extends Controller
{
    public function index()
    {
        return response()->json(FooterSection::orderBy('order')->get());
    }
}
