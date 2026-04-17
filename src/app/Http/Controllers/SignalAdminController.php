<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use Illuminate\Http\Request;

class SignalAdminController extends Controller
{
    public function index(Request $request)
    {
        $signals = Signal::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.signals.index', compact('signals'));
    }
}
