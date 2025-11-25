<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        return view('admin.pegawai.index');
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    public function store(Request $request) { }
    public function show(string $id) { }
    public function edit(string $id)
{
    return view('admin.pegawai.edit');
}
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }
}