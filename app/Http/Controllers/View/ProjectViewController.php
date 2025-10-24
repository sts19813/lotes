<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectViewController extends Controller
{
    public function index()
    {
        return view('api.projects.index');
    }
}
