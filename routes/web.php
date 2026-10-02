<?php

use App\Http\Controllers\AlergiaController;
use App\Http\Controllers\CondicionMedicaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TipoSanguineoController;
use App\Http\Controllers\EspecialidadController;

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


// Rutas iniciales de autenticación:

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'rol'])->group(function () {

    // Ruta de entrada principal
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // NOTA A RECORDAD: AGRUPAR RUTAS POR ROLES

    // Rutas de roles
    Route::get('roles', [RolController::class, 'index'])->name('roles');
    Route::post('roles', [RolController::class, 'insertarRol'])->name('roles.insertar');
    Route::get('roles/{id}/buscar', [RolController::class, 'buscarRolXId'])->name('roles.buscar');
    Route::put('roles/{rol}', [RolController::class, 'actualizarRol'])->name('roles.actualizar');
    Route::delete('roles/{rol}', [RolController::class, 'eliminarRol'])->name('roles.eliminar');

    // Rutas de estados
    Route::get('estados', [EstadoController::class, 'index'])->name('estados');
    Route::post('estados', [EstadoController::class, 'insertarEstado'])->name('estados.insertar');
    Route::get('estados/{id}/buscar', [EstadoController::class, 'buscarEstadoXId'])->name('estados.buscar');
    Route::put('estados/{estado}', [EstadoController::class, 'actualizarEstado'])->name('estados.actualizar');
    Route::delete('estados/{estado}', [EstadoController::class, 'eliminarEstado'])->name('estados.eliminar');

    // Rutas de tipos sanguíneos
    Route::get('tipos-sanguineos', [TipoSanguineoController::class, 'index'])->name('tipos-sanguineos');
    Route::post('tipos-sanguineos', [TipoSanguineoController::class, 'insertarTipoSanguineo'])->name('tipos-sanguineos.insertar');
    Route::get('tipos-sanguineos/{id}/buscar', [TipoSanguineoController::class, 'buscarTipoSanguineoXId'])->name('tipos-sanguineos.buscar');
    Route::put('tipos-sanguineos/{tipoSanguineo}', [TipoSanguineoController::class, 'actualizarTipoSanguineo'])->name('tipos-sanguineos.actualizar');
    Route::delete('tipos-sanguineos/{tipoSanguineo}', [TipoSanguineoController::class, 'eliminarTipoSanguineo'])->name('tipos-sanguineos.eliminar');

    // Rutas de especialidades
    Route::get('especialidades', [EspecialidadController::class, 'index'])->name('especialidades');
    Route::post('especialidades', [EspecialidadController::class, 'insertarEspecialidad'])->name('especialidades.insertar');
    Route::get('especialidades/{id}/buscar', [EspecialidadController::class, 'buscarEspecialidadXId'])->name('especialidades.buscar');
    Route::put('especialidades/{especialidad}', [EspecialidadController::class, 'actualizarEspecialidad'])->name('especialidades.actualizar');
    Route::delete('especialidades/{especialidad}', [EspecialidadController::class, 'eliminarEspecialidad'])->name('especialidades.eliminar');

    // Rutas de diagnósticos
    Route::get('diagnosticos', [DiagnosticoController::class, 'index'])->name('diagnosticos');
    Route::post('diagnosticos', [DiagnosticoController::class, 'insertarDiagnostico'])->name('diagnosticos.insertar');
    Route::get('diagnosticos/{id}/buscar', [DiagnosticoController::class, 'buscarDiagnosticoXId'])->name('diagnosticos.buscar');
    Route::put('diagnosticos/{diagnostico}', [DiagnosticoController::class, 'actualizarDiagnostico'])->name('diagnosticos.actualizar');
    Route::delete('diagnosticos/{diagnostico}', [DiagnosticoController::class, 'eliminarDiagnostico'])->name('diagnosticos.eliminar');

    // Rutas de condiciones médicas
    Route::get('condiciones-medicas', [CondicionMedicaController::class, 'index'])->name('condiciones-medicas');
    Route::post('condiciones-medicas', [CondicionMedicaController::class, 'insertarCondicionMedica'])->name('condiciones-medicas.insertar');
    Route::get('condiciones-medicas/{id}/buscar', [CondicionMedicaController::class, 'buscarCondicionMedicaXId'])->name('condiciones-medicas.buscar');
    Route::put('condiciones-medicas/{condicionMedica}', [CondicionMedicaController::class, 'actualizarCondicionMedica'])->name('condiciones-medicas.actualizar');
    Route::delete('condiciones-medicas/{condicionMedica}', [CondicionMedicaController::class, 'eliminarCondicionMedica'])->name('condiciones-medicas.eliminar');

    // Rutas de alergias
    Route::get('alergias', [AlergiaController::class, 'index'])->name('alergias');
    Route::post('alergias', [AlergiaController::class, 'insertarAlergia'])->name('alergias.insertar');
    Route::get('alergias/{id}/buscar', [AlergiaController::class, 'buscarAlergiaXId'])->name('alergias.buscar');
    Route::put('alergias/{alergia}', [AlergiaController::class, 'actualizarAlergia'])->name('alergias.actualizar');
    Route::delete('alergias/{alergia}', [AlergiaController::class, 'eliminarAlergia'])->name('alergias.eliminar');

    // Rutas de medicamentos
    Route::get('medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos');
    Route::post('medicamentos', [MedicamentoController::class, 'insertarMedicamento'])->name('medicamentos.insertar');
    Route::get('medicamentos/{id}/buscar', [MedicamentoController::class, 'buscarMedicamentoXId'])->name('medicamentos.buscar');
    Route::put('medicamentos/{medicamento}', [MedicamentoController::class, 'actualizarMedicamento'])->name('medicamentos.actualizar');
    Route::delete('medicamentos/{medicamento}', [MedicamentoController::class, 'eliminarMedicamento'])->name('medicamentos.eliminar');

    // Rutas de servicios
    Route::get('servicios', [ServicioController::class, 'index'])->name('servicios');
    Route::post('servicios', [ServicioController::class, 'insertarServicio'])->name('servicios.insertar');
    Route::get('servicios/{id}/buscar', [ServicioController::class, 'buscarServicioXId'])->name('servicios.buscar');
    Route::put('servicios/{servicio}', [ServicioController::class, 'actualizarServicio'])->name('servicios.actualizar');
    Route::delete('servicios/{servicio}', [ServicioController::class, 'eliminarServicio'])->name('servicios.eliminar');

    // Rutas de usuarios
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::post('usuarios', [UsuarioController::class, 'insertarUsuario'])->name('usuarios.insertar');
    Route::get('usuarios/{id}/buscar', [UsuarioController::class, 'buscarUsuarioXId'])->name('usuarios.buscar');
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');



    // ============== Fin de rutas protegidas ================================
});

