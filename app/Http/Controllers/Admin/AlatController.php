<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index()
    {
        return view('admin.alat.index', ['mediums' => []]);
    }
    public function create()
    {
        return view('admin.alat.create');
    }
    public function stok()
    {
        return view('admin.alat.stok');
    }

    public function store(Request $request) { }
    public function show(string $id) { }
    public function edit(string $id) { 
        return view('admin.alat.edit');
    }
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }
}