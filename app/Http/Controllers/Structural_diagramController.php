<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structural_diagram;

class Structural_diagramController extends Controller
{
    public function create() {
        return view('structural_diagram.create');
    }

    public function store(Request $request) {
        $post = Structural_diagram::create([
            'drawing_id' => $request->drawing_id,
            'floor_plan_name' => $request->floor_plan_name,
        ]);
        return back();
    }
}
