<?php

use App\Http\Controllers\AlergiaController;
use App\Http\Controllers\AnuncioGlobalController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CondicionMedicaController;
use App\Http\Controllers\DetalleFacturaController;
use App\Http\Controllers\DetalleRecetaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HorarioDoctorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PacienteAlergiaController;
use App\Http\Controllers\PacienteCondicionController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\RegistroMedicoController;
use App\Http\Controllers\RegistroMedicoDiagnosticoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TipoSanguineoController;
use App\Http\Controllers\EspecialidadController;

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// RUTAS DE AUTENTICACIÓN
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,3');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// RUTAS PROTEGIDAS
Route::middleware(['auth', 'rol', 'no-cache'])->group(function () {

    // ACCESO GENERAL: LOS TRES ROLES
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('home/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // ADMINISTRADOR
    Route::middleware('rol:Administrador')->group(function () {

        // Bitácora
        Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora');

        // Roles
        Route::get('roles', [RolController::class, 'index'])->name('roles');
        Route::post('roles', [RolController::class, 'insertarRol'])->name('roles.insertar');
        Route::get('roles/{id}/buscar', [RolController::class, 'buscarRolXId'])->name('roles.buscar');
        Route::put('roles/{rol}', [RolController::class, 'actualizarRol'])->name('roles.actualizar');
        Route::delete('roles/{rol}', [RolController::class, 'eliminarRol'])->name('roles.eliminar');

        // Estados
        Route::get('estados', [EstadoController::class, 'index'])->name('estados');
        Route::post('estados', [EstadoController::class, 'insertarEstado'])->name('estados.insertar');
        Route::get('estados/{id}/buscar', [EstadoController::class, 'buscarEstadoXId'])->name('estados.buscar');
        Route::put('estados/{estado}', [EstadoController::class, 'actualizarEstado'])->name('estados.actualizar');
        Route::delete('estados/{estado}', [EstadoController::class, 'eliminarEstado'])->name('estados.eliminar');

        // Usuarios
        Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::post('usuarios', [UsuarioController::class, 'insertarUsuario'])->name('usuarios.insertar');
        Route::get('usuarios/{id}/buscar', [UsuarioController::class, 'buscarUsuarioXId'])->name('usuarios.buscar');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
        Route::delete('usuarios/{usuario}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');

        // Administración de catálogos
        // Tipos sanguíneos
        Route::post('tipos-sanguineos', [TipoSanguineoController::class, 'insertarTipoSanguineo'])->name('tipos-sanguineos.insertar');
        Route::put('tipos-sanguineos/{tipoSanguineo}', [TipoSanguineoController::class, 'actualizarTipoSanguineo'])->name('tipos-sanguineos.actualizar');
        Route::delete('tipos-sanguineos/{tipoSanguineo}', [TipoSanguineoController::class, 'eliminarTipoSanguineo'])->name('tipos-sanguineos.eliminar');

        // Especialidades
        Route::post('especialidades', [EspecialidadController::class, 'insertarEspecialidad'])->name('especialidades.insertar');
        Route::put('especialidades/{especialidad}', [EspecialidadController::class, 'actualizarEspecialidad'])->name('especialidades.actualizar');
        Route::delete('especialidades/{especialidad}', [EspecialidadController::class, 'eliminarEspecialidad'])->name('especialidades.eliminar');

        // Diagnósticos
        Route::post('diagnosticos', [DiagnosticoController::class, 'insertarDiagnostico'])->name('diagnosticos.insertar');
        Route::put('diagnosticos/{diagnostico}', [DiagnosticoController::class, 'actualizarDiagnostico'])->name('diagnosticos.actualizar');
        Route::delete('diagnosticos/{diagnostico}', [DiagnosticoController::class, 'eliminarDiagnostico'])->name('diagnosticos.eliminar');

        // Condiciones médicas
        Route::post('condiciones-medicas', [CondicionMedicaController::class, 'insertarCondicionMedica'])->name('condiciones-medicas.insertar');
        Route::put('condiciones-medicas/{condicionMedica}', [CondicionMedicaController::class, 'actualizarCondicionMedica'])->name('condiciones-medicas.actualizar');
        Route::delete('condiciones-medicas/{condicionMedica}', [CondicionMedicaController::class, 'eliminarCondicionMedica'])->name('condiciones-medicas.eliminar');

        // Alergias
        Route::post('alergias', [AlergiaController::class, 'insertarAlergia'])->name('alergias.insertar');
        Route::put('alergias/{alergia}', [AlergiaController::class, 'actualizarAlergia'])->name('alergias.actualizar');
        Route::delete('alergias/{alergia}', [AlergiaController::class, 'eliminarAlergia'])->name('alergias.eliminar');

        // Medicamentos
        Route::post('medicamentos.css', [MedicamentoController::class, 'insertarMedicamento'])->name('medicamentos.css.insertar');
        Route::put('medicamentos.css/{medicamento}', [MedicamentoController::class, 'actualizarMedicamento'])->name('medicamentos.css.actualizar');
        Route::delete('medicamentos.css/{medicamento}', [MedicamentoController::class, 'eliminarMedicamento'])->name('medicamentos.css.eliminar');

        // Servicios
        Route::post('servicios', [ServicioController::class, 'insertarServicio'])->name('servicios.insertar');
        Route::put('servicios/{servicio}', [ServicioController::class, 'actualizarServicio'])->name('servicios.actualizar');
        Route::delete('servicios/{servicio}', [ServicioController::class, 'eliminarServicio'])->name('servicios.eliminar');

        // Doctores
        Route::post('doctores', [DoctorController::class, 'insertarDoctor'])->name('doctores.insertar');
        Route::put('doctores/{doctor}', [DoctorController::class, 'actualizarDoctor'])->name('doctores.actualizar');

        // Eliminación de horarios
        Route::delete('horarios/{horario}', [HorarioDoctorController::class, 'eliminarHorario'])->name('horarios.eliminar');

        // Administración de anuncios globales
        Route::post('/anuncios-globales/insertar', [AnuncioGlobalController::class, 'insertar'])->name('anuncios_globales.insertar');
        Route::put('/anuncios-globales/actualizar/{id}', [AnuncioGlobalController::class, 'actualizar'])->name('anuncios_globales.actualizar');

    });

    // ADMINISTRADOR Y MÉDICO
    Route::middleware('rol:Administrador,Médico')->group(function () {

        // Diagnósticos: consulta
        Route::get('diagnosticos', [DiagnosticoController::class, 'index'])->name('diagnosticos');
        Route::get('diagnosticos/{id}/buscar', [DiagnosticoController::class, 'buscarDiagnosticoXId'])->name('diagnosticos.buscar');

        // Condiciones médicas: consulta
        Route::get('condiciones-medicas', [CondicionMedicaController::class, 'index'])->name('condiciones-medicas');
        Route::get('condiciones-medicas/{id}/buscar', [CondicionMedicaController::class, 'buscarCondicionMedicaXId'])->name('condiciones-medicas.buscar');

        // Alergias: consulta
        Route::get('alergias', [AlergiaController::class, 'index'])->name('alergias');
        Route::get('alergias/{id}/buscar', [AlergiaController::class, 'buscarAlergiaXId'])->name('alergias.buscar');

        // Medicamentos: consulta
        Route::get('medicamentos.css', [MedicamentoController::class, 'index'])->name('medicamentos.css');
        Route::get('medicamentos.css/{id}/buscar', [MedicamentoController::class, 'buscarMedicamentoXId'])->name('medicamentos.css.buscar');

        // Registros médicos
        Route::get('registros-medicos', [RegistroMedicoController::class, 'index'])->name('registros_medicos');
        Route::post('registros-medicos', [RegistroMedicoController::class, 'insertarRegistroMedico'])->name('registros_medicos.insertar');
        Route::get('registros-medicos/{id}/buscar', [RegistroMedicoController::class, 'buscarRegistroMedicoXId'])->name('registros_medicos.buscar');
        Route::delete('registros-medicos/{registroMedico}', [RegistroMedicoController::class, 'eliminarRegistroMedico'])->name('registros_medicos.eliminar');

        // Diagnósticos asociados a registros médicos
        Route::get('registro-medico-diagnosticos', [RegistroMedicoDiagnosticoController::class, 'index'])->name('registro_medico_diagnosticos');
        Route::post('registro-medico-diagnosticos', [RegistroMedicoDiagnosticoController::class, 'insertarRegistroMedicoDiagnostico'])->name('registro_medico_diagnosticos.insertar');
        Route::get('registro-medico-diagnosticos/{id}/buscar', [RegistroMedicoDiagnosticoController::class, 'buscarRegistroMedicoDiagnosticoXId'])->name('registro_medico_diagnosticos.buscar');
        Route::delete('registro-medico-diagnosticos/{registroMedicoDiagnostico}', [RegistroMedicoDiagnosticoController::class, 'eliminarRegistroMedicoDiagnostico'])->name('registro_medico_diagnosticos.eliminar');

        // Condiciones de pacientes
        Route::get('/pacientes-condiciones', [PacienteCondicionController::class, 'index'])->name('pacientes_condiciones');
        Route::post('/pacientes-condiciones/insertar', [PacienteCondicionController::class, 'insertarPacienteCondicion'])->name('pacientes_condiciones.insertar');
        Route::get('/pacientes-condiciones/buscar/{id}', [PacienteCondicionController::class, 'buscarPacienteCondicion'])->name('pacientes_condiciones.buscar');
        Route::delete('/pacientes-condiciones/eliminar/{pacienteCondicion}', [PacienteCondicionController::class, 'eliminarPacienteCondicion'])->name('pacientes_condiciones.eliminar');

        // Alergias de pacientes
        Route::get('/pacientes-alergias', [PacienteAlergiaController::class, 'index'])->name('pacientes_alergias');
        Route::post('/pacientes-alergias/insertar', [PacienteAlergiaController::class, 'insertarPacienteAlergia'])->name('pacientes_alergias.insertar');
        Route::get('/pacientes-alergias/buscar/{id}', [PacienteAlergiaController::class, 'buscarPacienteAlergia'])->name('pacientes_alergias.buscar');
        Route::delete('/pacientes-alergias/eliminar/{pacienteAlergia}', [PacienteAlergiaController::class, 'eliminarPacienteAlergia'])->name('pacientes_alergias.eliminar');

        // Recetas
        Route::get('/recetas', [RecetaController::class, 'index'])->name('recetas');
        Route::post('/recetas/insertar', [RecetaController::class, 'insertarReceta'])->name('recetas.insertar');
        Route::get('/recetas/buscar/{id}', [RecetaController::class, 'buscarReceta'])->name('recetas.buscar');

        // Detalle de recetas
        Route::get('/detalle-recetas', [DetalleRecetaController::class, 'index'])->name('detalle_recetas');
        Route::post('/detalle-recetas/insertar', [DetalleRecetaController::class, 'insertarDetalleReceta'])->name('detalle_recetas.insertar');
        Route::get('/detalle-recetas/buscar/{id}', [DetalleRecetaController::class, 'buscarDetalleReceta'])->name('detalle_recetas.buscar');

    });

    // ADMINISTRADOR Y RECEPCIONISTA
    Route::middleware('rol:Administrador,Recepcionista')->group(function () {

        // Gestión de pacientes
        Route::post('pacientes', [PacienteController::class, 'insertarPaciente'])->name('pacientes.insertar');
        Route::put('pacientes/{paciente}', [PacienteController::class, 'actualizarPaciente'])->name('pacientes.actualizar');
        Route::delete('pacientes/{paciente}', [PacienteController::class, 'eliminarPaciente'])->name('pacientes.eliminar');

        // Gestión de citas
        Route::post('citas', [CitaController::class, 'insertarCita'])->name('citas.insertar');
        Route::put('citas/{cita}', [CitaController::class, 'actualizarCita'])->name('citas.actualizar');
        Route::delete('citas/{cita}', [CitaController::class, 'eliminarCita'])->name('citas.eliminar');

        // Facturas
        Route::post('/facturas/insertar', [FacturaController::class, 'insertarFactura'])->name('facturas.insertar');

        // Detalles de facturas
        Route::post('/detalle-facturas/insertar', [DetalleFacturaController::class, 'insertarDetalleFactura'])->name('detalle_facturas.insertar');

    });

    // LOS TRES ROLES: CONSULTAS Y OPERACIONES COMUNES
    Route::middleware('rol:Administrador,Médico,Recepcionista')->group(function () {

        // Tipos sanguíneos: consulta
        Route::get('tipos-sanguineos', [TipoSanguineoController::class, 'index'])->name('tipos-sanguineos');
        Route::get('tipos-sanguineos/{id}/buscar', [TipoSanguineoController::class, 'buscarTipoSanguineoXId'])->name('tipos-sanguineos.buscar');

        // Especialidades: consulta
        Route::get('especialidades', [EspecialidadController::class, 'index'])->name('especialidades');
        Route::get('especialidades/{id}/buscar', [EspecialidadController::class, 'buscarEspecialidadXId'])->name('especialidades.buscar');

        // Servicios: consulta
        Route::get('servicios', [ServicioController::class, 'index'])->name('servicios');
        Route::get('servicios/{id}/buscar', [ServicioController::class, 'buscarServicioXId'])->name('servicios.buscar');

        // Doctores: consulta
        Route::get('doctores', [DoctorController::class, 'index'])->name('doctores');
        Route::get('doctores/{id}/buscar', [DoctorController::class, 'buscarDoctorXId'])->name('doctores.buscar');

        // Horarios de doctores: consulta
        Route::get('horarios', [HorarioDoctorController::class, 'index'])->name('horarios');
        Route::get('horarios/{id}/buscar', [HorarioDoctorController::class, 'buscarHorarioXId'])->name('horarios.buscar');

        // Pacientes: consulta
        Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes');
        Route::get('pacientes/{id}/buscar', [PacienteController::class, 'buscarPacienteXId'])->name('pacientes.buscar');

        // Citas: consulta
        Route::get('citas', [CitaController::class, 'index'])->name('citas');
        Route::get('citas/{id}/buscar', [CitaController::class, 'buscarCitaXId'])->name('citas.buscar');

        // Facturas: consulta
        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas');
        Route::get('/facturas/todos', [FacturaController::class, 'mostrarTodos'])->name('facturas.todos');
        Route::get('/facturas/buscar/{id}', [FacturaController::class, 'buscarFactura'])->name('facturas.buscar');

        // Detalles de facturas: consulta
        Route::get('/detalle-facturas', [DetalleFacturaController::class, 'index'])->name('detalle_facturas');
        Route::get('/detalle-facturas/todos', [DetalleFacturaController::class, 'mostrarTodos'])->name('detalle_facturas.todos');
        Route::get('/detalle-facturas/buscar/{id}', [DetalleFacturaController::class, 'buscarDetalleFactura'])->name('detalle_facturas.buscar');

        // Notificaciones: consulta
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones');
        Route::get('/notificaciones/todos', [NotificacionController::class, 'mostrarTodos'])->name('notificaciones.todos');
        Route::get('/notificaciones/buscar/{id}', [NotificacionController::class, 'buscar'])->name('notificaciones.buscar');

        // Anuncios globales: consulta
        Route::get('/anuncios-globales', [AnuncioGlobalController::class, 'index'])->name('anuncios_globales');
        Route::get('/anuncios-globales/todos', [AnuncioGlobalController::class, 'mostrarTodos'])->name('anuncios_globales.todos');
        Route::get('/anuncios-globales/buscar/{id}', [AnuncioGlobalController::class, 'buscar'])->name('anuncios_globales.buscar');

    });

    // ADMINISTRADOR Y MÉDICO: GESTIÓN DE HORARIOS
    Route::middleware('rol:Administrador,Médico')->group(function () {

        Route::post('horarios', [HorarioDoctorController::class, 'insertarHorario'])->name('horarios.insertar');
        Route::put('horarios/{horario}', [HorarioDoctorController::class, 'actualizarHorario'])->name('horarios.actualizar');

    });

    // ADMINISTRADOR: GESTIÓN DE NOTIFICACIONES
    Route::middleware('rol:Administrador')->group(function () {

        Route::post('/notificaciones/insertar', [NotificacionController::class, 'insertar'])->name('notificaciones.insertar');
        Route::put('/notificaciones/actualizar/{id}', [NotificacionController::class, 'actualizar'])->name('notificaciones.actualizar');

    });

});
