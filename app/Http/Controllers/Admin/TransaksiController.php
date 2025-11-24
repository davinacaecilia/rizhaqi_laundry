<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('admin.transaksi.index', ['arts' => []]);
    }

    public function create()
    {
        return view('admin.transaksi.create');
    }
    public function status()
    {
        return view('admin.transaksi.status');
    }

    public function store(Request $request) { }
    public function show(string $id)
    {
        return view('admin.transaksi.show');
    }

    public function edit(string $id)
    {
        return view('admin.transaksi.edit');
    }
    
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }

}