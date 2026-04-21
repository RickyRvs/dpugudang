<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        if (session('user_role') !== 'pimpinan') abort(403);

        $query = AuditLog::with('user')->latest();
        if ($request->modul)  $query->where('modul', $request->modul);
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->dari)   $query->whereDate('created_at', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('created_at', '<=', $request->sampai);

        $logs  = $query->paginate(30);
        $users = \App\Models\User::orderBy('nama')->get();
        return view('audit.index', compact('logs', 'users'));
    }
}