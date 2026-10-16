<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index');
    }

    public function dashboard()
    {
        $usuariosPorEstado = Usuario::estadosDeUsuario();

        return view('home.dashboard', compact('usuariosPorEstado'));
    }
}
