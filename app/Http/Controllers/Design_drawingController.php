<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Design_drawing;

class Design_drawingController extends Controller
{
    public function create() {
        return view('design_drawing.create');
    }

    public function store(Request $request) {
        $post =Design_drawing::create([
            'drawing_id' => $request->drawing_id,
            'finishing_table_name' => $request->finishing_table_name,
        ]);
        return back();
    }
}
