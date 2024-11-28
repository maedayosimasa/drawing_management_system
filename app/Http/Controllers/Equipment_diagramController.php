<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment_diagram;

class Equipment_diagramController extends Controller
{
    public function create() {
        return view('equipment_diagram.create');
    }

    public function store(Request $request) {
        $post = Equipment_diagram::create([
            'drawing_id' => $request->drawing_id,
            'machinery_equipment_diagram_all_name' => $request->machinery_equipment_diagram_all_name
        ]);
        return back();
    }
}
