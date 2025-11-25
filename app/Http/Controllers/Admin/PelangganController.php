<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        return view('admin.pelanggan.index');
    }

    public function create()
    {
        return view('admin.pelanggan.create');
    }

    public function store(Request $request) { }
    public function show(string $id) { }
    public function edit(string $id)
{
    return view('admin.pelanggan.edit');
}
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }
}