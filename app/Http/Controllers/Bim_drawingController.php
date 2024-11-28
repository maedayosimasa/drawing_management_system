<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bim_drawing;

class Bim_drawingController extends Controller
{
    public function create(){
        return view('bim_drawing.create');
    }
    public function store(Request $request) {
        $post = Bim_drawing::create([
            'drawing_id' => $request->drawing_id,
            'bim_drawing_name' => $request->bim_drawing_name,
        ]);
        return back();
    }
}
