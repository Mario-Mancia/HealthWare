#!/usr/bin/env bash
set -Eeuo pipefail

readonly VERDE='\033[0;32m'
readonly AMARILLO='\033[0;33m'
readonly ROJO='\033[0;31m'
readonly AZUL='\033[0;34m'
readonly RESET='\033[0m'

info()  { printf "${AZUL}[INFO]${RESET} %s\n" "$*"; }
ok()    { printf "${VERDE}[OK]${RESET} %s\n" "$*"; }
aviso() { printf "${AMARILLO}[AVISO]${RESET} %s\n" "$*"; }
error() { printf "${ROJO}[ERROR]${RESET} %s\n" "$*" >&2; exit 1; }

if [[ "${1:-}" == "-h" || "${1:-}" == "--help" || "${1:-}" == "--ayuda" ]]; then
    cat <<'AYUDA'
HEALTHWARE · REESTRUCTURACION VISUAL (Bootstrap + Material Design 3 + componentes tipo Compose)

Uso:
  ./reestructurar-healthware-ui.sh [RAIZ_DEL_PROYECTO]

Se ejecuta al mismo nivel que las carpetas public/ y resources/ (o se le pasa la ruta).

Que hace:
  - Respalda public/css, public/js y resources/views en .respaldos-ui/<fecha>/.
  - Reemplaza el sistema de diseno (conserva la paleta de HealthWare) y los layouts:
      * Layout interno: boton de menu a la izquierda que oculta/despliega el menu lateral, nombre del sistema al centro,
        accesos rapidos, notificaciones con contador, tema y menu de usuario. Menu por roles desde un solo archivo
        (resources/views/layouts/partials/menu_config.php).
      * Layout de acceso/invitado y login con el mismo lenguaje visual.
  - Reestructura las 20 vistas de modulos SIN tocar la logica: clases hw-* unificadas, buscador en tiempo real,
    filtros, modales con scroll interno (ya no tapan los botones), etiquetas flotantes corregidas en selectores y fechas,
    badges y formularios por secciones.
  - Usuarios: al elegir el rol "Medico" se despliega la seccion del perfil de doctor (doctor[...]).
  - Facturacion: indicadores, exportacion a PDF (aparece si existe la ruta facturas.pdf), emision con pago unico
    (aparece si existe la ruta facturas.insertar) y detalle imprimible.
  - NO modifica el dashboard ni los toasts.
Requisitos: bash, python3.
AYUDA
    exit 0
fi

command -v python3 >/dev/null 2>&1 || error "Se necesita python3."

RAIZ="$(cd "${1:-.}" && pwd)"
[[ -d "$RAIZ/public" && -d "$RAIZ/resources/views" ]] \
    || error "No se encontraron public/ y resources/views en $RAIZ. Ejecuta el script al mismo nivel que esas carpetas."
[[ -f "$RAIZ/resources/views/layouts/app_layout.blade.php" || -f "$RAIZ/resources/views/layouts/partials/menu_config.php" ]] \
    || error "No existe resources/views/layouts/app_layout.blade.php."

info "Proyecto: $RAIZ"

# ------------------------------------------------------------
# Respaldo
# ------------------------------------------------------------
RESPALDO="$RAIZ/.respaldos-ui/$(date +%Y%m%d-%H%M%S)"
mkdir -p "$RESPALDO"
cp -r "$RAIZ/public/css" "$RESPALDO/public-css"
cp -r "$RAIZ/public/js" "$RESPALDO/public-js"
cp -r "$RAIZ/resources/views" "$RESPALDO/resources-views"
ok "Respaldo creado en ${RESPALDO#"$RAIZ"/}"

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

ARCHIVOS=0
escribir() {
    local destino="$RAIZ/$1"
    mkdir -p "$(dirname "$destino")"
    cat > "$destino"
    ARCHIVOS=$((ARCHIVOS + 1))
    printf "  ${VERDE}+${RESET} %s\n" "$1"
}

# ------------------------------------------------------------
# Programas auxiliares (python3)
# ------------------------------------------------------------
cat > "$TMP/extraer_menu.py" <<'__HW_PY__'
#!/usr/bin/env python3
"""Extrae la configuración del menú del app_layout actual a layouts/partials/menu_config.php (una sola fuente)."""
import sys
from pathlib import Path

raiz = Path(sys.argv[1])
layout = raiz / "resources/views/layouts/app_layout.blade.php"
destino = raiz / "resources/views/layouts/partials/menu_config.php"

if destino.exists():
    print("menu_config.php ya existe: se conserva")
    sys.exit(0)

texto = layout.read_text(encoding="utf-8")
ini = texto.find("$rolesTodos = [")
if ini == -1:
    print("ERROR: no se encontró la configuración del menú ($rolesTodos) en app_layout.blade.php")
    sys.exit(2)
fin = texto.find("@endphp", ini)
bloque = texto[ini:fin].rstrip()
bloque = "\n".join(l[4:] if l.startswith("    ") else l for l in bloque.split("\n"))
bloque = "    " + bloque if not bloque.startswith("$") else bloque

php = """<?php

/*
|--------------------------------------------------------------------------
| HealthWare · Configuración única del menú de navegación
|--------------------------------------------------------------------------
| La usan el menú lateral, los accesos rápidos de la barra superior y la
| pantalla de inicio. Cada enlace declara los roles que pueden verlo
| (en minúsculas y con tilde: administrador, médico, recepcionista).
|
| Para agregar un módulo: añade un grupo en $menuModulos y, si quieres un
| acceso directo en la barra superior, agrégalo a $accesosRapidos.
*/

""" + bloque.lstrip() + """

/*
|--------------------------------------------------------------------------
| Accesos rápidos de la barra superior (menú extra a la izquierda)
|--------------------------------------------------------------------------
*/
$accesosRapidos = [
    ['ruta' => 'pacientes', 'etiqueta' => 'Pacientes', 'icono' => 'groups', 'roles' => $rolesTodos],
    ['ruta' => 'citas', 'etiqueta' => 'Citas médicas', 'icono' => 'calendar_month', 'roles' => $rolesTodos],
    ['ruta' => 'doctores', 'etiqueta' => 'Doctores', 'icono' => 'medical_services', 'roles' => $rolesTodos],
    ['ruta' => 'facturas', 'etiqueta' => 'Facturación', 'icono' => 'receipt_long', 'roles' => $rolesAdminRecepcionista],
    ['ruta' => 'registros_medicos', 'etiqueta' => 'Expedientes clínicos', 'icono' => 'clinical_notes', 'roles' => $rolesAdminMedico],
];

return [
    'modulos' => $menuModulos,
    'accesos_rapidos' => $accesosRapidos,
];
"""
destino.parent.mkdir(parents=True, exist_ok=True)
destino.write_text(php, encoding="utf-8")
print("menu_config.php creado desde el menú actual")
__HW_PY__
cat > "$TMP/reestructurar.py" <<'__HW_PY__'
#!/usr/bin/env python3
"""
HealthWare - Reestructuración visual de las vistas Blade y sus scripts.

Solo modifica marcado (clases, atributos, bloques HTML y enlaces a CSS).
NO toca directivas Blade, PHP, rutas, nombres de campos ni ids que usan los JS.
Es idempotente: se puede ejecutar varias veces.
"""
import re
import sys
from pathlib import Path

RAIZ = Path(sys.argv[1]).resolve() if len(sys.argv) > 1 else Path.cwd()
VISTAS = RAIZ / "resources" / "views"
PUBLIC = RAIZ / "public"
CSS = PUBLIC / "css"
JS = PUBLIC / "js"

# CSS que se conservan; el resto son los CSS por módulo que reemplaza hw-components.css
CSS_CONSERVADOS = {
    "global-styles.css", "app-layout.css", "hw-components.css", "login.css",
    "toast.css", "sidebar-modulos.css", "dashboard.css",
}

informe = {"vistas": [], "js": [], "stubs": [], "avisos": [], "css_eliminados": []}


def leer(ruta):
    return ruta.read_text(encoding="utf-8")


def escribir(ruta, texto):
    ruta.write_text(texto, encoding="utf-8")


def aviso(msg):
    informe["avisos"].append(msg)


# ---------------------------------------------------------------------------
# Piezas de marcado
# ---------------------------------------------------------------------------
def toolbar(selects, extra_filtros=()):
    """Barra de búsqueda en tiempo real + filtros por columna."""
    filtros = "\n".join(
        f'''                <select class="form-select" data-hw-filtro-columna="{col}" aria-label="Filtrar por {etiqueta.lower()}">
                    <option value="">{etiqueta}: todos</option>
                </select>'''
        for col, etiqueta in extra_filtros
    )
    selects_html = "\n".join(f"                {s.strip()}" for s in selects)
    partes = [
        '''            <div class="hw-toolbar">
                <div class="hw-search">
                    <span class="material-symbols-outlined">search</span>
                    <input type="search" class="form-control" data-hw-buscar placeholder="Buscar en la tabla…" aria-label="Buscar en la tabla" autocomplete="off">
                    <button type="button" class="btn btn-icon hw-search-clear" data-hw-buscar-limpiar aria-label="Limpiar búsqueda">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>'''
    ]
    if selects_html.strip():
        partes.append(selects_html)
    if filtros:
        partes.append(filtros)
    partes.append('''                <span class="hw-toolbar-count" data-hw-contador></span>
                <button type="button" class="btn btn-text hw-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>
''')
    return "\n".join(partes)


SIN_RESULTADOS = '''
            <div class="hw-no-results">
                <span class="material-symbols-outlined">search_off</span>
                No se encontraron coincidencias con la búsqueda.
            </div>'''

FILTROS_PROPIOS = {
    # carpeta de la vista -> filtros por columna manejados por hw-ui.js (el JS del módulo no los trae)
    "usuarios": [(3, "Rol"), (4, "Estado")],
}


def seccion_medico(p):
    """Sección extra del formulario de usuario para el rol Médico (perfil de doctor)."""
    return f'''
                        {{{{-- PERFIL PROFESIONAL (solo si el rol es Médico) --}}}}
                        <div class="hw-form-section hw-section-collapsible" data-doctor-section hidden>

                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">stethoscope</span>

                                <div>
                                    <h3>Perfil profesional del médico</h3>
                                    <p>Se registra junto con la cuenta; sin este perfil el médico no podría usar sus funciones.</p>
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="doctor[especialidad_id]" id="{p}_doctor_especialidad_id" class="form-select" disabled>
                                            <option value="" selected></option>
                                            @foreach($especialidadesMedico as $especialidadMedico)
                                                <option value="{{{{ $especialidadMedico->id }}}}">{{{{ $especialidadMedico->especialidad }}}}</option>
                                            @endforeach
                                        </select>
                                        <label for="{p}_doctor_especialidad_id">Especialidad</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="doctor[numero_licencia]" id="{p}_doctor_numero_licencia"
                                               class="form-control" placeholder="Número de licencia" maxlength="50" disabled>
                                        <label for="{p}_doctor_numero_licencia">Número de licencia</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="doctor[dui]" id="{p}_doctor_dui"
                                               class="form-control" placeholder="DUI" maxlength="10" inputmode="numeric" disabled>
                                        <label for="{p}_doctor_dui">DUI</label>
                                    </div>
                                    <div class="hw-field-hint">Formato 00000000-0.</div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="doctor[fecha_nacimiento]" id="{p}_doctor_fecha_nacimiento"
                                               class="form-control" placeholder="Fecha de nacimiento" disabled>
                                        <label for="{p}_doctor_fecha_nacimiento">Fecha de nacimiento</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="doctor[sexo]" id="{p}_doctor_sexo" class="form-select" disabled>
                                            <option value="" selected></option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                        <label for="{p}_doctor_sexo">Sexo</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="doctor[descripcion_profesional]" id="{p}_doctor_descripcion_profesional"
                                                  class="form-control" placeholder="Descripción profesional" maxlength="500" disabled></textarea>
                                        <label for="{p}_doctor_descripcion_profesional">Descripción profesional (opcional)</label>
                                    </div>
                                </div>

                            </div>

                        </div>

'''


# ---------------------------------------------------------------------------
# Renombrado de clases por módulo -> hw-*
# ---------------------------------------------------------------------------
def prefijo_de(texto):
    m = re.search(r'class="([a-z][a-z-]*)-page"', texto)
    return m.group(1) if m else None


def renombrar(texto, prefijo):
    patron = re.compile(r"(?<![\w./-])" + re.escape(prefijo) + r"-(?=[a-z])")
    return patron.sub("hw-", texto)


def quedan_prefijos(texto, prefijo):
    return re.findall(r"(?<![\w./-])" + re.escape(prefijo) + r"-[a-z][\w-]*", texto)


def quitar_enlaces_css(texto):
    def es_modulo(m):
        nombre = m.group(1) + ".css"
        return nombre not in CSS_CONSERVADOS

    salida = []
    for linea in texto.split("\n"):
        m = re.search(r"asset\('css/([A-Za-z0-9_-]+)\.css'\)", linea)
        if m and "<link" in linea and es_modulo(m):
            continue
        salida.append(linea)
    return "\n".join(salida)


def rutas_js(texto):
    return re.findall(r"asset\('(js/[A-Za-z0-9_/.-]+\.js)'\)", texto)


# ---------------------------------------------------------------------------
# Transformación de las vistas index de cada módulo
# ---------------------------------------------------------------------------
def transformar_index(carpeta, ruta):
    t = leer(ruta)
    original = t
    prefijo = prefijo_de(t)

    if prefijo == "hw":
        informe["vistas"].append(f"{carpeta}: ya reestructurada")
        return

    if prefijo is None:
        if 'class="hw-page"' in t:
            informe["vistas"].append(f"{carpeta}: ya reestructurada")
            return
        aviso(f"{carpeta}/index.blade.php: no se encontró el contenedor *-page; se omite.")
        return

    t = renombrar(t, prefijo)
    t = re.sub(r"(?<![\w-])hw-table(?![\w-])", "hw-data-table", t)
    restos = quedan_prefijos(t, prefijo)
    if restos:
        aviso(f"{carpeta}: quedaron clases con prefijo '{prefijo}-': {sorted(set(restos))[:6]}")

    t = quitar_enlaces_css(t)

    # --- barra de búsqueda / filtros ---
    if "data-hw-buscar" not in t:
        existente = re.search(r'[ \t]*<div class="hw-toolbar">(.*?)</div>\n?', t, re.S)
        extra = FILTROS_PROPIOS.get(carpeta, ())
        if existente:
            selects = re.findall(r"<select.*?</select>", existente.group(1), re.S)
            nueva = toolbar(selects, extra)
            t = t[:existente.start()] + nueva + t[existente.end():]
        else:
            ancla = '<div class="table-responsive hw-table-container">'
            if ancla in t:
                sangria = re.search(r"([ \t]*)" + re.escape(ancla), t).group(1)
                nueva = toolbar([], extra)
                t = t.replace(sangria + ancla, nueva + "\n" + sangria + ancla, 1)
            else:
                aviso(f"{carpeta}: no se encontró la tabla para insertar el buscador.")

    # --- mensaje de "sin coincidencias" (modo sin DataTables) ---
    if "hw-no-results" not in t:
        i = t.find('<div class="table-responsive hw-table-container">')
        if i != -1:
            j = t.find("</table>", i)
            k = t.find("</div>", j) if j != -1 else -1
            if k != -1:
                k += len("</div>")
                t = t[:k] + SIN_RESULTADOS + t[k:]

    # --- JS del módulo: renombrar las pocas clases que usa ---
    for rel in rutas_js(t):
        archivo = PUBLIC / rel
        if archivo.exists():
            js = leer(archivo)
            nuevo = renombrar(js, prefijo)
            if nuevo != js:
                escribir(archivo, nuevo)
                informe["js"].append(rel)

    if carpeta == "usuarios":
        t = adaptar_usuarios(t)
    if carpeta == "facturacion":
        t = adaptar_facturacion(t)

    if t != original:
        escribir(ruta, t)
    informe["vistas"].append(f"{carpeta}: prefijo '{prefijo}-' -> 'hw-'")


# ---------------------------------------------------------------------------
# Usuarios: sección del Médico + data-rol + scripts
# ---------------------------------------------------------------------------
def adaptar_usuarios(t):
    # a) cada opción de rol lleva su nombre normalizado
    if 'data-rol="' not in t:
        reemplazo_rol = '<option value="{{ $rol->id }}" data-rol="{{ \\Illuminate\\Support\\Str::of($rol->rol)->ascii()->lower() }}"'
        t, n = re.subn(r'<option value="\{\{ \$rol->id \}\}"', lambda m: reemplazo_rol, t)
        if n != 2:
            aviso(f"usuarios: se esperaban 2 listas de roles y se encontraron {n}.")

    # b) lista de especialidades disponible para la sección
    if "$especialidadesMedico" not in t.split("@section('contenido')")[1][:1500]:
        bloque = """@section('contenido')

    @php
        // Especialidades para el perfil del médico (si el controlador no las envía, se consultan aquí)
        $especialidadesMedico = collect();
        try {
            $especialidadesMedico = $especialidades ?? (
                class_exists(\\App\\Models\\Especialidad::class)
                    ? \\App\\Models\\Especialidad::query()->orderBy('especialidad')->get()
                    : collect()
            );
        } catch (\\Throwable $e) {
            $especialidadesMedico = collect();
        }
    @endphp
"""
        t = t.replace("@section('contenido')\n", bloque, 1)

    # c) sección del perfil profesional antes de "SEGURIDAD" (modal nuevo y modal editar)
    if "data-doctor-section" not in t:
        marca = "{{-- SEGURIDAD --}}"
        pos = [m.start() for m in re.finditer(re.escape(marca), t)]
        if len(pos) == 2:
            # se inserta de atrás hacia adelante para no desplazar las posiciones
            lineas_ini = [t.rfind("\n", 0, p) + 1 for p in pos]
            t = t[:lineas_ini[1]] + seccion_medico("editar").lstrip("\n") + t[lineas_ini[1]:]
            t = t[:lineas_ini[0]] + seccion_medico("nuevo").lstrip("\n") + t[lineas_ini[0]:]
        else:
            aviso(f"usuarios: se esperaban 2 secciones SEGURIDAD y se encontraron {len(pos)}.")

    # d) script de la sección del médico
    if "usuarios-medico.js" not in t:
        t = t.replace(
            "<script src=\"{{ asset('js/usuario/usuarios.js') }}\"></script>",
            "<script src=\"{{ asset('js/usuario/usuarios.js') }}\"></script>\n    <script src=\"{{ asset('js/usuario/usuarios-medico.js') }}\"></script>",
            1,
        )

    # e) soporte de nombres anidados (doctor.dui -> doctor[dui]) en la lectura de errores de usuarios.js
    js = JS / "usuario" / "usuarios.js"
    if js.exists():
        texto = leer(js)
        buscado = "formulario.find(`[name=\"${nombre}\"]`)"
        nuevo = "formulario.find(`[name=\"${String(nombre).replace(/\\.([^.]+)/g, '[$1]')}\"]`)"
        if buscado in texto:
            escribir(js, texto.replace(buscado, nuevo))
            informe["js"].append("js/usuario/usuarios.js (errores de campos anidados)")
    return t


# ---------------------------------------------------------------------------
# Facturación: estadísticas, PDF, detalle y emisión con pago único
# ---------------------------------------------------------------------------
MODAL_EMITIR = '''
    {{-- MODAL: EMITIR FACTURA CON PAGO ÚNICO (solo si existe la ruta facturas.insertar) --}}
    @if(\\Illuminate\\Support\\Facades\\Route::has('facturas.insertar') && in_array($rolActual, ['administrador', 'recepcionista']))
    <div class="modal fade hw-modal" id="modalEmitir" tabindex="-1" aria-labelledby="tituloEmitir" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="hw-modal-header">
                    <div class="hw-modal-heading">
                        <div class="hw-modal-icon"><span class="material-symbols-outlined">receipt_long</span></div>
                        <div>
                            <h2 class="hw-modal-title" id="tituloEmitir">Emitir factura</h2>
                            <p class="hw-modal-subtitle">La factura se emite y se cobra en un solo paso.</p>
                        </div>
                    </div>
                    <button type="button" class="hw-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('facturas.insertar') }}" novalidate data-hw-factura>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body hw-modal-body">

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">person</span>
                                <div>
                                    <h3>Paciente y emisión</h3>
                                    <p>A quién se factura y en qué fecha.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <select name="id_paciente" id="nuevo_id_paciente" class="form-select" required>
                                            <option value="" disabled selected></option>
                                            @foreach(($pacientes ?? collect()) as $pacienteFactura)
                                                <option value="{{ $pacienteFactura->id }}">{{ trim(($pacienteFactura->nombres ?? '') . ' ' . ($pacienteFactura->apellidos ?? '')) }}</option>
                                            @endforeach
                                        </select>
                                        <label for="nuevo_id_paciente">Paciente</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="date" name="fecha_emision" id="nuevo_fecha_emision" class="form-control"
                                               value="{{ now()->format('Y-m-d') }}" placeholder="Fecha de emisión" required>
                                        <label for="nuevo_fecha_emision">Fecha de emisión</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">list_alt</span>
                                <div>
                                    <h3>Conceptos facturados</h3>
                                    <p>Servicios prestados, con cantidad y precio unitario.</p>
                                </div>
                            </div>

                            <div class="hw-invoice-lines" data-hw-lineas></div>

                            <button type="button" class="btn btn-tonal mt-3" data-hw-agregar-linea>
                                <span class="material-symbols-outlined">add</span>
                                Agregar concepto
                            </button>

                            <template id="hwLineaPlantilla">
                                <div class="hw-invoice-line" data-hw-linea>
                                    <div class="form-floating">
                                        <select class="form-select" name="items[__I__][id_servicio]" data-hw-servicio required>
                                            <option value="" disabled selected></option>
                                            @foreach(($servicios ?? collect()) as $servicioFactura)
                                                <option value="{{ $servicioFactura->id }}"
                                                        data-precio="{{ $servicioFactura->precio ?? $servicioFactura->precio_base ?? 0 }}">
                                                    {{ $servicioFactura->servicio ?? $servicioFactura->nombre ?? ('Servicio #' . $servicioFactura->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label>Servicio</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="items[__I__][cantidad]" min="1" step="1" value="1" placeholder="Cantidad" data-hw-cantidad required>
                                        <label>Cantidad</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="items[__I__][precio_unitario]" min="0" step="0.01" placeholder="Precio unitario" data-hw-precio required>
                                        <label>Precio unitario</label>
                                    </div>
                                    <button type="button" class="btn btn-icon" data-hw-quitar-linea aria-label="Quitar concepto">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">payments</span>
                                <div>
                                    <h3>Pago único</h3>
                                    <p>El pago se registra al emitir la factura.</p>
                                </div>
                            </div>
                            <div class="row g-3 align-items-start">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="metodo_pago" id="nuevo_metodo_pago" class="form-select" required>
                                            <option value="" disabled selected></option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                        </select>
                                        <label for="nuevo_metodo_pago">Método de pago</label>
                                    </div>
                                    <div class="form-floating mt-3">
                                        <input type="text" name="referencia_pago" id="nuevo_referencia_pago" class="form-control" placeholder="Referencia" maxlength="100">
                                        <label for="nuevo_referencia_pago">Referencia (opcional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="hw-invoice-summary" aria-live="polite">
                                        <div><span>Subtotal</span><span data-hw-subtotal>$ 0.00</span></div>
                                        <div><span>Total a cobrar</span><span data-hw-total>$ 0.00</span></div>
                                    </div>
                                    <input type="hidden" name="subtotal" data-hw-campo-subtotal value="0">
                                    <input type="hidden" name="total" data-hw-campo-total value="0">
                                    <input type="hidden" name="monto_pagado" data-hw-campo-pagado value="0">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="hw-modal-footer">
                        <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">task_alt</span>
                            Emitir y registrar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

'''


def adaptar_facturacion(t):
    # a) botón "Emitir factura" (solo si la ruta existe y el rol lo permite)
    if "#modalEmitir" not in t:
        boton = '''
            @if(\\Illuminate\\Support\\Facades\\Route::has('facturas.insertar') && in_array($rolActual, ['administrador', 'recepcionista']))
                <button type="button" class="btn btn-primary hw-create-button" data-bs-toggle="modal" data-bs-target="#modalEmitir">
                    <span class="material-symbols-outlined">add_circle</span>
                    Emitir factura
                </button>
            @endif
'''
        t = t.replace("        </header>", boton + "        </header>", 1)

    # b) más indicadores: pagadas y pendientes
    if "Pagadas" not in t:
        extra = '''          <div class="hw-stat">
            <span class="material-symbols-outlined">task_alt</span>
            <div><strong>{{ $lista->filter(fn ($f) => in_array(strtolower((string) $f->estado), ['pagada', 'pagado']))->count() }}</strong><span>Pagadas</span></div>
          </div>
          <div class="hw-stat">
            <span class="material-symbols-outlined">pending_actions</span>
            <div><strong>{{ $lista->filter(fn ($f) => in_array(strtolower((string) $f->estado), ['pendiente', 'emitida']))->count() }}</strong><span>Pendientes</span></div>
          </div>
'''
        t = re.sub(r"(<div class=\"hw-stats\">.*?)(\n        </div>)", lambda m: m.group(1) + "\n" + extra.rstrip("\n") + m.group(2), t, count=1, flags=re.S)

    # c) acciones por fila: detalle y PDF (guardadas por Route::has)
    if "facturas.pdf" not in t:
        acciones = '''
                                    @if(\\Illuminate\\Support\\Facades\\Route::has('detalle_facturas'))
                                        <a href="{{ route('detalle_facturas', $item->id) }}" class="hw-action-button"
                                           title="Ver factura completa" aria-label="Ver factura completa">
                                            <span class="material-symbols-outlined">description</span>
                                        </a>
                                    @endif
                                    @if(\\Illuminate\\Support\\Facades\\Route::has('facturas.pdf'))
                                        <a href="{{ route('facturas.pdf', $item->id) }}" class="hw-action-button"
                                           title="Exportar a PDF" aria-label="Exportar a PDF" target="_blank" rel="noopener">
                                            <span class="material-symbols-outlined">picture_as_pdf</span>
                                        </a>
                                    @endif'''
        patron = re.compile(r'(<span class="material-symbols-outlined">visibility</span>\s*</button>)(\s*</div>\s*</td>)')
        t, n = patron.subn(lambda m: m.group(1) + acciones + m.group(2), t, count=1)
        if n != 1:
            aviso("facturacion: no se encontró la celda de acciones para agregar detalle/PDF.")

    # d) modal de emisión
    if 'id="modalEmitir"' not in t:
        marca = "    {{-- MODAL: CONSULTA --}}"
        if marca in t:
            t = t.replace(marca, MODAL_EMITIR.rstrip("\n") + "\n\n" + marca, 1)
        else:
            aviso("facturacion: no se encontró el modal de consulta para ubicar el de emisión.")

    # e) script de líneas de factura
    if "hw-facturacion.js" not in t:
        t = t.replace(
            "<script src=\"{{ asset('js/facturacion/facturas.js') }}\"></script>",
            "<script src=\"{{ asset('js/facturacion/facturas.js') }}\"></script>\n    <script src=\"{{ asset('js/hw/hw-facturacion.js') }}\"></script>",
            1,
        )
    return t


# ---------------------------------------------------------------------------
# Vistas secundarias aún sin implementar -> pantalla base uniforme
# ---------------------------------------------------------------------------
STUBS = {
    "citas/detalle": ("Detalle de cita", "event_note", "Detalle de la cita", "Información contextual de la cita y acciones según su estado."),
    "doctores/perfil": ("Perfil del doctor", "badge", "Perfil profesional", "Datos profesionales, horarios y agenda del doctor."),
    "pacientes/expediente": ("Expediente del paciente", "clinical_notes", "Expediente clínico", "Historial, diagnósticos, alergias, condiciones y recetas del paciente."),
    "pacientes/perfil": ("Perfil del paciente", "person", "Perfil del paciente", "Información personal y administrativa del paciente."),
}


def transformar_stub(clave, ruta, titulo_vista, icono, titulo, subtitulo):
    t = leer(ruta)
    if "hw-placeholder" in t:
        return
    espec = re.search(r"\{\{--.*?--\}\}", t, re.S)
    scripts = re.search(r"@push\('scripts'\).*?@endpush", t, re.S)
    seccion = re.search(r"@section\('titulo',[^)]*\)", t)
    contenido = f"""@extends('layouts.app_layout')

{seccion.group(0) if seccion else "@section('titulo', '" + titulo_vista + "')"}

@section('contenido')

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">{icono}</span>
                </div>
                <div>
                    <h1 class="hw-title">{titulo}</h1>
                    <p class="hw-subtitle">{subtitulo}</p>
                </div>
            </div>
        </header>

        <section class="hw-surface">
            <div class="hw-placeholder">
                <span class="material-symbols-outlined">construction</span>
                <h2>Pantalla pendiente de conectar</h2>
                <p>
                    El diseño base ya usa el layout y los componentes de HealthWare.
                    Falta definir la ruta y la acción del controlador que cargan sus datos.
                </p>
            </div>
        </section>

    </div>

    {espec.group(0) if espec else ""}

@endsection

{scripts.group(0) if scripts else ""}
"""
    escribir(ruta, contenido)
    informe["stubs"].append(clave)


DETALLE_FACTURA = r"""@extends('layouts.app_layout')

@section('titulo', 'Detalle de factura')

@section('contenido')

    @php
        // La factura la entrega el controlador (relaciones esperadas: paciente, items y pagos).
        $factura = $factura ?? null;
        $fmt = fn ($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';

        $lineas = collect($factura->items ?? $factura->facturaItems ?? $factura->detalles ?? []);
        $pagos = collect($factura->pagos ?? []);
        $estadoFactura = strtolower((string) ($factura->estado ?? ''));
        $claseEstado = ['pagada' => 'success', 'pagado' => 'success', 'pendiente' => 'warning', 'anulada' => 'error', 'cancelada' => 'error', 'emitida' => 'info'][$estadoFactura] ?? 'neutral';
        $nombrePaciente = trim(($factura->paciente?->nombres ?? '') . ' ' . ($factura->paciente?->apellidos ?? ''));

        $subtotalFactura = $lineas->sum(fn ($i) => (float) ($i->subtotal ?? ((float) ($i->cantidad ?? 0) * (float) ($i->precio_unitario ?? 0))));
        $totalFactura = (float) ($factura->total ?? $subtotalFactura);
        $pagadoFactura = (float) $pagos->sum('monto');
    @endphp

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon"><span class="material-symbols-outlined">receipt_long</span></div>
                <div>
                    <h1 class="hw-title">Detalle de factura</h1>
                    <p class="hw-subtitle">Documento de la factura, sus conceptos y su pago.</p>
                </div>
            </div>

            <div class="hw-document-actions">
                @if(\Illuminate\Support\Facades\Route::has('facturas'))
                    <a href="{{ route('facturas') }}" class="btn btn-tonal">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Volver al listado
                    </a>
                @endif

                @if($factura)
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <span class="material-symbols-outlined">print</span>
                        Imprimir
                    </button>

                    @if(\Illuminate\Support\Facades\Route::has('facturas.pdf'))
                        <a href="{{ route('facturas.pdf', $factura->id) }}" class="btn btn-primary" target="_blank" rel="noopener">
                            <span class="material-symbols-outlined">picture_as_pdf</span>
                            Exportar a PDF
                        </a>
                    @endif
                @endif
            </div>
        </header>

        @if(! $factura)

            <section class="hw-surface">
                <div class="hw-empty">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h3>No se encontró la factura</h3>
                    <p>El controlador todavía no envió la factura a esta vista.</p>
                </div>
            </section>

        @else

            <article class="hw-document">

                <div class="hw-document-head">
                    <div class="hw-document-brand">
                        <span class="material-symbols-outlined">health_and_safety</span>
                        <div>
                            <strong>HealthWare</strong>
                            <span>Sistema de Gestión Clínica</span>
                        </div>
                    </div>

                    <div class="hw-document-meta">
                        <h2>Factura {{ $factura->numero_factura ?? ('#' . $factura->id) }}</h2>
                        <p>Emitida el {{ $fmt($factura->fecha_emision) }}</p>
                        <span class="hw-status hw-status-{{ $claseEstado }}">
                            <span class="hw-status-dot"></span>{{ ucfirst(str_replace('_', ' ', $estadoFactura ?: 'sin estado')) }}
                        </span>
                    </div>
                </div>

                <div class="hw-document-parties">
                    <div class="hw-document-party">
                        <h3>Emisor</h3>
                        <p><strong>HealthWare</strong></p>
                        <p>Sistema de Gestión Clínica</p>
                    </div>
                    <div class="hw-document-party">
                        <h3>Paciente</h3>
                        <p><strong>{{ $nombrePaciente ?: '—' }}</strong></p>
                        @if(! empty($factura->paciente?->dui))<p>DUI: {{ $factura->paciente->dui }}</p>@endif
                        @if(! empty($factura->paciente?->telefono))<p>Tel.: {{ $factura->paciente->telefono }}</p>@endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table hw-data-table align-middle">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lineas as $linea)
                                @php
                                    $cantidadLinea = (float) ($linea->cantidad ?? 0);
                                    $precioLinea = (float) ($linea->precio_unitario ?? 0);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="hw-fullname">{{ $linea->servicio?->servicio ?? $linea->descripcion ?? 'Servicio' }}</span>
                                        @if(! empty($linea->descripcion) && ($linea->servicio?->servicio ?? null))
                                            <span class="hw-identity-caption d-block">{{ $linea->descripcion }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ rtrim(rtrim(number_format($cantidadLinea, 2), '0'), '.') }}</td>
                                    <td class="text-end">$ {{ number_format($precioLinea, 2) }}</td>
                                    <td class="text-end">$ {{ number_format((float) ($linea->subtotal ?? $cantidadLinea * $precioLinea), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center hw-muted py-4">Sin conceptos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="hw-document-totals">
                    <div><span>Subtotal</span><span>$ {{ number_format($subtotalFactura, 2) }}</span></div>
                    @if($pagos->isNotEmpty())
                        <div><span>Pagado</span><span>$ {{ number_format($pagadoFactura, 2) }}</span></div>
                    @endif
                    <div class="is-total"><span>Total</span><span>$ {{ number_format($totalFactura, 2) }}</span></div>
                </div>

                @if($pagos->isNotEmpty())
                    <h3 class="h6 mt-4 mb-2">Pago registrado</h3>
                    <div class="table-responsive">
                        <table class="table hw-data-table align-middle mb-0">
                            <thead><tr><th>Fecha</th><th>Método</th><th>Referencia</th><th class="text-end">Monto</th></tr></thead>
                            <tbody>
                                @foreach($pagos as $pago)
                                    <tr>
                                        <td>{{ $fmt($pago->fecha_pago ?? $pago->created_at) }}</td>
                                        <td>{{ ucfirst((string) ($pago->metodo_pago ?? $pago->metodo ?? '—')) }}</td>
                                        <td>{{ $pago->referencia ?? $pago->referencia_pago ?? '—' }}</td>
                                        <td class="text-end">$ {{ number_format((float) ($pago->monto ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </article>

        @endif

    </div>

    {{--
        VISTA: Detalle individual de una factura (imprimible).
        Variables esperadas: $factura con relaciones paciente, items (o facturaItems/detalles) y pagos.
        La exportación a PDF se activa sola cuando exista la ruta nombrada "facturas.pdf".
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
@endpush
"""


def escribir_detalle_factura():
    ruta = VISTAS / "facturacion" / "detalle.blade.php"
    if not ruta.parent.exists():
        return
    if ruta.exists() and "hw-document" in leer(ruta):
        return
    escribir(ruta, DETALLE_FACTURA)
    informe["stubs"].append("facturacion/detalle (documento imprimible completo)")


# ---------------------------------------------------------------------------
# Programa principal
# ---------------------------------------------------------------------------
def main():
    if not VISTAS.is_dir() or not PUBLIC.is_dir():
        print("ERROR: no se encontraron resources/views ni public en", RAIZ)
        sys.exit(1)

    for index in sorted(VISTAS.glob("*/index.blade.php")):
        carpeta = index.parent.name
        if carpeta in {"home", "login", "components", "layouts"}:
            continue
        transformar_index(carpeta, index)

    for clave, (titulo_vista, icono, titulo, subtitulo) in STUBS.items():
        ruta = VISTAS / (clave + ".blade.php")
        if ruta.exists():
            transformar_stub(clave, ruta, titulo_vista, icono, titulo, subtitulo)

    escribir_detalle_factura()

    # CSS por módulo: ya no se usan (los reemplaza hw-components.css)
    for archivo in sorted(CSS.glob("*.css")):
        if archivo.name not in CSS_CONSERVADOS:
            archivo.unlink()
            informe["css_eliminados"].append(archivo.name)

    print("VISTAS TRANSFORMADAS:", len(informe["vistas"]))
    for linea in informe["vistas"]:
        print("  -", linea)
    print("VISTAS SECUNDARIAS:", ", ".join(informe["stubs"]) or "ninguna")
    print("JS AJUSTADOS:", len(informe["js"]))
    print("CSS ELIMINADOS:", len(informe["css_eliminados"]))
    for linea in informe["avisos"]:
        print("AVISO:", linea)


main()
__HW_PY__

info "Extrayendo la configuracion del menu actual"
python3 "$TMP/extraer_menu.py" "$RAIZ" || error "No se pudo extraer el menu de app_layout.blade.php."

info "Escribiendo sistema de diseno, scripts y layouts"
escribir "public/css/global-styles.css" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · SISTEMA DE DISEÑO (Material Design 3 + Bootstrap 5.3)
   Paleta de HealthWare conservada. Componentes inspirados en 3DFS:
   botones en píldora, campos rellenos, tarjetas tonales, chips y
   superficies con elevación suave (estilo Jetpack Compose).
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap');

/* ---------- 1. TOKENS ---------- */
:root {
    color-scheme: light;
    --md-sys-typescale-font: 'Roboto', system-ui, -apple-system, 'Segoe UI', sans-serif;

    --md-sys-color-primary: #6750a4;
    --md-sys-color-on-primary: #ffffff;
    --md-sys-color-primary-container: #eaddff;
    --md-sys-color-on-primary-container: #21005d;

    --md-sys-color-secondary: #625b71;
    --md-sys-color-on-secondary: #ffffff;
    --md-sys-color-secondary-container: #e8def8;
    --md-sys-color-on-secondary-container: #1d192b;

    --md-sys-color-tertiary: #7d5260;
    --md-sys-color-on-tertiary: #ffffff;
    --md-sys-color-tertiary-container: #ffd8e4;
    --md-sys-color-on-tertiary-container: #31111d;

    --md-sys-color-error: #b3261e;
    --md-sys-color-on-error: #ffffff;
    --md-sys-color-error-container: #f9dedc;
    --md-sys-color-on-error-container: #410e0b;

    --md-sys-color-background: #fef7ff;
    --md-sys-color-on-background: #1d1b20;
    --md-sys-color-surface: #fef7ff;
    --md-sys-color-on-surface: #1d1b20;
    --md-sys-color-surface-variant: #e7e0ec;
    --md-sys-color-on-surface-variant: #49454f;

    --md-sys-color-surface-container-lowest: #ffffff;
    --md-sys-color-surface-container-low: #f7f2fa;
    --md-sys-color-surface-container: #f3edf7;
    --md-sys-color-surface-container-high: #ece6f0;
    --md-sys-color-surface-container-highest: #e6e0e9;

    --md-sys-color-brand-bg: #6750a4;
    --md-sys-color-on-brand-bg: #ffffff;

    --md-sys-color-outline: #79747e;
    --md-sys-color-outline-variant: #cac4d0;

    --md-sys-color-inverse-surface: #322f35;
    --md-sys-color-inverse-on-surface: #f5eff7;
    --md-sys-color-inverse-primary: #d0bcff;

    /* Estados semánticos (clínica) */
    --hw-success: #286b45;
    --hw-success-container: #d9f2e2;
    --hw-warning: #805b17;
    --hw-warning-container: #f8edce;
    --hw-info: #0b5cab;
    --hw-info-container: #d8e8fb;

    --md-sys-state-hover: rgba(103, 80, 164, 0.08);
    --md-sys-state-focus: rgba(103, 80, 164, 0.12);
    --md-sys-state-pressed: rgba(103, 80, 164, 0.12);
    --md-sys-state-dragged: rgba(103, 80, 164, 0.16);

    --md-sys-elevation-1: 0 1px 2px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08);
    --md-sys-elevation-2: 0 2px 6px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.08);
    --md-sys-elevation-3: 0 8px 24px rgba(29, 27, 32, 0.16), 0 2px 6px rgba(29, 27, 32, 0.08);

    --md-sys-shape-xs: 4px;
    --md-sys-shape-sm: 8px;
    --md-sys-shape-md: 12px;
    --md-sys-shape-lg: 16px;
    --md-sys-shape-xl: 28px;
    --md-sys-shape-full: 9999px;

    --md-sys-motion-duration-short: 150ms;
    --md-sys-motion-duration-medium: 250ms;
    --md-sys-motion-duration-long: 400ms;
    --md-sys-motion-easing: cubic-bezier(0.2, 0, 0, 1);

    --hw-topbar-height: 64px;
    --hw-sidebar-width: 288px;

    --bs-font-sans-serif: var(--md-sys-typescale-font);
    --bs-body-font-family: var(--md-sys-typescale-font);
    --bs-body-bg: var(--md-sys-color-background);
    --bs-body-color: var(--md-sys-color-on-background);
    --bs-primary: var(--md-sys-color-primary);
    --bs-primary-rgb: 103, 80, 164;
    --bs-secondary: var(--md-sys-color-secondary);
    --bs-secondary-rgb: 98, 91, 113;
    --bs-danger: var(--md-sys-color-error);
    --bs-danger-rgb: 179, 38, 30;
    --bs-border-color: var(--md-sys-color-outline-variant);
    --bs-border-radius: var(--md-sys-shape-md);
    --bs-border-radius-lg: var(--md-sys-shape-lg);
    --bs-link-color: var(--md-sys-color-primary);
    --bs-link-hover-color: var(--md-sys-color-primary);
}

:root[data-bs-theme="dark"] {
    color-scheme: dark;

    --md-sys-color-primary: #d0bcff;
    --md-sys-color-on-primary: #381e72;
    --md-sys-color-primary-container: #4f378b;
    --md-sys-color-on-primary-container: #eaddff;
    --md-sys-color-secondary: #ccc2dc;
    --md-sys-color-on-secondary: #332d41;
    --md-sys-color-secondary-container: #4a4458;
    --md-sys-color-on-secondary-container: #e8def8;
    --md-sys-color-tertiary: #efb8c8;
    --md-sys-color-on-tertiary: #492532;
    --md-sys-color-tertiary-container: #633b48;
    --md-sys-color-on-tertiary-container: #ffd8e4;
    --md-sys-color-error: #f2b8b5;
    --md-sys-color-on-error: #601410;
    --md-sys-color-error-container: #8c1d18;
    --md-sys-color-on-error-container: #f9dedc;
    --md-sys-color-background: #141218;
    --md-sys-color-on-background: #e6e0e9;
    --md-sys-color-surface: #141218;
    --md-sys-color-on-surface: #e6e0e9;
    --md-sys-color-surface-variant: #49454f;
    --md-sys-color-on-surface-variant: #cac4d0;
    --md-sys-color-surface-container-lowest: #0f0d13;
    --md-sys-color-surface-container-low: #1d1b20;
    --md-sys-color-surface-container: #211f26;
    --md-sys-color-surface-container-high: #2b2930;
    --md-sys-color-surface-container-highest: #36343b;
    --md-sys-color-brand-bg: #21005d;
    --md-sys-color-on-brand-bg: #eaddff;
    --md-sys-color-outline: #938f99;
    --md-sys-color-outline-variant: #49454f;
    --md-sys-color-inverse-surface: #e6e0e9;
    --md-sys-color-inverse-on-surface: #322f35;
    --md-sys-color-inverse-primary: #6750a4;

    --hw-success: #a6dcb9;
    --hw-success-container: #183b2a;
    --hw-warning: #f1d18b;
    --hw-warning-container: #493817;
    --hw-info: #a8cbf5;
    --hw-info-container: #173a63;

    --md-sys-state-hover: rgba(208, 188, 255, 0.08);
    --md-sys-state-focus: rgba(208, 188, 255, 0.12);
    --md-sys-state-pressed: rgba(208, 188, 255, 0.12);
    --md-sys-state-dragged: rgba(208, 188, 255, 0.16);
    --md-sys-elevation-1: 0 1px 2px rgba(0, 0, 0, 0.35), 0 1px 3px rgba(0, 0, 0, 0.25);
    --md-sys-elevation-2: 0 2px 6px rgba(0, 0, 0, 0.35), 0 1px 2px rgba(0, 0, 0, 0.25);
    --md-sys-elevation-3: 0 10px 28px rgba(0, 0, 0, 0.5), 0 2px 6px rgba(0, 0, 0, 0.3);

    --bs-primary-rgb: 208, 188, 255;
    --bs-secondary-rgb: 204, 194, 220;
    --bs-danger-rgb: 242, 184, 181;
}

/* ---------- 2. BASE ---------- */
*, *::before, *::after { box-sizing: border-box; }
html { min-height: 100%; scroll-behavior: smooth; }
body {
    min-height: 100vh;
    font-family: var(--md-sys-typescale-font);
    background-color: var(--md-sys-color-background);
    color: var(--md-sys-color-on-background);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    transition: background-color var(--md-sys-motion-duration-medium), color var(--md-sys-motion-duration-medium);
}
h1, h2, h3, h4, h5, h6 { color: var(--md-sys-color-on-surface); font-weight: 500; letter-spacing: -0.02em; }
p { color: var(--md-sys-color-on-surface-variant); line-height: 1.6; }
a { color: var(--md-sys-color-primary); text-decoration: none; transition: color var(--md-sys-motion-duration-short); }
a:hover { color: var(--md-sys-color-primary); }
hr { border-color: var(--md-sys-color-outline-variant); opacity: 1; }
::selection { color: var(--md-sys-color-on-primary); background-color: var(--md-sys-color-primary); }
:focus-visible { outline: 3px solid color-mix(in srgb, var(--md-sys-color-primary) 55%, transparent); outline-offset: 2px; }
button, input, select, textarea { font-family: inherit; }

.material-symbols-outlined {
    font-family: 'Material Symbols Outlined';
    font-weight: normal;
    font-style: normal;
    font-size: 24px;
    line-height: 1;
    letter-spacing: normal;
    text-transform: none;
    white-space: nowrap;
    word-wrap: normal;
    direction: ltr;
    font-feature-settings: 'liga';
    -webkit-font-feature-settings: 'liga';
    -webkit-font-smoothing: antialiased;
    user-select: none;
    vertical-align: middle;
}

/* ---------- 3. BOTONES (relleno · tonal · contorno · texto · icono) ---------- */
.btn {
    --bs-btn-border-radius: var(--md-sys-shape-full);
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    padding: 0.5rem 1.4rem;
    border-radius: var(--md-sys-shape-full);
    font-weight: 500;
    letter-spacing: 0.01em;
    transition: background-color var(--md-sys-motion-duration-short), color var(--md-sys-motion-duration-short),
        border-color var(--md-sys-motion-duration-short), box-shadow var(--md-sys-motion-duration-short),
        transform 120ms var(--md-sys-motion-easing);
}
.btn:active { transform: scale(0.98); }
.btn:focus-visible { outline: 3px solid color-mix(in srgb, var(--md-sys-color-primary) 55%, transparent); outline-offset: 2px; box-shadow: none; }
.btn .material-symbols-outlined { font-size: 1.2rem; }
.btn-sm { min-height: 34px; padding: 0.3rem 1rem; font-size: 0.85rem; }
.btn-lg { min-height: 48px; padding: 0.7rem 1.8rem; }

.btn-primary { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); border: 1px solid transparent; box-shadow: var(--md-sys-elevation-1); }
.btn-primary:hover, .btn-primary:focus { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); box-shadow: inset 0 0 0 100px var(--md-sys-state-hover), var(--md-sys-elevation-2); }
.btn-primary:active { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); box-shadow: inset 0 0 0 100px var(--md-sys-state-pressed); }
.btn-primary:disabled { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); opacity: .6; }

.btn-secondary { background-color: var(--md-sys-color-secondary); color: var(--md-sys-color-on-secondary); border-color: transparent; }
.btn-secondary:hover { background-color: var(--md-sys-color-secondary); color: var(--md-sys-color-on-secondary); box-shadow: inset 0 0 0 100px var(--md-sys-state-hover); }

.btn-outline-primary { background: transparent; color: var(--md-sys-color-primary); border: 1px solid var(--md-sys-color-outline); }
.btn-outline-primary:hover { background-color: var(--md-sys-state-hover); color: var(--md-sys-color-primary); border-color: var(--md-sys-color-primary); }

.btn-outline-danger { background: transparent; color: var(--md-sys-color-error); border: 1px solid var(--md-sys-color-error); }
.btn-outline-danger:hover { background-color: var(--md-sys-color-error-container); color: var(--md-sys-color-on-error-container); border-color: var(--md-sys-color-error); }

.btn-tonal { background-color: var(--md-sys-color-secondary-container); color: var(--md-sys-color-on-secondary-container); border: 1px solid transparent; }
.btn-tonal:hover, .btn-tonal:focus { background-color: var(--md-sys-color-secondary-container); color: var(--md-sys-color-on-secondary-container); box-shadow: inset 0 0 0 100px var(--md-sys-state-hover); }

.btn-text { background: transparent; color: var(--md-sys-color-primary); border: 1px solid transparent; padding: 0.5rem 1rem; }
.btn-text:hover { background-color: var(--md-sys-state-hover); color: var(--md-sys-color-primary); }

.btn-danger { background-color: var(--md-sys-color-error); color: var(--md-sys-color-on-error); border-color: transparent; }
.btn-danger:hover { background-color: var(--md-sys-color-error); color: var(--md-sys-color-on-error); box-shadow: inset 0 0 0 100px rgba(255, 255, 255, 0.1); }

.btn-icon { width: 40px; height: 40px; min-height: 40px; padding: 0; border-radius: var(--md-sys-shape-full); color: var(--md-sys-color-on-surface-variant); background: transparent; border: none; position: relative; }
.btn-icon:hover, .btn-icon:focus-visible { color: var(--md-sys-color-primary); background-color: var(--md-sys-state-hover); }

.btn-close { border-radius: 50%; }
:root[data-bs-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

/* ---------- 4. FORMULARIOS (campos rellenos M3 + etiquetas flotantes) ---------- */
.form-label { color: var(--md-sys-color-on-surface-variant); font-weight: 500; font-size: 0.85rem; margin-bottom: 0.35rem; }

.form-control, .form-select {
    min-height: 3.5rem;
    color: var(--md-sys-color-on-surface);
    background-color: var(--md-sys-color-surface-container-lowest);
    border: 1px solid var(--md-sys-color-outline);
    border-radius: var(--md-sys-shape-md);
    transition: border-color var(--md-sys-motion-duration-short), box-shadow var(--md-sys-motion-duration-short), background-color var(--md-sys-motion-duration-short);
}
:root[data-bs-theme="dark"] .form-control, :root[data-bs-theme="dark"] .form-select { background-color: var(--md-sys-color-surface-container); }
.form-control::placeholder { color: var(--md-sys-color-on-surface-variant); opacity: 0.7; }
.form-control:hover:not(:disabled):not(:focus), .form-select:hover:not(:disabled):not(:focus) { border-color: var(--md-sys-color-on-surface-variant); }
.form-control:focus, .form-select:focus {
    color: var(--md-sys-color-on-surface);
    background-color: var(--md-sys-color-surface-container-lowest);
    border-color: var(--md-sys-color-primary);
    box-shadow: 0 0 0 1px var(--md-sys-color-primary);
}
:root[data-bs-theme="dark"] .form-control:focus, :root[data-bs-theme="dark"] .form-select:focus { background-color: var(--md-sys-color-surface-container); }
.form-control:disabled, .form-select:disabled { background-color: var(--md-sys-color-surface-container); opacity: 0.6; }
.form-control.is-invalid, .form-select.is-invalid { border-color: var(--md-sys-color-error); box-shadow: 0 0 0 1px var(--md-sys-color-error); background-image: none; }
.form-control.is-valid, .form-select.is-valid { border-color: var(--hw-success); background-image: none; }
.invalid-feedback { color: var(--md-sys-color-error); font-size: 0.8rem; margin-top: 0.3rem; }
.form-text { color: var(--md-sys-color-on-surface-variant); }
textarea.form-control { min-height: 6rem; }
.form-select { background-position: right 0.9rem center; padding-right: 2.5rem; }
:root[data-bs-theme="dark"] .form-select { --bs-form-select-bg-img: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23cac4d0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e"); }

/* Etiquetas flotantes: la geometría de Bootstrap se conserva (el valor nunca queda bajo la etiqueta) */
.form-floating > .form-control,
.form-floating > .form-control-plaintext,
.form-floating > .form-select { height: 3.5rem; min-height: 3.5rem; padding: 1.625rem 0.9rem 0.5rem; line-height: 1.25; }
.form-floating > textarea.form-control { height: 7rem; min-height: 7rem; padding-top: 1.75rem; }
.form-floating > label { padding: 1rem 0.9rem; color: var(--md-sys-color-on-surface-variant); }
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label,
.form-floating > .form-control:-webkit-autofill ~ label,
.form-floating > input[type="date"] ~ label,
.form-floating > input[type="time"] ~ label,
.form-floating > input[type="datetime-local"] ~ label,
.form-floating > input[type="month"] ~ label,
.form-floating > input[type="week"] ~ label {
    color: var(--md-sys-color-on-surface-variant);
    opacity: 0.9;
    transform: scale(0.8) translateY(-0.62rem) translateX(0.15rem);
}
.form-floating > .form-control:focus ~ label,
.form-floating > .form-select:focus ~ label { color: var(--md-sys-color-primary); opacity: 1; }
.form-floating > .form-control:not(:placeholder-shown) ~ label::after,
.form-floating > .form-control:focus ~ label::after,
.form-floating > .form-select ~ label::after,
.form-floating > .form-control:-webkit-autofill ~ label::after { background: transparent; }
.form-floating > input[type="date"],
.form-floating > input[type="time"],
.form-floating > input[type="datetime-local"] { color-scheme: inherit; }
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator,
input[type="datetime-local"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.7; border-radius: 6px; padding: 2px; }
:root[data-bs-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator,
:root[data-bs-theme="dark"] input[type="time"]::-webkit-calendar-picker-indicator,
:root[data-bs-theme="dark"] input[type="datetime-local"]::-webkit-calendar-picker-indicator { filter: invert(1); }
select.form-select option { color: var(--md-sys-color-on-surface); background-color: var(--md-sys-color-surface-container-lowest); }
:root[data-bs-theme="dark"] select.form-select option { background-color: var(--md-sys-color-surface-container-high); }

.input-group > .input-group-text { color: var(--md-sys-color-on-surface-variant); background-color: var(--md-sys-color-surface-container-high); border: 1px solid var(--md-sys-color-outline); border-radius: var(--md-sys-shape-md) 0 0 var(--md-sys-shape-md); }

.form-check-input { background-color: var(--md-sys-color-surface-container-lowest); border-color: var(--md-sys-color-outline); }
.form-check-input:checked { background-color: var(--md-sys-color-primary); border-color: var(--md-sys-color-primary); }
.form-check-input:focus { border-color: var(--md-sys-color-primary); box-shadow: 0 0 0 3px var(--md-sys-state-focus); }
.form-switch .form-check-input { width: 3.1em; height: 1.65em; margin-top: 0; cursor: pointer; }
.form-switch .form-check-input:checked { --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e"); }

/* ---------- 5. TARJETAS · LISTAS · BADGES · ALERTAS ---------- */
.card { color: var(--md-sys-color-on-surface); background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-lg); box-shadow: none; }
.card-header, .card-footer { color: var(--md-sys-color-on-surface); background-color: transparent; border-color: var(--md-sys-color-outline-variant); }
.surface { background-color: var(--md-sys-color-surface); color: var(--md-sys-color-on-surface); }
.surface-container { background-color: var(--md-sys-color-surface-container); color: var(--md-sys-color-on-surface); }
.surface-container-low { background-color: var(--md-sys-color-surface-container-low); color: var(--md-sys-color-on-surface); }
.surface-container-high { background-color: var(--md-sys-color-surface-container-high); color: var(--md-sys-color-on-surface); }
.elevation-1 { box-shadow: var(--md-sys-elevation-1); }
.elevation-2 { box-shadow: var(--md-sys-elevation-2); }
.elevation-3 { box-shadow: var(--md-sys-elevation-3); }

.badge { --bs-badge-border-radius: var(--md-sys-shape-sm); font-weight: 500; letter-spacing: 0.02em; }
.alert { border: 0; border-radius: var(--md-sys-shape-md); }
.alert-danger { background: var(--md-sys-color-error-container); color: var(--md-sys-color-on-error-container); }
.alert-success { background: var(--hw-success-container); color: var(--hw-success); }
.alert-warning { background: var(--hw-warning-container); color: var(--hw-warning); }
.alert-info { background: var(--hw-info-container); color: var(--hw-info); }

.progress { --bs-progress-height: 0.5rem; --bs-progress-bg: var(--md-sys-color-surface-container-high); --bs-progress-bar-bg: var(--md-sys-color-primary); --bs-progress-border-radius: var(--md-sys-shape-full); }

/* ---------- 6. NAVEGACIÓN · MENÚS · PAGINACIÓN ---------- */
.dropdown-menu {
    --bs-dropdown-bg: var(--md-sys-color-surface-container);
    --bs-dropdown-color: var(--md-sys-color-on-surface);
    --bs-dropdown-border-color: var(--md-sys-color-outline-variant);
    --bs-dropdown-border-radius: var(--md-sys-shape-lg);
    --bs-dropdown-link-color: var(--md-sys-color-on-surface);
    --bs-dropdown-link-hover-bg: var(--md-sys-state-hover);
    --bs-dropdown-link-hover-color: var(--md-sys-color-on-surface);
    --bs-dropdown-link-active-bg: var(--md-sys-color-primary-container);
    --bs-dropdown-link-active-color: var(--md-sys-color-on-primary-container);
    --bs-dropdown-divider-bg: var(--md-sys-color-outline-variant);
    box-shadow: var(--md-sys-elevation-3);
    padding: 0.4rem;
    min-width: 14rem;
    animation: hw-pop 0.16s var(--md-sys-motion-easing);
}
.dropdown-item { border-radius: var(--md-sys-shape-sm); display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 0.8rem; font-size: 0.9rem; }

.pagination { --bs-pagination-bg: transparent; --bs-pagination-color: var(--md-sys-color-on-surface); --bs-pagination-border-color: transparent; --bs-pagination-hover-bg: var(--md-sys-state-hover); --bs-pagination-hover-color: var(--md-sys-color-primary); --bs-pagination-hover-border-color: transparent; --bs-pagination-active-bg: var(--md-sys-color-primary-container); --bs-pagination-active-color: var(--md-sys-color-on-primary-container); --bs-pagination-active-border-color: transparent; --bs-pagination-disabled-bg: transparent; --bs-pagination-disabled-border-color: transparent; --bs-pagination-disabled-color: var(--md-sys-color-outline); --bs-pagination-focus-bg: var(--md-sys-state-hover); --bs-pagination-focus-color: var(--md-sys-color-primary); gap: 0.25rem; }
.page-link { border-radius: var(--md-sys-shape-full) !important; padding: 0.4rem 0.9rem; font-weight: 500; font-size: 0.85rem; box-shadow: none !important; }

/* ---------- 7. UTILIDADES ---------- */
.md-divider { width: 100%; height: 1px; background-color: var(--md-sys-color-outline-variant); border: 0; }
.text-primary { color: var(--md-sys-color-primary) !important; }
.text-secondary { color: var(--md-sys-color-on-surface-variant) !important; }
.text-error { color: var(--md-sys-color-error) !important; }
.bg-primary-container { background-color: var(--md-sys-color-primary-container) !important; color: var(--md-sys-color-on-primary-container) !important; }
.rounded-md { border-radius: var(--md-sys-shape-md); }
.rounded-lg { border-radius: var(--md-sys-shape-lg); }
.rounded-xl { border-radius: var(--md-sys-shape-xl); }
.cursor-pointer { cursor: pointer; }

/* Barra superior heredada (páginas de invitado) */
.navbar-healthware { background-color: var(--md-sys-color-brand-bg); color: var(--md-sys-color-on-brand-bg); box-shadow: var(--md-sys-elevation-1); }
.navbar-healthware .navbar-brand, .navbar-healthware .nav-link { color: var(--md-sys-color-on-brand-bg); }
.navbar-healthware .navbar-brand { font-weight: 700; }
.navbar-healthware .btn-icon { color: var(--md-sys-color-on-brand-bg); }
.navbar-healthware .btn-icon:hover { background-color: rgba(255, 255, 255, 0.12); color: var(--md-sys-color-on-brand-bg); }
.footer-healthware { background-color: var(--md-sys-color-surface); border-top: 1px solid var(--md-sys-color-outline-variant); padding: 1.5rem 0; }
.footer-text { color: var(--md-sys-color-on-surface-variant); font-size: 0.875rem; }

@keyframes hw-pop { from { opacity: 0; transform: translateY(-4px) scale(0.98); } to { opacity: 1; transform: none; } }
@keyframes hw-rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { scroll-behavior: auto !important; animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
}
__HW_EOF__
escribir "public/css/app-layout.css" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · LAYOUT INTERNO
   Navbar (menú · marca centrada · acciones) + menú lateral
   que se oculta y se despliega desde el botón de la izquierda.
   ========================================================= */

.app-body { min-height: 100vh; background-color: var(--md-sys-color-background); }

/* ---------- BARRA SUPERIOR ---------- */
.app-topbar {
    position: sticky;
    top: 0;
    z-index: 1040;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 1rem;
    height: var(--hw-topbar-height);
    padding: 0 1rem;
    background-color: var(--md-sys-color-surface-container-low);
    border-bottom: 1px solid var(--md-sys-color-outline-variant);
    box-shadow: var(--md-sys-elevation-1);
}
.app-topbar-start, .app-topbar-end { display: flex; align-items: center; gap: 0.35rem; min-width: 0; }
.app-topbar-end { justify-content: flex-end; }

.app-menu-toggle { color: var(--md-sys-color-on-surface); }
.app-menu-toggle .material-symbols-outlined { transition: transform var(--md-sys-motion-duration-medium) var(--md-sys-motion-easing); }
html.hw-nav-open .app-menu-toggle .material-symbols-outlined { transform: rotate(90deg); }

.app-topnav { display: none; align-items: center; gap: 0.15rem; margin-left: 0.25rem; }
@media (min-width: 992px) { .app-topnav { display: flex; } }
.app-topnav-link {
    display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.45rem 0.9rem;
    color: var(--md-sys-color-on-surface-variant); background: transparent; border: 0;
    border-radius: var(--md-sys-shape-full); font-size: 0.88rem; font-weight: 500;
    transition: background-color var(--md-sys-motion-duration-short), color var(--md-sys-motion-duration-short);
}
.app-topnav-link .material-symbols-outlined { font-size: 1.2rem; }
.app-topnav-link:hover, .app-topnav-link.show { color: var(--md-sys-color-on-surface); background-color: var(--md-sys-state-hover); }
.app-topnav-link.active { color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-primary-container); }
.app-topnav-link.dropdown-toggle::after { display: none; }

.app-brand { display: inline-flex; align-items: center; gap: 0.6rem; color: var(--md-sys-color-on-surface); font-weight: 700; letter-spacing: -0.02em; }
.app-brand:hover { color: var(--md-sys-color-primary); }
.app-brand-icon { color: var(--md-sys-color-primary); font-size: 1.9rem; font-variation-settings: 'FILL' 1; }
.app-brand-name { font-size: 1.2rem; }
.app-topbar-center { display: flex; justify-content: center; }

/* Notificaciones */
.app-notif { position: relative; }
.app-notif-button .material-symbols-outlined { font-size: 1.5rem; }
.app-notif-badge {
    position: absolute; top: 2px; right: 1px; min-width: 1.15rem; height: 1.15rem; padding: 0 0.28rem;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--md-sys-color-on-error); background: var(--md-sys-color-error);
    border: 2px solid var(--md-sys-color-surface-container-low);
    border-radius: var(--md-sys-shape-full); font-size: 0.64rem; font-weight: 700; line-height: 1;
}
.app-notif-menu { width: min(380px, calc(100vw - 1.5rem)); padding: 0; overflow: hidden; }
.app-notif-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.95rem 1.1rem 0.8rem; }
.app-notif-header h2 { margin: 0; font-size: 1rem; font-weight: 700; }
.app-notif-header span { color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); padding: 0.2rem 0.6rem; border-radius: var(--md-sys-shape-full); font-size: 0.72rem; font-weight: 600; }
.app-notif-list { max-height: 340px; overflow-y: auto; border-top: 1px solid var(--md-sys-color-outline-variant); border-bottom: 1px solid var(--md-sys-color-outline-variant); }
.app-notif-item { display: flex; gap: 0.8rem; padding: 0.8rem 1.1rem; color: var(--md-sys-color-on-surface); border-bottom: 1px solid var(--md-sys-color-outline-variant); transition: background-color var(--md-sys-motion-duration-short); }
.app-notif-item:last-child { border-bottom: 0; }
.app-notif-item:hover { background-color: var(--md-sys-state-hover); color: var(--md-sys-color-on-surface); }
.app-notif-icon { flex: 0 0 38px; width: 38px; height: 38px; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-full); }
.app-notif-icon .material-symbols-outlined { font-size: 1.2rem; }
.app-notif-item.is-unread .app-notif-icon { color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); }
.app-notif-body { min-width: 0; flex: 1; }
.app-notif-title { display: block; font-size: 0.85rem; font-weight: 600; line-height: 1.3; }
.app-notif-item.is-unread .app-notif-title { color: var(--md-sys-color-primary); }
.app-notif-text { display: block; margin-top: 0.15rem; color: var(--md-sys-color-on-surface-variant); font-size: 0.78rem; line-height: 1.35; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
.app-notif-time { display: block; margin-top: 0.25rem; color: var(--md-sys-color-outline); font-size: 0.7rem; }
.app-notif-dot { flex: 0 0 8px; width: 8px; height: 8px; margin-top: 0.4rem; background: var(--md-sys-color-primary); border-radius: 50%; }
.app-notif-empty { padding: 2rem 1rem; text-align: center; color: var(--md-sys-color-on-surface-variant); font-size: 0.85rem; }
.app-notif-empty .material-symbols-outlined { display: block; margin: 0 auto 0.5rem; font-size: 2rem; color: var(--md-sys-color-outline); }
.app-notif-footer { display: block; padding: 0.75rem 1.1rem; text-align: center; font-size: 0.85rem; font-weight: 600; }
.app-notif-footer:hover { background-color: var(--md-sys-state-hover); }

/* Usuario */
.app-user-trigger { display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.25rem 0.5rem 0.25rem 0.3rem; color: var(--md-sys-color-on-surface); background: transparent; border: 0; border-radius: var(--md-sys-shape-full); transition: background-color var(--md-sys-motion-duration-short); }
.app-user-trigger:hover, .app-user-trigger.show { background-color: var(--md-sys-state-hover); }
.app-user-avatar { width: 36px; height: 36px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-primary); background: var(--md-sys-color-primary); border-radius: 50%; font-size: 0.85rem; font-weight: 600; }
.app-user-avatar-large { width: 46px; height: 46px; font-size: 1rem; }
.app-user-summary { flex-direction: column; align-items: flex-start; line-height: 1.15; text-align: left; }
.app-user-name { font-size: 0.85rem; font-weight: 600; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.app-user-role { color: var(--md-sys-color-on-surface-variant); font-size: 0.7rem; }
.app-user-chevron { font-size: 1.2rem; color: var(--md-sys-color-on-surface-variant); }
.app-user-menu { min-width: 17rem; padding: 0.5rem; }
.app-user-menu-header { display: flex; align-items: center; gap: 0.8rem; padding: 0.6rem; }
.app-user-menu-identity { display: flex; flex-direction: column; min-width: 0; }
.app-user-menu-name { font-size: 0.9rem; font-weight: 600; }
.app-user-menu-email { color: var(--md-sys-color-on-surface-variant); font-size: 0.75rem; overflow: hidden; text-overflow: ellipsis; }
.app-user-role-badge { display: inline-flex; align-items: center; gap: 0.4rem; margin: 0 0.6rem 0.3rem; padding: 0.3rem 0.7rem; color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-full); font-size: 0.75rem; font-weight: 600; }
.app-user-role-badge .material-symbols-outlined { font-size: 1rem; }
.app-logout-button { color: var(--md-sys-color-error); }
.app-logout-button:hover { color: var(--md-sys-color-error); background-color: var(--md-sys-color-error-container); }

/* ---------- ESPACIO DE TRABAJO ---------- */
.app-workspace { position: relative; min-height: calc(100vh - var(--hw-topbar-height)); }

.app-sidebar {
    position: fixed;
    top: var(--hw-topbar-height);
    bottom: 0;
    left: 0;
    z-index: 1035;
    width: var(--hw-sidebar-width);
    display: flex;
    flex-direction: column;
    background-color: var(--md-sys-color-surface-container-low);
    border-right: 1px solid var(--md-sys-color-outline-variant);
    transform: translateX(-100%);
    transition: transform var(--md-sys-motion-duration-medium) var(--md-sys-motion-easing);
    visibility: hidden;
}
html.hw-nav-open .app-sidebar { transform: none; visibility: visible; box-shadow: var(--md-sys-elevation-3); }
.app-sidebar-backdrop { position: fixed; inset: var(--hw-topbar-height) 0 0 0; z-index: 1034; background: rgba(0, 0, 0, 0.4); opacity: 0; visibility: hidden; transition: opacity var(--md-sys-motion-duration-medium); }
html.hw-nav-open .app-sidebar-backdrop { opacity: 1; visibility: visible; }

.app-screen { min-width: 0; padding: 1.75rem 1rem 3rem; transition: margin-left var(--md-sys-motion-duration-medium) var(--md-sys-motion-easing); }
.app-screen-content { width: 100%; max-width: 1480px; margin-inline: auto; }

@media (min-width: 992px) {
    .app-sidebar { transform: none; visibility: visible; box-shadow: none; }
    html.hw-nav-collapsed .app-sidebar { transform: translateX(-100%); visibility: hidden; }
    .app-sidebar-backdrop { display: none; }
    .app-screen { margin-left: var(--hw-sidebar-width); padding: 2rem clamp(1.25rem, 3vw, 2.75rem) 3.5rem; }
    html.hw-nav-collapsed .app-screen { margin-left: 0; }
}
@media (max-width: 575.98px) {
    .app-topbar { gap: 0.25rem; padding: 0 0.5rem; }
    .app-brand-name { font-size: 1.05rem; }
}

/* ---------- MENÚ LATERAL ---------- */
.app-sidebar-body { flex: 1; min-height: 0; overflow-y: auto; padding: 1rem 0.85rem 0.5rem; display: flex; flex-direction: column; }
.sidebar-heading { padding: 0.25rem 0.9rem 0.75rem; }
.sidebar-section-label { color: var(--md-sys-color-on-surface-variant); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; }
.app-navigation { display: flex; flex-direction: column; gap: 0.2rem; }
.sidebar-link {
    display: flex; align-items: center; gap: 0.85rem; width: 100%; padding: 0.7rem 0.9rem;
    color: var(--md-sys-color-on-surface-variant); background: transparent; border: 0;
    border-radius: var(--md-sys-shape-full); font-size: 0.9rem; font-weight: 500; text-align: left;
    transition: background-color var(--md-sys-motion-duration-short), color var(--md-sys-motion-duration-short);
}
.sidebar-link:hover { color: var(--md-sys-color-on-surface); background-color: var(--md-sys-state-hover); }
.sidebar-link.active { color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-primary-container); font-weight: 600; }
.sidebar-link.active .sidebar-icon { font-variation-settings: 'FILL' 1; }
.sidebar-icon { font-size: 1.4rem; flex-shrink: 0; }
.sidebar-link-label { flex: 1; }
.sidebar-chevron { margin-left: auto; font-size: 1.25rem; transition: transform var(--md-sys-motion-duration-medium) var(--md-sys-motion-easing); }
.sidebar-group-toggle:not(.collapsed) .sidebar-chevron { transform: rotate(180deg); }
.sidebar-group-toggle.has-active { color: var(--md-sys-color-on-surface); }
.sidebar-group-toggle.has-active .sidebar-icon { color: var(--md-sys-color-primary); }
.sidebar-submenu { display: flex; flex-direction: column; gap: 0.15rem; margin: 0.1rem 0 0.35rem 1.45rem; padding-left: 0.65rem; border-left: 2px solid var(--md-sys-color-outline-variant); }
.sidebar-submenu.collapse:not(.show) { display: none; }
.sidebar-sublink { padding: 0.55rem 0.85rem; font-size: 0.85rem; }
.sidebar-sublink .sidebar-icon { font-size: 1.2rem; }
.app-sidebar-footer { display: flex; flex-direction: column; gap: 0.1rem; margin-top: auto; padding: 1rem 0.9rem 0.75rem; border-top: 1px solid var(--md-sys-color-outline-variant); }
.app-sidebar-footer-brand { color: var(--md-sys-color-on-surface); font-size: 0.82rem; font-weight: 700; }
.app-sidebar-footer-version, .app-sidebar-copyright { color: var(--md-sys-color-on-surface-variant); font-size: 0.7rem; }
__HW_EOF__
escribir "public/css/hw-components.css" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · COMPONENTES DE VISTA (hw-*)
   Un solo juego de clases para todos los módulos: encabezado,
   superficie, barra de búsqueda/filtros, tabla, badges, modales
   con scroll interno, secciones de formulario y detalle.
   Se carga DESPUÉS de los estilos de las librerías (DataTables).
   ========================================================= */

/* ---------- ESTRUCTURA ---------- */
.hw-page { display: flex; flex-direction: column; gap: 1.5rem; width: 100%; min-width: 0; animation: hw-rise 0.35s var(--md-sys-motion-easing) both; }
.hw-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.hw-heading { display: flex; align-items: center; gap: 1rem; min-width: 0; }
.hw-heading-icon { width: 52px; height: 52px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-lg); }
.hw-heading-icon .material-symbols-outlined { font-size: 1.8rem; }
.hw-title { margin: 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.03em; }
.hw-subtitle { margin: 0.25rem 0 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.9rem; }
.hw-create-button { flex-shrink: 0; min-height: 46px; padding-inline: 1.4rem; }

/* Tarjetas de resumen */
.hw-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
.hw-stat { display: flex; align-items: center; gap: 0.9rem; padding: 1.05rem 1.2rem; background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-1); }
.hw-stat > .material-symbols-outlined { width: 46px; height: 46px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-lg); font-size: 1.5rem; }
.hw-stat strong { display: block; font-size: 1.35rem; line-height: 1.1; font-variant-numeric: tabular-nums; }
.hw-stat span:not(.material-symbols-outlined) { color: var(--md-sys-color-on-surface-variant); font-size: 0.78rem; }

/* Superficie */
.hw-surface { min-width: 0; overflow: hidden; background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-1); }
.hw-surface-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1.5rem 1.75rem 1.15rem; }
.hw-surface-title { margin: 0; font-size: 1.05rem; font-weight: 700; letter-spacing: -0.015em; }
.hw-surface-description { margin: 0.25rem 0 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.85rem; }
.hw-record-indicator { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.45rem 0.85rem; color: var(--md-sys-color-on-surface-variant); background-color: var(--md-sys-color-surface-container); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-full); font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
.hw-record-indicator .material-symbols-outlined { font-size: 1.05rem; }

/* ---------- BARRA DE BÚSQUEDA Y FILTROS (en tiempo real) ---------- */
.hw-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; padding: 0 1.75rem 1.1rem; }
.hw-search { position: relative; flex: 1 1 280px; max-width: 460px; min-width: 200px; }
.hw-search > .material-symbols-outlined { position: absolute; left: 0.95rem; top: 50%; transform: translateY(-50%); color: var(--md-sys-color-on-surface-variant); font-size: 1.3rem; pointer-events: none; }
.hw-search .form-control { min-height: 46px; height: 46px; padding: 0.5rem 2.6rem; border-radius: var(--md-sys-shape-full); background-color: var(--md-sys-color-surface-container); border-color: var(--md-sys-color-outline-variant); font-size: 0.9rem; }
.hw-search .form-control:focus { background-color: var(--md-sys-color-surface-container-lowest); }
.hw-search-clear { position: absolute; right: 0.4rem; top: 50%; transform: translateY(-50%); display: none; width: 34px; height: 34px; min-height: 34px; }
.hw-search.has-value .hw-search-clear { display: inline-flex; }
.hw-toolbar .form-select { width: auto; min-width: 170px; height: 46px; min-height: 46px; padding: 0.4rem 2.4rem 0.4rem 1rem; border-radius: var(--md-sys-shape-full); background-color: var(--md-sys-color-surface-container); border-color: var(--md-sys-color-outline-variant); font-size: 0.85rem; }
.hw-toolbar-clear { margin-left: auto; }
.hw-toolbar-count { color: var(--md-sys-color-on-surface-variant); font-size: 0.8rem; white-space: nowrap; }

/* ---------- TABLA ---------- */
.hw-table-container { padding: 0 1rem 1rem; background: transparent; border: 0; box-shadow: none; }
.hw-data-table { width: 100% !important; margin: 0 !important; border-collapse: separate; border-spacing: 0; color: var(--md-sys-color-on-surface); vertical-align: middle; --bs-table-bg: transparent; --bs-table-color: var(--md-sys-color-on-surface); --bs-table-hover-bg: transparent; }
.hw-data-table thead th { padding: 0.9rem 0.9rem; color: var(--md-sys-color-on-surface-variant); background-color: var(--md-sys-color-surface-container); border-top: 1px solid var(--md-sys-color-outline-variant); border-bottom: 1px solid var(--md-sys-color-outline-variant); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; }
.hw-data-table thead th:first-child { border-top-left-radius: var(--md-sys-shape-md); border-left: 1px solid var(--md-sys-color-outline-variant); }
.hw-data-table thead th:last-child { border-top-right-radius: var(--md-sys-shape-md); border-right: 1px solid var(--md-sys-color-outline-variant); }
.hw-data-table tbody td { padding: 0.95rem 0.9rem; color: var(--md-sys-color-on-surface); background-color: transparent; border-bottom: 1px solid var(--md-sys-color-outline-variant); font-size: 0.86rem; vertical-align: middle; transition: background-color var(--md-sys-motion-duration-short); }
.hw-data-table tbody tr:last-child td { border-bottom: 0; }
.hw-data-table tbody tr:hover td { background-color: var(--md-sys-state-hover); }
.hw-data-table .hw-column-id { width: 70px; }
.hw-id { color: var(--md-sys-color-on-surface-variant) !important; font-size: 0.75rem !important; font-weight: 600; font-variant-numeric: tabular-nums; }
.hw-muted, .hw-email { color: var(--md-sys-color-on-surface-variant) !important; }
.hw-mono { font-variant-numeric: tabular-nums; letter-spacing: 0.02em; }

.hw-identity { display: flex; align-items: center; gap: 0.8rem; min-width: 180px; }
.hw-avatar { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background-color: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-full); transition: transform var(--md-sys-motion-duration-short); }
.hw-data-table tbody tr:hover .hw-avatar { transform: scale(1.06); }
.hw-avatar .material-symbols-outlined { font-size: 1.3rem; }
.hw-identity-data { display: flex; flex-direction: column; min-width: 0; gap: 0.15rem; }
.hw-fullname { color: var(--md-sys-color-on-surface); font-size: 0.88rem; font-weight: 600; line-height: 1.3; }
.hw-identity-caption { color: var(--md-sys-color-on-surface-variant); font-size: 0.72rem; }

/* ---------- BADGES: chips y estados ---------- */
.hw-chip { display: inline-flex; align-items: center; max-width: 200px; padding: 0.32rem 0.75rem; overflow: hidden; color: var(--md-sys-color-on-secondary-container); background-color: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-full); font-size: 0.74rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
.hw-role { display: inline-flex; align-items: center; max-width: 180px; padding: 0.32rem 0.75rem; color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-full); font-size: 0.74rem; font-weight: 600; white-space: nowrap; }
.hw-status { display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.32rem 0.75rem; border-radius: var(--md-sys-shape-full); font-size: 0.74rem; font-weight: 600; white-space: nowrap; }
.hw-status .hw-status-dot { width: 7px; height: 7px; flex-shrink: 0; border-radius: var(--md-sys-shape-full); background-color: currentColor; }
.hw-status .material-symbols-outlined { font-size: 0.95rem; }
.hw-status-success, .hw-status-active { color: var(--hw-success); background-color: var(--hw-success-container); }
.hw-status-warning, .hw-status-inactive { color: var(--hw-warning); background-color: var(--hw-warning-container); }
.hw-status-info { color: var(--hw-info); background-color: var(--hw-info-container); }
.hw-status-error, .hw-status-deleted { color: var(--md-sys-color-on-error-container); background-color: var(--md-sys-color-error-container); }
.hw-status-neutral { color: var(--md-sys-color-on-surface-variant); background-color: var(--md-sys-color-surface-container-high); }
.hw-deleted-label { display: inline-flex; align-items: center; gap: 0.35rem; color: var(--md-sys-color-on-surface-variant); font-size: 0.78rem; }
.hw-deleted-label .material-symbols-outlined { font-size: 1rem; }

/* Acciones por fila */
.hw-actions { display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; width: 100%; }
.hw-action-button { width: 38px; height: 38px; display: inline-grid; place-items: center; color: var(--md-sys-color-on-surface-variant); background: transparent; border: 0; border-radius: var(--md-sys-shape-full); transition: background-color var(--md-sys-motion-duration-short), color var(--md-sys-motion-duration-short); }
.hw-action-button .material-symbols-outlined { font-size: 1.3rem; }
.hw-action-button:hover { color: var(--md-sys-color-primary); background-color: var(--md-sys-color-primary-container); }
.hw-action-delete:hover { color: var(--md-sys-color-on-error-container); background-color: var(--md-sys-color-error-container); }

/* Estado vacío */
.hw-empty { display: flex; flex-direction: column; align-items: center; gap: 0.4rem; padding: 3rem 1.5rem 3.5rem; text-align: center; }
.hw-empty > .material-symbols-outlined { width: 72px; height: 72px; display: grid; place-items: center; color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-xl); font-size: 2.2rem; }
.hw-empty h3 { margin: 0.6rem 0 0; font-size: 1.05rem; font-weight: 600; }
.hw-empty p { margin: 0; font-size: 0.88rem; }
.hw-no-results { display: none; padding: 2.5rem 1rem; text-align: center; color: var(--md-sys-color-on-surface-variant); font-size: 0.9rem; }
.hw-no-results .material-symbols-outlined { display: block; margin: 0 auto 0.5rem; font-size: 2.2rem; color: var(--md-sys-color-outline); }

/* ---------- DATATABLES (se oculta su buscador: se usa .hw-search) ---------- */
.hw-surface .dataTables_wrapper { padding: 0; }
.hw-surface .dataTables_wrapper .dataTables_filter { display: none; }
.hw-surface .dataTables_wrapper .row { margin: 0; align-items: center; row-gap: 0.5rem; }
.hw-surface .dataTables_wrapper .row > div { padding: 0; }
.hw-surface .dataTables_wrapper .row:first-child { padding: 0 0.5rem 0.75rem; }
.hw-surface .dataTables_wrapper .row:last-child { padding: 0.75rem 0.5rem 0.25rem; }
.hw-surface .dataTables_info, .hw-surface .dataTables_length label { color: var(--md-sys-color-on-surface-variant); font-size: 0.8rem; }
.hw-surface .dataTables_length select { display: inline-block; width: auto; min-height: 36px; height: 36px; margin: 0 0.4rem; padding: 0.2rem 2rem 0.2rem 0.8rem; border-radius: var(--md-sys-shape-full); font-size: 0.8rem; }
.hw-surface .dataTables_paginate { display: flex; justify-content: flex-end; }
.hw-surface .dataTables_paginate .pagination { margin: 0; }
table.dataTable { border-collapse: separate !important; }
table.dataTable > thead > tr > th, table.dataTable > thead > tr > td { box-shadow: none !important; }
table.dataTable.hw-data-table > thead .sorting::before, table.dataTable.hw-data-table > thead .sorting::after { display: none; }

/* ---------- MODALES: scroll interno, cabecera y pie fijos ---------- */
.modal-backdrop { --bs-backdrop-bg: #1d1b20; --bs-backdrop-opacity: 0.55; }
.hw-modal .modal-dialog { margin: 1rem auto; }
.hw-modal .modal-dialog-scrollable { height: auto; max-height: calc(100% - 2rem); }
.hw-modal .modal-dialog-centered.modal-dialog-scrollable { min-height: 0; }
.hw-modal .modal-content { display: flex; flex-direction: column; max-height: calc(100vh - 2rem); overflow: hidden; color: var(--md-sys-color-on-surface); background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-3); }
.hw-modal .modal-content > form { display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; margin: 0; }
.hw-modal .modal-content > form > .alert, .hw-modal .modal-content > .alert { flex: 0 0 auto; margin: 0.75rem 1.5rem 0; }
.hw-modal-body { flex: 1 1 auto; min-height: 0; overflow-y: auto; overscroll-behavior: contain; padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1.1rem; scrollbar-width: thin; scrollbar-color: var(--md-sys-color-outline-variant) transparent; }
.hw-modal-header { flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem 1rem; border-bottom: 1px solid var(--md-sys-color-outline-variant); }
.hw-modal-heading { display: flex; align-items: center; gap: 0.9rem; min-width: 0; }
.hw-modal-icon { width: 46px; height: 46px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-primary-container); background: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-lg); }
.hw-modal-icon .material-symbols-outlined { font-size: 1.5rem; }
.hw-modal-title { margin: 0; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.02em; }
.hw-modal-subtitle { margin: 0.15rem 0 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.82rem; }
.hw-modal-close { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-surface-variant); background: transparent; border: 0; border-radius: var(--md-sys-shape-full); }
.hw-modal-close:hover { background-color: var(--md-sys-state-hover); color: var(--md-sys-color-on-surface); }
.hw-modal-footer { flex: 0 0 auto; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 0.6rem; padding: 0.95rem 1.5rem; background-color: var(--md-sys-color-surface-container-low); border-top: 1px solid var(--md-sys-color-outline-variant); }
.modal.fade .modal-dialog { transform: translateY(14px) scale(0.985); transition: transform 0.25s var(--md-sys-motion-easing); }
.modal.show .modal-dialog { transform: none; }

/* Secciones del formulario */
.hw-form-section { padding: 1.15rem 1.25rem 1.3rem; background-color: var(--md-sys-color-surface-container); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-lg); }
.hw-form-section-heading { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.05rem; }
.hw-form-section-heading > .material-symbols-outlined { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-md); font-size: 1.35rem; }
.hw-form-section-heading h3 { margin: 0; font-size: 0.95rem; font-weight: 700; }
.hw-form-section-heading p { margin: 0.1rem 0 0; font-size: 0.78rem; }
.hw-field-hint { margin-top: 0.3rem; padding-left: 0.2rem; color: var(--md-sys-color-on-surface-variant); font-size: 0.75rem; }
.hw-form-section .row { --bs-gutter-y: 1rem; }
.hw-section-collapsible[hidden] { display: none !important; }
.hw-section-collapsible { animation: hw-rise 0.25s var(--md-sys-motion-easing) both; border-color: color-mix(in srgb, var(--md-sys-color-primary) 45%, var(--md-sys-color-outline-variant)); }

/* Interruptor de estado */
.hw-switch-container { display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-height: 3.5rem; padding: 0.55rem 0.9rem; background-color: var(--md-sys-color-surface-container-lowest); border: 1px solid var(--md-sys-color-outline); border-radius: var(--md-sys-shape-md); }
:root[data-bs-theme="dark"] .hw-switch-container { background-color: var(--md-sys-color-surface-container); }
.hw-switch-information { display: flex; align-items: center; gap: 0.7rem; min-width: 0; }
.hw-switch-information > .material-symbols-outlined { color: var(--md-sys-color-primary); }
.hw-switch-title { display: block; font-size: 0.88rem; font-weight: 600; }
.hw-switch-description { display: block; color: var(--md-sys-color-on-surface-variant); font-size: 0.74rem; }
.hw-switch { margin: 0; padding: 0; min-height: 0; }
.hw-switch .form-check-input { float: none; margin: 0; }
.hw-phone-field .form-control { border-radius: 0 var(--md-sys-shape-md) var(--md-sys-shape-md) 0; min-height: 3.5rem; }
.hw-phone-field .input-group-text { min-height: 3.5rem; padding-inline: 0.95rem; }
.hw-phone-field:focus-within .input-group-text { border-color: var(--md-sys-color-primary); }

/* Detalle (modal de consulta) */
.hw-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; margin: 0; }
.hw-detail-item { display: flex; flex-direction: column; gap: 0.25rem; min-width: 0; padding: 0.85rem 1rem; background-color: var(--md-sys-color-surface-container); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-md); }
.hw-detail-item.is-wide { grid-column: 1 / -1; }
.hw-detail-item dt { color: var(--md-sys-color-on-surface-variant); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; }
.hw-detail-item dd { margin: 0; color: var(--md-sys-color-on-surface); font-size: 0.9rem; font-weight: 500; overflow-wrap: anywhere; }
.hw-subtable { margin: 0.35rem 0 0; font-size: 0.82rem; --bs-table-bg: transparent; --bs-table-color: var(--md-sys-color-on-surface); }
.hw-subtable th { color: var(--md-sys-color-on-surface-variant); font-size: 0.68rem; letter-spacing: 0.06em; text-transform: uppercase; }
@media (max-width: 575.98px) { .hw-detail-grid { grid-template-columns: 1fr; } }

/* Confirmación de eliminación */
.hw-delete-modal .modal-content { overflow: visible; max-height: none; }
.hw-delete-content { position: relative; display: flex; flex-direction: column; align-items: center; gap: 1.1rem; padding: 2.2rem 1.75rem 1.6rem; text-align: center; }
.hw-delete-content form { width: 100%; display: flex; flex-direction: column; gap: 1rem; }
.hw-delete-icon { width: 68px; height: 68px; display: grid; place-items: center; color: var(--md-sys-color-on-error-container); background: var(--md-sys-color-error-container); border-radius: var(--md-sys-shape-xl); }
.hw-delete-icon .material-symbols-outlined { font-size: 2rem; }
.hw-delete-close { position: absolute; top: 0.9rem; right: 0.9rem; }
.hw-delete-heading { display: flex; flex-direction: column; gap: 0.3rem; }
.hw-delete-overline { color: var(--md-sys-color-error); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; }
.hw-delete-heading h2 { margin: 0; font-size: 1.25rem; font-weight: 700; }
.hw-delete-heading p { margin: 0; font-size: 0.88rem; }
.hw-delete-identity { display: flex; align-items: center; gap: 0.85rem; padding: 0.85rem 1rem; text-align: left; background-color: var(--md-sys-color-surface-container); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-lg); }
.hw-delete-avatar { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-error-container); background: var(--md-sys-color-error-container); border-radius: var(--md-sys-shape-full); }
.hw-delete-data { display: flex; flex-direction: column; min-width: 0; gap: 0.1rem; }
.hw-delete-name { font-size: 0.92rem; font-weight: 600; overflow-wrap: anywhere; }
.hw-delete-sub, .hw-delete-email, .hw-delete-id { color: var(--md-sys-color-on-surface-variant); font-size: 0.75rem; overflow-wrap: anywhere; }
.hw-delete-notice { display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.75rem 0.95rem; text-align: left; color: var(--hw-warning); background: var(--hw-warning-container); border-radius: var(--md-sys-shape-md); }
.hw-delete-notice p { margin: 0; color: inherit; font-size: 0.8rem; line-height: 1.45; }
.hw-delete-notice .material-symbols-outlined { font-size: 1.2rem; }
.hw-delete-actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 0.6rem; }
.hw-delete-confirm { color: var(--md-sys-color-on-error); background-color: var(--md-sys-color-error); border: 1px solid transparent; }
.hw-delete-confirm:hover, .hw-delete-confirm:focus { color: var(--md-sys-color-on-error); background-color: var(--md-sys-color-error); box-shadow: inset 0 0 0 100px rgba(255, 255, 255, 0.12); }

/* ---------- ESPECÍFICOS ---------- */
.hw-week { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.75rem; padding: 0 1.25rem 1.25rem; }
.hw-week-day { display: flex; flex-direction: column; gap: 0.5rem; min-width: 0; padding: 0.85rem; background-color: var(--md-sys-color-surface-container); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-lg); }
.hw-week-day h3 { margin: 0; color: var(--md-sys-color-on-surface-variant); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; }
.hw-week-slot { display: flex; flex-direction: column; gap: 0.1rem; padding: 0.5rem 0.65rem; color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-md); font-size: 0.76rem; }
.hw-week-slot strong { font-variant-numeric: tabular-nums; }
.hw-week-slot span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; opacity: 0.85; }
.hw-week-empty { color: var(--md-sys-color-on-surface-variant); font-size: 0.74rem; opacity: 0.75; }
@media (max-width: 1199.98px) { .hw-week { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (max-width: 575.98px) { .hw-week { grid-template-columns: 1fr 1fr; } }

.hw-server-filters { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) auto auto; align-items: center; gap: 0.75rem; padding: 0 1.75rem 1.1rem; }
@media (max-width: 991.98px) { .hw-server-filters { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575.98px) { .hw-server-filters { grid-template-columns: 1fr; } }

/* Vistas pendientes de implementar */
.hw-placeholder { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 3.5rem 1.5rem; text-align: center; }
.hw-placeholder > .material-symbols-outlined { width: 76px; height: 76px; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-xl); font-size: 2.3rem; }
.hw-placeholder h2 { margin: 0.7rem 0 0; font-size: 1.15rem; }
.hw-placeholder p { max-width: 46ch; margin: 0; font-size: 0.9rem; }

/* Facturación: emisión con pago único y documento imprimible */
.hw-invoice-lines { display: flex; flex-direction: column; gap: 0.75rem; }
.hw-invoice-line { display: grid; grid-template-columns: minmax(0, 2.4fr) minmax(0, 0.8fr) minmax(0, 1fr) auto; align-items: center; gap: 0.6rem; padding: 0.75rem; background-color: var(--md-sys-color-surface-container-lowest); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-md); }
:root[data-bs-theme="dark"] .hw-invoice-line { background-color: var(--md-sys-color-surface-container-high); }
.hw-invoice-line .form-floating > .form-control, .hw-invoice-line .form-floating > .form-select { height: 3.2rem; min-height: 3.2rem; }
@media (max-width: 767.98px) { .hw-invoice-line { grid-template-columns: 1fr 1fr; } .hw-invoice-line > :first-child { grid-column: 1 / -1; } }
.hw-invoice-summary { display: flex; flex-direction: column; gap: 0.45rem; margin-left: auto; width: min(340px, 100%); padding: 1rem 1.15rem; background-color: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); border-radius: var(--md-sys-shape-lg); }
.hw-invoice-summary div { display: flex; justify-content: space-between; gap: 1rem; font-size: 0.88rem; }
.hw-invoice-summary div:last-child { padding-top: 0.5rem; border-top: 1px solid color-mix(in srgb, currentColor 25%, transparent); font-size: 1.1rem; font-weight: 700; }

.hw-document { max-width: 920px; margin-inline: auto; padding: 2rem 2.25rem; background-color: var(--md-sys-color-surface-container-lowest); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-2); }
:root[data-bs-theme="dark"] .hw-document { background-color: var(--md-sys-color-surface-container); }
.hw-document-head { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid var(--md-sys-color-primary); }
.hw-document-brand { display: flex; align-items: center; gap: 0.75rem; }
.hw-document-brand .material-symbols-outlined { color: var(--md-sys-color-primary); font-size: 2.4rem; font-variation-settings: 'FILL' 1; }
.hw-document-brand strong { display: block; font-size: 1.3rem; letter-spacing: -0.02em; }
.hw-document-brand span:not(.material-symbols-outlined) { color: var(--md-sys-color-on-surface-variant); font-size: 0.8rem; }
.hw-document-meta { text-align: right; }
.hw-document-meta h2 { margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--md-sys-color-primary); }
.hw-document-meta p { margin: 0.2rem 0 0; font-size: 0.85rem; }
.hw-document-parties { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; margin: 1.5rem 0; }
@media (max-width: 575.98px) { .hw-document-parties { grid-template-columns: 1fr; } .hw-document-meta { text-align: left; } }
.hw-document-party { padding: 1rem 1.15rem; background-color: var(--md-sys-color-surface-container); border-radius: var(--md-sys-shape-lg); }
.hw-document-party h3 { margin: 0 0 0.5rem; color: var(--md-sys-color-on-surface-variant); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
.hw-document-party p { margin: 0.1rem 0; color: var(--md-sys-color-on-surface); font-size: 0.9rem; }
.hw-document-totals { display: flex; flex-direction: column; gap: 0.4rem; margin: 1.25rem 0 0 auto; width: min(340px, 100%); }
.hw-document-totals div { display: flex; justify-content: space-between; font-size: 0.9rem; }
.hw-document-totals .is-total { padding-top: 0.6rem; border-top: 2px solid var(--md-sys-color-primary); font-size: 1.2rem; font-weight: 700; color: var(--md-sys-color-primary); }
.hw-document-actions { display: flex; flex-wrap: wrap; gap: 0.6rem; }

@media print {
    .app-topbar, .app-sidebar, .app-sidebar-backdrop, .hw-header, .hw-document-actions, .hw-toast-container { display: none !important; }
    .app-screen { margin: 0 !important; padding: 0 !important; }
    .hw-document { border: 0; box-shadow: none; max-width: none; padding: 0; }
}

/* ---------- INICIO: accesos rápidos ---------- */
.hw-hero { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; padding: 2rem 2.25rem; background: linear-gradient(135deg, var(--md-sys-color-primary-container), var(--md-sys-color-surface-container-low) 70%); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-1); }
.hw-hero h1 { margin: 0; font-size: 1.8rem; font-weight: 700; letter-spacing: -0.03em; }
.hw-hero p { margin: 0.4rem 0 0; max-width: 56ch; }
.hw-hero-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-surface-container-lowest); border-radius: var(--md-sys-shape-full); font-size: 0.82rem; font-weight: 600; box-shadow: var(--md-sys-elevation-1); }
.hw-quick-section { display: flex; flex-direction: column; gap: 0.85rem; }
.hw-quick-section h2 { margin: 0; display: flex; align-items: center; gap: 0.6rem; font-size: 0.95rem; font-weight: 700; }
.hw-quick-section h2 .material-symbols-outlined { color: var(--md-sys-color-primary); font-size: 1.4rem; }
.hw-quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 0.85rem; }
.hw-quick-card { display: flex; align-items: center; gap: 0.9rem; padding: 1rem 1.1rem; color: var(--md-sys-color-on-surface); background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-lg); transition: transform var(--md-sys-motion-duration-medium) var(--md-sys-motion-easing), box-shadow var(--md-sys-motion-duration-medium), border-color var(--md-sys-motion-duration-medium); }
.hw-quick-card:hover { color: var(--md-sys-color-on-surface); transform: translateY(-3px); box-shadow: var(--md-sys-elevation-3); border-color: color-mix(in srgb, var(--md-sys-color-primary) 45%, var(--md-sys-color-outline-variant)); }
.hw-quick-card > .material-symbols-outlined { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-secondary-container); background: var(--md-sys-color-secondary-container); border-radius: var(--md-sys-shape-md); font-size: 1.5rem; }
.hw-quick-card span.hw-quick-text { font-size: 0.9rem; font-weight: 600; line-height: 1.25; }
__HW_EOF__
escribir "public/css/login.css" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · ACCESO (auth layout)
   Misma familia visual que el layout interno: superficies tonales,
   campos rellenos y botones en píldora.
   ========================================================= */

.auth-shell { min-height: 100vh; display: flex; flex-direction: column; background:
    radial-gradient(900px 500px at 12% -8%, color-mix(in srgb, var(--md-sys-color-primary) 16%, transparent), transparent 60%),
    radial-gradient(800px 520px at 100% 110%, color-mix(in srgb, var(--md-sys-color-tertiary) 14%, transparent), transparent 60%),
    var(--md-sys-color-background); }
.auth-topbar { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; height: var(--hw-topbar-height); padding: 0 1rem; }
.auth-topbar .app-brand { grid-column: 2; }
.auth-topbar-end { grid-column: 3; display: flex; justify-content: flex-end; }
.auth-main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1rem 2rem; }
.auth-footer { padding: 1rem; text-align: center; color: var(--md-sys-color-on-surface-variant); font-size: 0.8rem; }

.login-card { width: 100%; max-width: 1000px; min-height: 610px; overflow: hidden; background-color: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant); border-radius: var(--md-sys-shape-xl); box-shadow: var(--md-sys-elevation-3); animation: hw-rise 0.4s var(--md-sys-motion-easing) both; }

/* Panel institucional */
.login-brand-panel { position: relative; display: flex; flex-direction: column; justify-content: center; padding: 3rem; overflow: hidden; color: var(--md-sys-color-on-primary-container); background: linear-gradient(155deg, var(--md-sys-color-primary-container), color-mix(in srgb, var(--md-sys-color-primary) 28%, var(--md-sys-color-primary-container))); border-right: 1px solid var(--md-sys-color-outline-variant); }
.login-brand-panel::after { content: ""; position: absolute; right: -90px; bottom: -90px; width: 260px; height: 260px; border-radius: 50%; background: color-mix(in srgb, var(--md-sys-color-primary) 18%, transparent); }
.login-brand-content { position: relative; z-index: 1; max-width: 340px; margin: auto; }
.login-brand-icon { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.75rem; color: var(--md-sys-color-on-primary); background-color: var(--md-sys-color-primary); border-radius: var(--md-sys-shape-lg); box-shadow: var(--md-sys-elevation-2); }
.login-brand-icon .material-symbols-outlined { font-size: 2rem; font-variation-settings: 'FILL' 1; }
.login-overline { display: block; margin-bottom: 0.75rem; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.16em; opacity: 0.85; }
.login-brand-title { margin-bottom: 1rem; color: var(--md-sys-color-on-primary-container); font-size: 2rem; font-weight: 700; line-height: 1.15; letter-spacing: -0.035em; }
.login-brand-description { color: var(--md-sys-color-on-primary-container); opacity: 0.85; font-size: 0.95rem; }
.login-brand-divider { height: 1px; margin: 1.75rem 0; background: color-mix(in srgb, var(--md-sys-color-on-primary-container) 25%, transparent); }
.login-feature { display: flex; gap: 0.9rem; margin-bottom: 1.1rem; }
.login-feature > .material-symbols-outlined { width: 42px; height: 42px; flex-shrink: 0; display: grid; place-items: center; color: var(--md-sys-color-on-primary-container); background: color-mix(in srgb, var(--md-sys-color-surface-container-lowest) 55%, transparent); border-radius: var(--md-sys-shape-md); font-size: 1.4rem; }
.login-feature h2 { margin: 0; color: var(--md-sys-color-on-primary-container); font-size: 0.95rem; font-weight: 700; }
.login-feature p { margin: 0.15rem 0 0; color: var(--md-sys-color-on-primary-container); opacity: 0.8; font-size: 0.82rem; }
.login-brand-footer { position: absolute; left: 3rem; bottom: 1.5rem; z-index: 1; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; opacity: 0.75; }

/* Panel de acceso */
.login-form-panel { display: flex; align-items: center; justify-content: center; padding: 3rem 2.5rem; background-color: var(--md-sys-color-surface-container-low); }
.login-form-content { width: 100%; max-width: 380px; }
.login-heading { margin-bottom: 2rem; text-align: center; }
.login-heading-icon { width: 60px; height: 60px; display: grid; place-items: center; margin: 0 auto 1rem; color: var(--md-sys-color-on-primary-container); background-color: var(--md-sys-color-primary-container); border-radius: var(--md-sys-shape-xl); }
.login-heading-icon .material-symbols-outlined { font-size: 2rem; }
.login-heading h2 { margin-bottom: 0.4rem; font-size: 1.6rem; font-weight: 700; letter-spacing: -0.03em; }
.login-mobile-brand { display: none; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.25rem; color: var(--md-sys-color-primary); font-size: 1.2rem; font-weight: 700; }
.login-password-field { position: relative; }
.login-password-field .form-control { padding-right: 3.4rem; }
.login-password-toggle { position: absolute; top: 50%; right: 0.5rem; transform: translateY(-50%); width: 40px; height: 40px; display: grid; place-items: center; color: var(--md-sys-color-on-surface-variant); background: transparent; border: 0; border-radius: var(--md-sys-shape-full); }
.login-password-toggle:hover { color: var(--md-sys-color-primary); background-color: var(--md-sys-state-hover); }
.login-extra-options { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem; font-size: 0.88rem; }
.login-forgot-link { font-weight: 500; }
.login-submit { width: 100%; min-height: 50px; font-size: 0.95rem; }
.login-security-note { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; color: var(--md-sys-color-on-surface-variant); font-size: 0.78rem; }
.login-security-note .material-symbols-outlined { font-size: 1.1rem; color: var(--md-sys-color-primary); }

@media (max-width: 991.98px) {
    .login-card { max-width: 480px; min-height: 0; }
    .login-mobile-brand { display: flex; }
    .login-form-panel { padding: 2.25rem 1.5rem; }
}
__HW_EOF__
escribir "public/css/sidebar-modulos.css" <<'__HW_EOF__'
/* Los estilos del menú lateral ahora viven en app-layout.css (se conserva este archivo vacío para evitar 404 en páginas en caché). */
__HW_EOF__
escribir "public/js/hw/hw-shell.js" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · SHELL DE LA APLICACIÓN
   - Botón de la navbar que oculta/despliega el menú lateral
     (escritorio: colapsa y recuerda el estado; móvil: panel sobre el contenido).
   - Cierre con Esc, con el fondo y al elegir un enlace en móvil.
   ========================================================= */
(() => {
    'use strict';

    const root = document.documentElement;
    const CLAVE = 'hw-nav-collapsed';
    const escritorio = window.matchMedia('(min-width: 992px)');

    const abierto = () => (escritorio.matches ? !root.classList.contains('hw-nav-collapsed') : root.classList.contains('hw-nav-open'));

    function actualizarBoton() {
        const boton = document.getElementById('sidebarToggle');
        if (!boton) return;
        const estaAbierto = abierto();
        boton.setAttribute('aria-expanded', String(estaAbierto));
        boton.setAttribute('aria-label', estaAbierto ? 'Ocultar menú de navegación' : 'Mostrar menú de navegación');
        const icono = boton.querySelector('.material-symbols-outlined');
        if (icono) icono.textContent = estaAbierto && !escritorio.matches ? 'close' : 'menu';
    }

    function alternar() {
        if (escritorio.matches) {
            const colapsado = root.classList.toggle('hw-nav-collapsed');
            try { localStorage.setItem(CLAVE, colapsado ? '1' : '0'); } catch (e) { /* almacenamiento no disponible */ }
        } else {
            root.classList.toggle('hw-nav-open');
        }
        actualizarBoton();
    }

    function cerrarMovil() {
        if (!escritorio.matches && root.classList.contains('hw-nav-open')) {
            root.classList.remove('hw-nav-open');
            actualizarBoton();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sidebarToggle')?.addEventListener('click', alternar);
        document.getElementById('sidebarBackdrop')?.addEventListener('click', cerrarMovil);

        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape') cerrarMovil();
        });

        document.querySelectorAll('#appSidebar a.sidebar-link').forEach((enlace) => {
            enlace.addEventListener('click', cerrarMovil);
        });

        escritorio.addEventListener('change', () => {
            root.classList.remove('hw-nav-open');
            actualizarBoton();
        });

        actualizarBoton();
    });
})();
__HW_EOF__
escribir "public/js/hw/hw-ui.js" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · COMPORTAMIENTOS COMPARTIDOS DE LA INTERFAZ
   - Búsqueda en tiempo real sobre las tablas (con DataTables si está
     cargado y, si no, filtrando las filas directamente). Ignora tildes.
   - Los selectores [data-filtro-columna] siguen siendo manejados por el
     script de cada módulo; este archivo solo coordina el buscador y el
     botón "Limpiar filtros".
   - Cada modal se abre con su cuerpo desplazado al inicio.
   Debe cargarse DESPUÉS de los scripts de los módulos.
   ========================================================= */
(() => {
    'use strict';

    const normalizar = (texto) =>
        String(texto ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

    const consultas = new Map(); // id de tabla -> consulta normalizada
    const columnas = new Map(); // id de tabla -> Map(indice de columna -> valor exacto normalizado)
    const tieneDataTables = () => !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable);

    /* Filtro propio para DataTables: no distingue tildes ni mayúsculas. */
    if (tieneDataTables() && window.jQuery.fn.dataTable?.ext?.search) {
        window.jQuery.fn.dataTable.ext.search.push((configuracion, datos) => {
            const id = configuracion.nTable?.id ?? '';
            const porColumna = columnas.get(id);
            if (porColumna) {
                for (const [indice, valor] of porColumna) {
                    if (valor && normalizar(String(datos[indice] ?? '').replace(/\s+/g, ' ')) !== valor) return false;
                }
            }
            const consulta = consultas.get(id);
            if (!consulta) return true;
            const fila = normalizar(datos.join(' '));
            return consulta.split(/\s+/).every((palabra) => fila.includes(palabra));
        });
    }

    function tablaDe(elemento) {
        const superficie = elemento.closest('.hw-surface') || document;
        return superficie.querySelector('table.hw-data-table, table.table');
    }

    function filtrarManual(tabla, consulta) {
        const cuerpo = tabla.tBodies[0];
        if (!cuerpo) return 0;
        const porColumna = columnas.get(tabla.id);
        let visibles = 0;
        Array.from(cuerpo.rows).forEach((fila) => {
            let coincide = !consulta || consulta.split(/\s+/).every((p) => normalizar(fila.textContent).includes(p));
            if (coincide && porColumna) {
                for (const [indice, valor] of porColumna) {
                    const celda = fila.cells[indice];
                    if (valor && normalizar((celda?.textContent ?? '').replace(/\s+/g, ' ')) !== valor) { coincide = false; break; }
                }
            }
            fila.hidden = !coincide;
            if (coincide) visibles += 1;
        });
        return visibles;
    }

    function actualizarEstado(buscador, tabla, total, visibles) {
        const contador = buscador.closest('.hw-toolbar')?.querySelector('[data-hw-contador]');
        if (contador) {
            contador.textContent = total === visibles ? `${total} registros` : `${visibles} de ${total} registros`;
        }
        const vacio = buscador.closest('.hw-surface')?.querySelector('.hw-no-results');
        if (vacio) vacio.style.display = visibles === 0 ? 'block' : 'none';
    }

    function aplicarBusqueda(buscador) {
        const tabla = tablaDe(buscador);
        if (!tabla) return;

        const consulta = normalizar(buscador.value);
        consultas.set(tabla.id, consulta);
        buscador.closest('.hw-search')?.classList.toggle('has-value', consulta !== '');

        if (tieneDataTables() && window.jQuery.fn.DataTable.isDataTable(tabla)) {
            const api = window.jQuery(tabla).DataTable();
            api.draw();
            const info = api.page.info();
            actualizarEstado(buscador, tabla, info.recordsTotal, info.recordsDisplay);
            return;
        }

        const total = tabla.tBodies[0] ? tabla.tBodies[0].rows.length : 0;
        actualizarEstado(buscador, tabla, total, filtrarManual(tabla, consulta));
    }

    function inicializarBuscadores() {
        document.querySelectorAll('[data-hw-buscar]').forEach((buscador) => {
            buscador.addEventListener('input', () => aplicarBusqueda(buscador));
            buscador.addEventListener('keydown', (evento) => {
                if (evento.key === 'Escape') {
                    buscador.value = '';
                    aplicarBusqueda(buscador);
                }
            });

            buscador.closest('.hw-search')?.querySelector('[data-hw-buscar-limpiar]')?.addEventListener('click', () => {
                buscador.value = '';
                aplicarBusqueda(buscador);
                buscador.focus();
            });

            // El botón "Limpiar filtros" de cada módulo también vacía el buscador.
            buscador.closest('.hw-toolbar')?.querySelectorAll('[data-filtros-limpiar]').forEach((boton) => {
                boton.addEventListener('click', () => {
                    buscador.value = '';
                    window.setTimeout(() => aplicarBusqueda(buscador), 0);
                });
            });

            // Mantener el contador al cambiar un selector de columna.
            buscador.closest('.hw-toolbar')?.querySelectorAll('[data-filtro-columna]').forEach((select) => {
                select.addEventListener('change', () => window.setTimeout(() => aplicarBusqueda(buscador), 0));
            });

            aplicarBusqueda(buscador);
        });
    }

    /* Filtros [data-hw-filtro-columna="n"]: rellenan sus opciones con los valores de la columna. */
    function inicializarFiltrosColumna() {
        document.querySelectorAll('select[data-hw-filtro-columna]').forEach((select) => {
            const tabla = tablaDe(select);
            if (!tabla || !tabla.tBodies[0]) return;
            const indice = Number(select.dataset.hwFiltroColumna);

            const valores = new Set();
            Array.from(tabla.tBodies[0].rows).forEach((fila) => {
                const texto = (fila.cells[indice]?.textContent ?? '').replace(/\s+/g, ' ').trim();
                if (texto) valores.add(texto);
            });
            [...valores].sort((a, b) => a.localeCompare(b, 'es')).forEach((valor) => {
                const opcion = document.createElement('option');
                opcion.value = valor;
                opcion.textContent = valor;
                select.appendChild(opcion);
            });

            select.addEventListener('change', () => {
                const mapa = columnas.get(tabla.id) ?? new Map();
                mapa.set(indice, normalizar(select.value));
                columnas.set(tabla.id, mapa);
                const buscador = select.closest('.hw-toolbar')?.querySelector('[data-hw-buscar]');
                if (buscador) aplicarBusqueda(buscador);
            });
        });

        // "Limpiar filtros" también reinicia los filtros propios.
        document.querySelectorAll('[data-filtros-limpiar]').forEach((boton) => {
            boton.addEventListener('click', () => {
                const barra = boton.closest('.hw-toolbar');
                barra?.querySelectorAll('select[data-hw-filtro-columna]').forEach((select) => { select.value = ''; });
                const tabla = barra ? tablaDe(barra) : null;
                if (tabla) columnas.delete(tabla.id);
                const buscador = barra?.querySelector('[data-hw-buscar]');
                if (buscador) window.setTimeout(() => aplicarBusqueda(buscador), 0);
            });
        });
    }

    function inicializarModales() {
        document.querySelectorAll('.hw-modal').forEach((modal) => {
            modal.addEventListener('show.bs.modal', () => {
                modal.querySelectorAll('.hw-modal-body').forEach((cuerpo) => { cuerpo.scrollTop = 0; });
            });
            modal.addEventListener('shown.bs.modal', () => {
                const primero = modal.querySelector('.hw-modal-body input:not([type="hidden"]):not([disabled]), .hw-modal-body select:not([disabled])');
                if (primero && !modal.querySelector('[data-id]')) primero.focus({ preventScroll: true });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        inicializarFiltrosColumna();
        inicializarBuscadores();
        inicializarModales();
    });
})();
__HW_EOF__
escribir "public/js/hw/hw-facturacion.js" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · FACTURACIÓN: emisión con pago único
   Conceptos dinámicos (servicio, cantidad, precio), total en vivo
   y validación mínima antes de enviar. El envío AJAX lo hace el
   script del módulo (facturas.js) sobre #formNuevo.
   ========================================================= */
(() => {
    'use strict';

    const dinero = (n) => `$ ${Number(n || 0).toFixed(2)}`;

    document.addEventListener('DOMContentLoaded', () => {
        const formulario = document.querySelector('form[data-hw-factura]');
        if (!formulario) return;

        const contenedor = formulario.querySelector('[data-hw-lineas]');
        const plantilla = document.getElementById('hwLineaPlantilla');
        const botonAgregar = formulario.querySelector('[data-hw-agregar-linea]');
        const modal = formulario.closest('.modal');
        if (!contenedor || !plantilla) return;

        let indice = 0;

        const total = () => {
            let suma = 0;
            contenedor.querySelectorAll('[data-hw-linea]').forEach((linea) => {
                const cantidad = parseFloat(linea.querySelector('[data-hw-cantidad]')?.value) || 0;
                const precio = parseFloat(linea.querySelector('[data-hw-precio]')?.value) || 0;
                suma += cantidad * precio;
            });
            return Math.round(suma * 100) / 100;
        };

        function recalcular() {
            const valor = total();
            formulario.querySelectorAll('[data-hw-subtotal], [data-hw-total]').forEach((nodo) => { nodo.textContent = dinero(valor); });
            [['[data-hw-campo-subtotal]', valor], ['[data-hw-campo-total]', valor], ['[data-hw-campo-pagado]', valor]].forEach(([selector, v]) => {
                const campo = formulario.querySelector(selector);
                if (campo) campo.value = v.toFixed(2);
            });
        }

        function agregar() {
            const contenido = document.createElement('div');
            contenido.innerHTML = plantilla.innerHTML.replaceAll('__I__', String(indice++)).trim();
            const linea = contenido.firstElementChild;
            contenedor.appendChild(linea);
            recalcular();
            return linea;
        }

        function reiniciar() {
            contenedor.innerHTML = '';
            indice = 0;
            agregar();
        }

        botonAgregar?.addEventListener('click', agregar);

        contenedor.addEventListener('click', (evento) => {
            const quitar = evento.target.closest('[data-hw-quitar-linea]');
            if (!quitar) return;
            quitar.closest('[data-hw-linea]')?.remove();
            recalcular();
        });

        contenedor.addEventListener('change', (evento) => {
            if (evento.target.matches('[data-hw-servicio]')) {
                const linea = evento.target.closest('[data-hw-linea]');
                const precio = linea?.querySelector('[data-hw-precio]');
                const referencia = evento.target.selectedOptions[0]?.dataset.precio;
                if (precio && referencia !== undefined) precio.value = Number(referencia).toFixed(2);
            }
            recalcular();
        });
        contenedor.addEventListener('input', recalcular);

        // Validación mínima antes de que el módulo envíe por AJAX (fase de captura)
        formulario.addEventListener('submit', (evento) => {
            let error = '';
            if (!contenedor.querySelector('[data-hw-linea]')) {
                error = 'Agrega al menos un concepto a la factura.';
            } else if (total() <= 0) {
                error = 'El total de la factura debe ser mayor que cero.';
            }
            if (!error) return;

            evento.preventDefault();
            evento.stopImmediatePropagation();
            const resumen = formulario.querySelector('[data-form-errors]');
            if (resumen) {
                resumen.classList.remove('d-none');
                resumen.textContent = error;
            }
        }, true);

        modal?.addEventListener('show.bs.modal', () => { if (!contenedor.children.length) reiniciar(); });
        modal?.addEventListener('hidden.bs.modal', () => window.setTimeout(reiniciar, 0));
        formulario.addEventListener('reset', () => window.setTimeout(reiniciar, 0));

        reiniciar();
    });
})();
__HW_EOF__
escribir "public/js/usuario/usuarios-medico.js" <<'__HW_EOF__'
/* =========================================================
   HEALTHWARE · USUARIOS: PERFIL PROFESIONAL DEL MÉDICO
   Si el rol elegido es "Médico" se despliega la sección del
   perfil de doctor (la tabla doctores hereda de usuarios), de modo
   que nunca exista una cuenta con rol Médico sin su perfil.

   - Los campos viajan como doctor[campo] en la MISMA petición que
     la cuenta de usuario (el controlador crea ambos en una transacción).
   - Los campos de la sección oculta se deshabilitan: no se envían.
   - Se valida ANTES del script de usuarios (fase de captura).
   - Al editar, la sección se rellena si la respuesta trae "doctor".
   ========================================================= */
(() => {
    'use strict';

    const normalizar = (t) => String(t ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();

    const CAMPOS = {
        especialidad_id: { etiqueta: 'La especialidad', requerido: true },
        numero_licencia: { etiqueta: 'El número de licencia', requerido: true, max: 50 },
        dui: { etiqueta: 'El DUI', requerido: true, max: 10, patron: /^\d{8}-?\d$/, mensaje: 'El DUI debe tener el formato 00000000-0.' },
        fecha_nacimiento: { etiqueta: 'La fecha de nacimiento', requerido: true, fecha: true },
        sexo: { etiqueta: 'El sexo', requerido: true },
        descripcion_profesional: { etiqueta: 'La descripción', requerido: false, max: 500 },
    };

    const esRolMedico = (select) => {
        const opcion = select?.options?.[select.selectedIndex];
        if (!opcion || !opcion.value) return false;
        return normalizar(opcion.dataset.rol || opcion.textContent) === 'medico';
    };

    const campo = (formulario, nombre) => formulario.querySelector(`[name="doctor[${nombre}]"]`);

    function alternarSeccion(formulario) {
        const seccion = formulario.querySelector('[data-doctor-section]');
        const selectRol = formulario.querySelector('select[name="id_rol"]');
        if (!seccion || !selectRol) return;

        const visible = esRolMedico(selectRol);
        seccion.hidden = !visible;
        seccion.querySelectorAll('input, select, textarea').forEach((control) => { control.disabled = !visible; });
        if (!visible) limpiarErrores(seccion);

        const aviso = formulario.querySelector('[data-doctor-aviso]');
        if (aviso) aviso.hidden = !visible;
    }

    function limpiarErrores(contenedor) {
        contenedor.querySelectorAll('.is-invalid').forEach((c) => { c.classList.remove('is-invalid'); c.removeAttribute('aria-invalid'); });
        contenedor.querySelectorAll('[data-field-error]').forEach((e) => e.remove());
    }

    function marcarError(control, mensaje) {
        control.classList.add('is-invalid');
        control.setAttribute('aria-invalid', 'true');
        const contenedor = control.closest('.form-floating') || control;
        contenedor.insertAdjacentHTML('afterend', '<div class="invalid-feedback d-block" data-field-error></div>');
        contenedor.nextElementSibling.textContent = mensaje;
    }

    function validar(formulario, esEdicion) {
        const seccion = formulario.querySelector('[data-doctor-section]');
        if (!seccion || seccion.hidden) return true;
        limpiarErrores(seccion);

        let correcto = true;
        Object.entries(CAMPOS).forEach(([nombre, regla]) => {
            const control = campo(formulario, nombre);
            if (!control) return;
            const valor = String(control.value ?? '').trim();
            let error = '';

            // En edición el perfil existente puede no venir en la respuesta: no se bloquea el guardado.
            if (!valor) {
                if (regla.requerido && !esEdicion) error = `${regla.etiqueta} es obligatoria.`;
            } else if (regla.max && valor.length > regla.max) {
                error = `${regla.etiqueta} no puede superar los ${regla.max} caracteres.`;
            } else if (regla.patron && !regla.patron.test(valor)) {
                error = regla.mensaje;
            } else if (regla.fecha && new Date(valor) > new Date()) {
                error = 'La fecha de nacimiento no puede ser futura.';
            }

            if (error) {
                marcarError(control, error);
                correcto = false;
            }
        });

        if (!correcto) {
            const resumen = formulario.querySelector('[data-form-errors]');
            if (resumen) {
                resumen.classList.remove('d-none');
                resumen.textContent = 'Completa el perfil profesional del médico antes de continuar.';
            }
            seccion.querySelector('.is-invalid')?.focus();
        }
        return correcto;
    }

    function rellenar(formulario, usuario) {
        const doctor = usuario?.doctor;
        alternarSeccion(formulario);
        if (!doctor) return;
        Object.keys(CAMPOS).forEach((nombre) => {
            const control = campo(formulario, nombre);
            if (control && doctor[nombre] !== undefined && doctor[nombre] !== null) {
                control.value = nombre === 'fecha_nacimiento' ? String(doctor[nombre]).slice(0, 10) : doctor[nombre];
            }
        });
    }

    function iniciar() {
        document.querySelectorAll('form').forEach((formulario) => {
            if (!formulario.querySelector('[data-doctor-section]')) return;
            const esEdicion = /editar|modificar/i.test(formulario.id);

            formulario.querySelector('select[name="id_rol"]')?.addEventListener('change', () => alternarSeccion(formulario));
            formulario.addEventListener('reset', () => window.setTimeout(() => alternarSeccion(formulario), 0));

            // Fase de captura: se ejecuta antes que el manejador de envío de usuarios.js
            formulario.addEventListener('submit', (evento) => {
                alternarSeccion(formulario);
                if (!validar(formulario, esEdicion)) {
                    evento.preventDefault();
                    evento.stopImmediatePropagation();
                }
            }, true);

            // Limpiar el error de un campo al corregirlo
            formulario.addEventListener('input', (evento) => {
                const control = evento.target;
                if (control.name?.startsWith('doctor[') && control.classList.contains('is-invalid')) {
                    control.classList.remove('is-invalid');
                    (control.closest('.form-floating') || control).nextElementSibling?.matches('[data-field-error]') &&
                        (control.closest('.form-floating') || control).nextElementSibling.remove();
                }
            });

            alternarSeccion(formulario);
        });

        // Tras cargar un usuario para editarlo, el rol cambia por código: reflejarlo.
        if (window.jQuery) {
            window.jQuery(document).ajaxSuccess((evento, xhr, configuracion, datos) => {
                if (configuracion.type !== 'GET' || !datos || typeof datos !== 'object' || datos.id_rol === undefined) return;
                const formulario = document.querySelector('form[id*="Editar"], form[id*="Modificar"]');
                if (formulario && formulario.querySelector('[data-doctor-section]')) {
                    window.setTimeout(() => rellenar(formulario, datos), 30);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', iniciar);
    window.HWUsuariosMedico = { alternarSeccion, rellenar };
})();
__HW_EOF__
escribir "resources/views/layouts/app_layout.blade.php" <<'__HW_EOF__'
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- Tema y estado del menú lateral, antes de pintar (evita parpadeos) --}}
    <script src="{{ asset('js/global-scripts.js') }}"></script>
    <script>
        (function () {
            try {
                if (window.matchMedia('(min-width: 992px)').matches && localStorage.getItem('hw-nav-collapsed') === '1') {
                    document.documentElement.classList.add('hw-nav-collapsed');
                }
            } catch (e) { /* almacenamiento no disponible */ }
        })();
    </script>

    {{-- Framework y sistema de diseño --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">

    {{-- Material Symbols --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    {{-- Estilos de librerías y particulares de cada vista --}}
    @stack('estilos')

    {{-- Componentes de vista: se cargan al final para prevalecer sobre las librerías --}}
    <link rel="stylesheet" href="{{ asset('css/hw-components.css') }}">
</head>

<body class="app-body">

@php
    /*
    |--------------------------------------------------------------------------
    | Usuario autenticado
    |--------------------------------------------------------------------------
    */
    $usuario = Auth::user();

    $nombreUsuario = trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? ''));
    $nombreUsuario = $nombreUsuario ?: 'Usuario';

    $iniciales = mb_strtoupper(
        mb_substr($usuario->nombres ?? 'U', 0, 1) .
        mb_substr($usuario->apellidos ?? '', 0, 1)
    );

    $rolUsuario = mb_strtolower(trim($usuario->rol->rol ?? ''));

    /*
    |--------------------------------------------------------------------------
    | Menú (única fuente: layouts/partials/menu_config.php)
    |--------------------------------------------------------------------------
    */
    $configuracionMenu = require resource_path('views/layouts/partials/menu_config.php');
    $menuModulos = $configuracionMenu['modulos'];

    $puedeVer = function (array $enlace) use ($rolUsuario) {
        return \Illuminate\Support\Facades\Route::has($enlace['ruta'])
            && in_array($rolUsuario, $enlace['roles'], true);
    };

    $accesosRapidos = array_values(array_filter($configuracionMenu['accesos_rapidos'], $puedeVer));

    /*
    |--------------------------------------------------------------------------
    | Notificaciones del usuario (no rompe la página si el modelo no existe)
    |--------------------------------------------------------------------------
    */
    $notifPendientes = 0;
    $notifRecientes = collect();

    $claseNotificacion = collect(['\\App\\Models\\Notificacion', '\\App\\Models\\Notificaciones'])
        ->first(fn ($clase) => class_exists($clase));

    if ($claseNotificacion && $usuario) {
        try {
            $consultaNotificaciones = $claseNotificacion::query()->where('id_usuario', $usuario->id);
            $notifPendientes = (clone $consultaNotificaciones)->whereNull('leido_en')->count();
            $notifRecientes = (clone $consultaNotificaciones)->orderByDesc('created_at')->limit(5)->get();
        } catch (\Throwable $e) {
            $notifPendientes = 0;
            $notifRecientes = collect();
        }
    }

    $iconoNotificacion = function ($notificacion) {
        $tipo = mb_strtolower((string) ($notificacion->tipo ?? ''));
        return match (true) {
            str_contains($tipo, 'cita') => 'event',
            str_contains($tipo, 'factur') || str_contains($tipo, 'pago') => 'receipt_long',
            str_contains($tipo, 'receta') => 'medication',
            str_contains($tipo, 'anuncio') => 'campaign',
            str_contains($tipo, 'alerta') || str_contains($tipo, 'urgente') => 'warning',
            default => 'notifications',
        };
    };
@endphp

{{-- =====================================================
     BARRA SUPERIOR: menú · marca centrada · acciones
====================================================== --}}
<header class="app-topbar">

    {{-- Izquierda: botón del menú lateral + menús extra --}}
    <div class="app-topbar-start">

        <button
            type="button"
            class="btn btn-icon app-menu-toggle"
            id="sidebarToggle"
            aria-controls="appSidebar"
            aria-expanded="true"
            aria-label="Mostrar u ocultar el menú de navegación">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <nav class="app-topnav" aria-label="Accesos de la barra superior">

            <a href="{{ route('home') }}"
               class="app-topnav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <span class="material-symbols-outlined">home</span>
                Inicio
            </a>

            @if(\Illuminate\Support\Facades\Route::has('dashboard'))
                <a href="{{ route('dashboard') }}"
                   class="app-topnav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
            @endif

            @if(count($accesosRapidos))
                <div class="dropdown">
                    <button type="button"
                            class="app-topnav-link dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <span class="material-symbols-outlined">bolt</span>
                        Accesos rápidos
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>

                    <ul class="dropdown-menu">
                        @foreach($accesosRapidos as $acceso)
                            <li>
                                <a class="dropdown-item" href="{{ route($acceso['ruta']) }}">
                                    <span class="material-symbols-outlined">{{ $acceso['icono'] }}</span>
                                    {{ $acceso['etiqueta'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </nav>
    </div>

    {{-- Centro: nombre del sistema --}}
    <div class="app-topbar-center">
        <a href="{{ route('home') }}" class="app-brand" aria-label="HealthWare, ir al inicio">
            <span class="material-symbols-outlined app-brand-icon">health_and_safety</span>
            <span class="app-brand-name">HealthWare</span>
        </a>
    </div>

    {{-- Derecha: notificaciones · tema · usuario --}}
    <div class="app-topbar-end">

        @if(\Illuminate\Support\Facades\Route::has('notificaciones'))
            <div class="dropdown app-notif">
                <button type="button"
                        class="btn btn-icon app-notif-button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                        aria-label="Notificaciones{{ $notifPendientes ? ': ' . $notifPendientes . ' sin leer' : '' }}"
                        title="Notificaciones">
                    <span class="material-symbols-outlined">notifications</span>
                    @if($notifPendientes > 0)
                        <span class="app-notif-badge">{{ $notifPendientes > 99 ? '99+' : $notifPendientes }}</span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-end app-notif-menu">

                    <div class="app-notif-header">
                        <h2>Notificaciones</h2>
                        <span>{{ $notifPendientes }} sin leer</span>
                    </div>

                    <div class="app-notif-list">
                        @forelse($notifRecientes as $notificacion)
                            @php $sinLeer = empty($notificacion->leido_en); @endphp
                            <a href="{{ route('notificaciones') }}"
                               class="app-notif-item {{ $sinLeer ? 'is-unread' : '' }}">
                                <span class="app-notif-icon">
                                    <span class="material-symbols-outlined">{{ $iconoNotificacion($notificacion) }}</span>
                                </span>
                                <span class="app-notif-body">
                                    <span class="app-notif-title">{{ $notificacion->titulo ?? 'Notificación' }}</span>
                                    <span class="app-notif-text">{{ $notificacion->mensaje ?? '' }}</span>
                                    <span class="app-notif-time">
                                        {{ $notificacion->created_at ? \Illuminate\Support\Carbon::parse($notificacion->created_at)->locale('es')->diffForHumans() : '' }}
                                    </span>
                                </span>
                                @if($sinLeer)
                                    <span class="app-notif-dot" aria-hidden="true"></span>
                                @endif
                            </a>
                        @empty
                            <div class="app-notif-empty">
                                <span class="material-symbols-outlined">notifications_off</span>
                                No tienes notificaciones recientes.
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('notificaciones') }}" class="app-notif-footer">Ver todas las notificaciones</a>
                </div>
            </div>
        @endif

        {{-- Cambio de tema --}}
        <button
            type="button"
            class="btn btn-icon app-theme-button"
            id="themeToggleBtn"
            aria-label="Cambiar tema"
            aria-pressed="false"
            title="Cambiar tema">
            <span class="material-symbols-outlined" id="themeIcon">dark_mode</span>
        </button>

        {{-- Menú de usuario --}}
        <div class="dropdown app-user-dropdown">

            <button
                type="button"
                class="app-user-trigger"
                data-bs-toggle="dropdown"
                data-bs-auto-close="true"
                aria-expanded="false"
                aria-label="Abrir menú de usuario">

                <span class="app-user-avatar">{{ $iniciales ?: 'U' }}</span>

                <span class="app-user-summary d-none d-md-flex">
                    <span class="app-user-name">{{ $nombreUsuario }}</span>
                    <span class="app-user-role">{{ $usuario->rol->rol ?? 'Usuario' }}</span>
                </span>

                <span class="material-symbols-outlined app-user-chevron d-none d-md-inline">expand_more</span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end app-user-menu">

                <li>
                    <div class="app-user-menu-header">
                        <span class="app-user-avatar app-user-avatar-large">{{ $iniciales ?: 'U' }}</span>
                        <div class="app-user-menu-identity">
                            <span class="app-user-menu-name">{{ $nombreUsuario }}</span>
                            <span class="app-user-menu-email">{{ $usuario->correo ?? '' }}</span>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="app-user-role-badge">
                        <span class="material-symbols-outlined">verified_user</span>
                        {{ $usuario->rol->rol ?? 'Usuario' }}
                    </div>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item app-logout-button">
                            <span class="material-symbols-outlined">logout</span>
                            Cerrar sesión
                        </button>
                    </form>
                </li>

            </ul>
        </div>

    </div>

</header>


{{-- =====================================================
     ESPACIO DE TRABAJO
====================================================== --}}
<div class="app-workspace">

    {{-- MENÚ LATERAL (se oculta y despliega con el botón de la barra superior) --}}
    <aside class="app-sidebar" id="appSidebar" aria-label="Navegación principal">

        <div class="app-sidebar-body">

            <div class="sidebar-heading">
                <span class="sidebar-section-label">Menú principal</span>
            </div>

            <nav class="app-navigation">

                <a href="{{ route('home') }}"
                   class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span class="material-symbols-outlined sidebar-icon">home</span>
                    <span>Inicio</span>
                </a>

                @if(\Illuminate\Support\Facades\Route::has('dashboard'))
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined sidebar-icon">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                @endif

                @foreach ($menuModulos as $menuModulo)

                    @php
                        // Solo enlaces con ruta existente y permitidos para el rol
                        $menuEnlaces = array_values(array_filter($menuModulo['enlaces'], $puedeVer));

                        $menuActivo = false;

                        foreach ($menuEnlaces as $menuEnlace) {
                            $patrones = [$menuEnlace['ruta'], $menuEnlace['ruta'] . '.*'];

                            foreach ($menuEnlace['rutas_activas'] ?? [] as $rutaActiva) {
                                $patrones[] = $rutaActiva;
                                $patrones[] = $rutaActiva . '.*';
                            }

                            if (request()->routeIs(...$patrones)) {
                                $menuActivo = true;
                                break;
                            }
                        }
                    @endphp

                    @continue($menuEnlaces === [])

                    <div class="sidebar-module">

                        <button
                            type="button"
                            class="sidebar-link sidebar-group-toggle {{ $menuActivo ? 'has-active' : 'collapsed' }}"
                            data-bs-toggle="collapse"
                            data-bs-target="#modulo-{{ $menuModulo['id'] }}"
                            aria-expanded="{{ $menuActivo ? 'true' : 'false' }}"
                            aria-controls="modulo-{{ $menuModulo['id'] }}">

                            <span class="material-symbols-outlined sidebar-icon">{{ $menuModulo['icono'] }}</span>
                            <span class="sidebar-link-label">{{ $menuModulo['titulo'] }}</span>
                            <span class="material-symbols-outlined sidebar-chevron">expand_more</span>
                        </button>

                        <div class="collapse sidebar-submenu {{ $menuActivo ? 'show' : '' }}"
                             id="modulo-{{ $menuModulo['id'] }}">

                            @foreach ($menuEnlaces as $menuEnlace)

                                @php
                                    $patronesEnlace = [$menuEnlace['ruta'], $menuEnlace['ruta'] . '.*'];

                                    foreach ($menuEnlace['rutas_activas'] ?? [] as $rutaActiva) {
                                        $patronesEnlace[] = $rutaActiva;
                                        $patronesEnlace[] = $rutaActiva . '.*';
                                    }

                                    $enlaceActivo = request()->routeIs(...$patronesEnlace);
                                @endphp

                                <a href="{{ route($menuEnlace['ruta']) }}"
                                   class="sidebar-link sidebar-sublink {{ $enlaceActivo ? 'active' : '' }}">
                                    <span class="material-symbols-outlined sidebar-icon">{{ $menuEnlace['icono'] }}</span>
                                    <span>{{ $menuEnlace['etiqueta'] }}</span>
                                </a>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </nav>

            <footer class="app-sidebar-footer">
                <span class="app-sidebar-footer-brand">HealthWare</span>
                <span class="app-sidebar-footer-version">Sistema de Gestión Clínica · v1.0.0</span>
                <span class="app-sidebar-copyright">&copy; {{ date('Y') }} HealthWare</span>
            </footer>

        </div>

    </aside>

    <div class="app-sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    {{-- PANTALLA INTERNA --}}
    <main class="app-screen">
        <div class="app-screen-content">
            @yield('contenido')
        </div>
    </main>

</div>


{{-- COMPONENTES REUTILIZABLES --}}
@include('components.toast')


{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>

@stack('scripts')

{{-- Comportamientos compartidos: se cargan después de los scripts de cada módulo --}}
<script src="{{ asset('js/hw/hw-shell.js') }}"></script>
<script src="{{ asset('js/hw/hw-ui.js') }}"></script>

</body>
</html>
__HW_EOF__
escribir "resources/views/layouts/auth_layout.blade.php" <<'__HW_EOF__'
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- Inicialización temprana del tema --}}
    <script src="{{ asset('js/global-scripts.js') }}"></script>

    {{-- Framework y sistema de diseño (mismo que el layout interno) --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    {{-- Estilos particulares de cada vista --}}
    @stack('estilos')
</head>

<body class="auth-shell">

    {{-- Barra superior: misma composición que la interna (marca al centro, tema a la derecha) --}}
    <header class="auth-topbar">
        <a href="{{ url('/') }}" class="app-brand" aria-label="HealthWare">
            <span class="material-symbols-outlined app-brand-icon">health_and_safety</span>
            <span class="app-brand-name">HealthWare</span>
        </a>

        <div class="auth-topbar-end">
            <button
                type="button"
                class="btn btn-icon"
                id="themeToggleBtn"
                title="Cambiar tema"
                aria-label="Cambiar tema"
                aria-pressed="false">
                <span class="material-symbols-outlined" id="themeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <main class="auth-main">
        @yield('contenido')
    </main>

    <footer class="auth-footer">
        &copy; {{ date('Y') }} HealthWare · Sistema de Gestión Clínica
    </footer>

    @include('components.toast')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}"></script>

    @stack('scripts')

</body>
</html>
__HW_EOF__
escribir "resources/views/layouts/guest_layout.blade.php" <<'__HW_EOF__'
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- Inicialización temprana del tema --}}
    <script src="{{ asset('js/global-scripts.js') }}"></script>

    {{-- Framework y sistema de diseño (mismo que el layout interno) --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    {{-- Estilos particulares de cada vista --}}
    @stack('estilos')
</head>

<body class="auth-shell">

    {{-- Páginas de invitado: comparten la composición del layout de acceso --}}
    <header class="auth-topbar">
        <a href="{{ url('/') }}" class="app-brand" aria-label="HealthWare">
            <span class="material-symbols-outlined app-brand-icon">health_and_safety</span>
            <span class="app-brand-name">HealthWare</span>
        </a>

        <div class="auth-topbar-end">
            <button
                type="button"
                class="btn btn-icon"
                id="themeToggleBtn"
                title="Cambiar tema"
                aria-label="Cambiar tema"
                aria-pressed="false">
                <span class="material-symbols-outlined" id="themeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <main class="auth-main">
        @yield('contenido')
    </main>

    <footer class="auth-footer">
        &copy; {{ date('Y') }} HealthWare · Sistema de Gestión Clínica
    </footer>

    @include('components.toast')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}"></script>

    @stack('scripts')

</body>
</html>
__HW_EOF__

HOME_VISTA="$RAIZ/resources/views/home/index.blade.php"
if [[ ! -f "$HOME_VISTA" ]] || grep -q "Ruta: home.index" "$HOME_VISTA" || ! grep -q "hw-hero" "$HOME_VISTA"; then
escribir "resources/views/home/index.blade.php" <<'__HW_EOF__'
@extends('layouts.app_layout')

@section('titulo', 'Inicio')

@section('contenido')

    @php
        $usuarioActual = auth()->user();
        $rolInicio = mb_strtolower(trim($usuarioActual->rol->rol ?? ''));
        $configuracionInicio = require resource_path('views/layouts/partials/menu_config.php');

        // Solo los módulos con ruta existente y permitidos para el rol del usuario
        $gruposInicio = [];
        foreach ($configuracionInicio['modulos'] as $moduloInicio) {
            $enlacesInicio = array_values(array_filter(
                $moduloInicio['enlaces'],
                fn ($enlace) => \Illuminate\Support\Facades\Route::has($enlace['ruta'])
                    && in_array($rolInicio, $enlace['roles'], true)
            ));

            if ($enlacesInicio !== []) {
                $gruposInicio[] = [
                    'titulo' => $moduloInicio['titulo'],
                    'icono' => $moduloInicio['icono'],
                    'enlaces' => $enlacesInicio,
                ];
            }
        }

        $horaInicio = (int) now()->format('H');
        $saludoInicio = $horaInicio < 12 ? 'Buenos días' : ($horaInicio < 19 ? 'Buenas tardes' : 'Buenas noches');
    @endphp

    <div class="hw-page">

        <section class="hw-hero">
            <div>
                <h1>{{ $saludoInicio }}, {{ $usuarioActual->nombres ?? 'bienvenido' }}</h1>
                <p>
                    Bienvenido a HealthWare, el sistema de gestión clínica. Desde aquí puedes
                    ir directamente a los módulos disponibles para tu rol.
                </p>
            </div>

            <span class="hw-hero-badge">
                <span class="material-symbols-outlined">verified_user</span>
                {{ $usuarioActual->rol->rol ?? 'Usuario' }}
            </span>
        </section>

        @forelse($gruposInicio as $grupoInicio)
            <section class="hw-quick-section">
                <h2>
                    <span class="material-symbols-outlined">{{ $grupoInicio['icono'] }}</span>
                    {{ $grupoInicio['titulo'] }}
                </h2>

                <div class="hw-quick-grid">
                    @foreach($grupoInicio['enlaces'] as $enlaceInicio)
                        <a href="{{ route($enlaceInicio['ruta']) }}" class="hw-quick-card">
                            <span class="material-symbols-outlined">{{ $enlaceInicio['icono'] }}</span>
                            <span class="hw-quick-text">{{ $enlaceInicio['etiqueta'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @empty
            <section class="hw-surface">
                <div class="hw-empty">
                    <span class="material-symbols-outlined">lock</span>
                    <h3>No hay módulos disponibles</h3>
                    <p>Tu rol todavía no tiene accesos asignados. Contacta con el administrador.</p>
                </div>
            </section>
        @endforelse

    </div>

@endsection
__HW_EOF__
fi

info "Reestructurando las vistas de los modulos (sin tocar la logica)"
python3 "$TMP/reestructurar.py" "$RAIZ"

cat > "$RAIZ/TASKLIST_UI.txt" <<'__HW_EOF__'
TASKLIST - REESTRUCTURACION VISUAL DE HEALTHWARE
=================================================
Respaldo del estado anterior: .respaldos-ui/<fecha>/  (public-css, public-js, resources-views)

CARGA (despues de ejecutar el script)
[ ] 1. php artisan view:clear   y recargar el navegador con Ctrl+F5 (los CSS cambiaron de nombre/contenido).
[ ] 2. Iniciar sesion con cada rol (administrador, medico, recepcionista): el menu lateral solo muestra lo permitido (menu_config.php).
[ ] 3. Boton de menu (izquierda de la barra): oculta/despliega el menu en escritorio (recuerda el estado) y en movil abre un panel.
[ ] 4. Marca "HealthWare" al centro, accesos rapidos (Inicio, Dashboard, Accesos rapidos), tema claro/oscuro y menu de usuario.
[ ] 5. Campana: muestra el contador de pendientes y las 5 ultimas. Requiere el modelo App\Models\Notificacion (o Notificaciones)
       con columnas id_usuario, titulo, mensaje, tipo, leido_en, created_at. Si el modelo tiene otro nombre, ajustar la lista $claseNotificacion
       en resources/views/layouts/app_layout.blade.php.

MODULOS
[ ] 6. Cada listado: buscador en tiempo real (sin distinguir tildes), filtros por columna, "Limpiar filtros", contador de resultados.
[ ] 7. Modales (nuevo/editar/consultar/eliminar): el cuerpo se desplaza internamente; cabecera y botones quedan siempre visibles.
       Etiquetas flotantes correctas en selectores y fechas (sin superponerse a los valores).
[ ] 8. Formularios con validacion y errores por campo (el JS de cada modulo no cambio).
[ ] 9. Usuarios: al elegir el rol Medico aparece "Perfil profesional del medico" (en nuevo y en editar). Ver CONTROLADOR abajo.
[ ] 10. Facturacion: indicadores, boton "Exportar a PDF" (solo si existe la ruta facturas.pdf) y "Emitir factura" (solo si existe facturas.insertar).
        Detalle imprimible: ruta nombrada detalle_facturas que envie $factura (con paciente, items y pagos).
[ ] 11. Vistas pendientes (cita/doctor/paciente/expediente): muestran una pantalla base hasta que se conecten sus rutas.
[ ] 12. El dashboard no se toco. Los toasts tampoco.

CONTROLADOR - USUARIOS CON ROL MEDICO (ejemplo; ajustar nombres de tablas/modelos)
La interfaz envia los campos del doctor como doctor[...] en la MISMA peticion que el usuario.
En el controlador de usuarios (store y update):

    $esMedico = (int) $request->input('id_rol') === Rol::where('rol', 'Médico')->value('id');

    $request->validate([
        // ...reglas actuales del usuario...
        'doctor'                          => ['exclude_unless:' . ($esMedico ? 'true' : 'false') . ',true'],
        'doctor.especialidad_id'          => [Rule::requiredIf($esMedico), 'nullable', 'exists:especialidades,id'],
        'doctor.numero_licencia'          => [Rule::requiredIf($esMedico), 'nullable', 'string', 'max:50'],
        'doctor.dui'                      => [Rule::requiredIf($esMedico), 'nullable', 'string', 'max:10'],
        'doctor.fecha_nacimiento'         => [Rule::requiredIf($esMedico), 'nullable', 'date', 'before:today'],
        'doctor.sexo'                     => [Rule::requiredIf($esMedico), 'nullable', 'in:M,F'],
        'doctor.descripcion_profesional'  => ['nullable', 'string', 'max:500'],
    ]);

    DB::transaction(function () use ($request, $esMedico) {
        $usuario = Usuario::create(/* datos de la cuenta */);

        if ($esMedico) {
            Doctor::updateOrCreate(['usuario_id' => $usuario->id], array_filter($request->input('doctor', [])));
        }
    });

Para que al editar se rellene el perfil, usuarios.buscar puede devolver el usuario con la relacion: $usuario->load('doctor')
(la interfaz usa la clave "doctor" de la respuesta JSON).

NOTAS
 - Si algo no se ve bien, quedan en .respaldos-ui/ las copias de los CSS/JS/vistas anteriores.
 - Las clases ahora son hw-* (antes alergias-*, usuarios-*, ...). Los CSS por modulo se eliminaron; todo vive en
   global-styles.css, app-layout.css y hw-components.css.
__HW_EOF__
ok "TASKLIST_UI.txt creado"

echo
echo "============================================================"
echo " RESUMEN"
echo "============================================================"
echo "Proyecto:         $RAIZ"
echo "Archivos escritos: $ARCHIVOS"
echo "Respaldo en:       ${RESPALDO#"$RAIZ"/}"
echo
echo "Siguiente:"
echo "  1) php artisan view:clear  y Ctrl+F5 en el navegador."
echo "  2) Seguir TASKLIST_UI.txt (incluye el ejemplo de controlador para el rol Medico)."
echo "============================================================"
