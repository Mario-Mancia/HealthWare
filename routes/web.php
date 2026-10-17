<?php

use App\Http\Controllers\AlergiaController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CondicionMedicaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HorarioDoctorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\PacienteCondicionController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\RegistroMedicoController;
use App\Http\Controllers\RegistroMedicoDiagnosticoController;
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

    // Rutas de HOME
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('home/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora');

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


    //Rutas de doctores
    Route::get('doctores', [DoctorController::class, 'index'])->name('doctores');
    Route::post('doctores', [DoctorController::class, 'insertarDoctor'])->name('doctores.insertar');
    Route::get('doctores/{id}/buscar', [DoctorController::class, 'buscarDoctorXId'])->name('doctores.buscar');
    Route::put('doctores/{doctor}', [DoctorController::class, 'actualizarDoctor'])->name('doctores.actualizar');

    // Rutas de horarios de doctores
    Route::get('horarios', [HorarioDoctorController::class, 'index'])->name('horarios');
    Route::post('horarios', [HorarioDoctorController::class, 'insertarHorario'])->name('horarios.insertar');
    Route::get('horarios/{id}/buscar', [HorarioDoctorController::class, 'buscarHorarioXId'])->name('horarios.buscar');
    Route::put('horarios/{horario}', [HorarioDoctorController::class, 'actualizarHorario'])->name('horarios.actualizar');
    Route::delete('horarios/{horario}', [HorarioDoctorController::class, 'eliminarHorario'])->name('horarios.eliminar');

    // Rutas de pacientes
    Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes');
    Route::post('pacientes', [PacienteController::class, 'insertarPaciente'])->name('pacientes.insertar');
    Route::get('pacientes/{id}/buscar', [PacienteController::class, 'buscarPacienteXId'])->name('pacientes.buscar');
    Route::put('pacientes/{paciente}', [PacienteController::class, 'actualizarPaciente'])->name('pacientes.actualizar');
    Route::delete('pacientes/{paciente}', [PacienteController::class, 'eliminarPaciente'])->name('pacientes.eliminar');

    // Rutas de citas
    Route::get('citas', [CitaController::class, 'index'])->name('citas');
    Route::post('citas', [CitaController::class, 'insertarCita'])->name('citas.insertar');
    Route::get('citas/{id}/buscar', [CitaController::class, 'buscarCitaXId'])->name('citas.buscar');
    Route::put('citas/{cita}', [CitaController::class, 'actualizarCita'])->name('citas.actualizar');
    Route::delete('citas/{cita}', [CitaController::class, 'eliminarCita'])->name('citas.eliminar');

    // Rutas de registros médicos
    Route::get('registros-medicos', [RegistroMedicoController::class, 'index',])->name('registros_medicos');
    Route::post('registros-medicos', [RegistroMedicoController::class, 'insertarRegistroMedico',])->name('registros_medicos.insertar');
    Route::get('registros-medicos/{id}/buscar', [RegistroMedicoController::class, 'buscarRegistroMedicoXId',])->name('registros_medicos.buscar');
    Route::delete('registros-medicos/{registroMedico}', [RegistroMedicoController::class, 'eliminarRegistroMedico',])->name('registros_medicos.eliminar');

    // Rutas de registros medicos diagnosticos
    Route::get('registro-medico-diagnosticos', [RegistroMedicoDiagnosticoController::class, 'index',])->name('registro_medico_diagnosticos');
    Route::post('registro-medico-diagnosticos', [RegistroMedicoDiagnosticoController::class, 'insertarRegistroMedicoDiagnostico',])->name('registro_medico_diagnosticos.insertar');
    Route::get('registro-medico-diagnosticos/{id}/buscar', [RegistroMedicoDiagnosticoController::class, 'buscarRegistroMedicoDiagnosticoXId',])->name('registro_medico_diagnosticos.buscar');
    Route::delete('registro-medico-diagnosticos/{registroMedicoDiagnostico}', [RegistroMedicoDiagnosticoController::class, 'eliminarRegistroMedicoDiagnostico',])->name('registro_medico_diagnosticos.eliminar');

    // Rutas de pacientes condiciones
    Route::get('/pacientes-condiciones', [PacienteCondicionController::class, 'index'])->name('pacientes_condiciones');
    Route::post('/pacientes-condiciones/insertar', [PacienteCondicionController::class, 'insertarPacienteCondicion'])->name('pacientes_condiciones.insertar');
    Route::get('/pacientes-condiciones/buscar/{id}', [PacienteCondicionController::class, 'buscarPacienteCondicion'])->name('pacientes_condiciones.buscar');
    Route::delete('/pacientes-condiciones/eliminar/{pacienteCondicion}', [PacienteCondicionController::class, 'eliminarPacienteCondicion'])->name('pacientes_condiciones.eliminar');

    // ============== Fin de rutas protegidas ================================
});

