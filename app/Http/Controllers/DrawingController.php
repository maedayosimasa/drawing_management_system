<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drawing;

class DrawingController extends Controller
{
   public function create() {
    return view('drawing.create');
   }

   public function store(Request $request) {
    $post = Drawing::create([
              'project_name_id' => $request->project_name_id
    ]);
    return back();
   }

      //一覧画面表示
   public function index() {
      $posts = Drawing::all();
      return view('drawing.index', compact('posts'));
   }
}
