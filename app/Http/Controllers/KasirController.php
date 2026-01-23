<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    // Alias for Transaksi POS
    public function index() {
        return redirect()->route('transaksi.create');
    }
}
