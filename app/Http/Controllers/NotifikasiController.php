<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function readAll()
    {
        Notifikasi::where('user_id', session('user_id'))->where('dibaca', false)->update(['dibaca' => true]);
        return back();
    }
}