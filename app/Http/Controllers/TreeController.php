<?php

namespace App\Http\Controllers;

use App\Models\Tree;


class TreeController extends Controller
{
    public function index()
    {
       $tree = Tree::get()->toTree();

       return view('tree',compact('tree'));
    }

}
