<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting_log;

class Meeting_logController extends Controller
{
    public function create() {
        return view('meeting_log.create');
    }
    public function store(Request $request) {
        $post = Meeting_log::create([
            'project_name_id' => $request->project_name_id,
            'meeting_log_name' => $request->meeting_log_name
        ]);
        return back();
    }
}
