<?php
// cotizacion.php
// Vista + JS del cotizador. Ruta asumida: /ruta/a/tu/vista/cotizacion.php

session_start();

// --- 1) Verificar autenticación (ajusta la variable de sesión si usas otra)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.html");
    exit();
}

// --- 2) Verificar permisos (ajusta según tu include)
require_once '../../includes/verificar_permisos.php';
requierePermiso('cotizar_paquete');

// --- 3) Conexión DB (ajusta ruta)
require_once '../../config/database.php';
if (!isset($conn) || $conn->connect_error) {
    // Mostrar HTML simple: la vista no funcionará sin BD
    echo '<div class="alert alert-danger">Error: No se pudo conectar a la base de datos.</div>';
    exit();
}
?>
<!-- NUEVA ESTRUCTURA DE GRID PARA COTIZAR PAQUETES -->
<style>
.cotizador-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  grid-template-rows: auto auto 1fr;
  gap: 1.2rem;
  max-width: 1200px;
  margin: 2rem auto;
}
.cotizador-filtro {
  grid-column: 1 / 2;
  grid-row: 1 / 3;
  background: #fff;
  border-radius: 7px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 1.2rem 1rem 1.5rem 1rem;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  height: 540px;
  max-height: 540px;
}
#lista_paquetes {
  flex: 1 1 auto;
  overflow-y: auto;
  min-height: 0;
  max-height: 420px;
  border: 1px solid #e3e3e3;
  border-radius: 5px;
  background: #f9f9f9;
  padding: 0.5rem 0.2rem;
  width: 100%;
  box-sizing: border-box;
  /* Ocupa todo el contenedor */
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
.paquete-separador {
  border-top: 1.5px solid #d1d5db;
  margin: 0.7rem 0 0.7rem 0;
  width: 100%;
}
.categoria-separador {
  border-top: 2.5px solid #156080;
  margin: 1.2rem 0 0.7rem 0;
  width: 100%;
  opacity: 0.7;
}
.categoria-label {
  font-weight: bold;
  color: #156080;
  margin-bottom: 0.5rem;
  margin-top: 0.5rem;
  font-size: 1.08em;
  letter-spacing: 0.5px;
}
.cotizador-paquete {
  grid-column: 2 / 3;
  grid-row: 1 / 2;
  background: #fff;
  border-radius: 7px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 1.2rem 1rem;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}
.cotizador-cliente {
  grid-column: 2 / 3;
  grid-row: 2 / 3;
  background: #fff;
  border-radius: 7px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 1.2rem 1rem;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}
.cotizador-comparacion {
  grid-column: 1 / 3;
  grid-row: 3 / 4;
  background: #fff;
  border-radius: 7px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 1.2rem 1rem 1.5rem 1rem;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  /* Para que la tabla ocupe todo el ancho */
  overflow-x: auto;
}
.cotizador-comparacion table.table {
  width: 100% !important;
  min-width: 900px;
  table-layout: fixed;
  border-collapse: collapse;
  background: #fff;
}
.cotizador-comparacion th,
.cotizador-comparacion td {
  text-align: left;
  vertical-align: middle;
  word-break: break-word;
  border: 1.5px solid #e3e3e3 !important;
  background: #fff;
  padding: 0.45rem 0.5rem;
}
.cotizador-comparacion th:first-child,
.cotizador-comparacion td:first-child {
  width: 120px !important;
  min-width: 90px;
  max-width: 140px;
}
.cotizador-comparacion th:not(:first-child),
.cotizador-comparacion td:not(:first-child) {
  width: auto;
}
.cotizador-comparacion .btn_seleccionar_paquete_tabla {
  width: 100%;
  min-width: 90px;
  max-width: 180px;
  font-size: 1em;
  padding: 0.45rem 0.5rem;
  margin-top: 0.5rem;
  margin-bottom: 0.2rem;
  display: block;
  text-align: center;
}
.cotizador-comparacion td {
  vertical-align: top;
}
.cotizador-comparacion tr {
  border-bottom: 1.5px solid #e3e3e3 !important;
}
.cotizador-comparacion tr:last-child {
  border-bottom: 2.5px solid #156080 !important;
}

/* RESPONSIVE: Tablets */
@media (max-width: 900px) {
  .cotizador-grid {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto auto auto;
    gap: 1rem;
    max-width: 98vw;
  }
  .cotizador-filtro, .cotizador-paquete, .cotizador-cliente, .cotizador-comparacion {
    grid-column: 1 / 2 !important;
    grid-row: auto !important;
    min-width: 0;
    padding: 1rem 0.5rem;
  }
  .cotizador-filtro {
    height: 400px;
    max-height: 400px;
  }
  #lista_paquetes {
    max-height: 260px;
  }
  .cotizador-comparacion table.table {
    min-width: 600px;
    font-size: 0.97em;
  }
  .cotizador-comparacion th:first-child,
  .cotizador-comparacion td:first-child {
    min-width: 70px;
    max-width: 90px;
    font-size: 0.97em;
  }
  .cotizador-comparacion .btn_seleccionar_paquete_tabla {
    min-width: 70px;
    font-size: 0.97em;
  }
}

/* RESPONSIVE: Celulares */
@media (max-width: 600px) {
  .cotizador-grid {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto auto auto;
    gap: 0.7rem;
    max-width: 100vw;
    margin: 0.5rem auto;
  }
  .cotizador-filtro {
    height: 220px;
    max-height: 220px;
    padding: 0.5rem 0.1rem;
    font-size: 0.98em;
  }
  #lista_paquetes {
    max-height: 110px;
    padding: 0.2rem 0.1rem;
    font-size: 0.97em;
  }
  .cotizador-paquete, .cotizador-cliente, .cotizador-comparacion {
    padding: 0.7rem 0.2rem;
    min-width: 0;
    font-size: 0.97em;
  }
  .cotizador-comparacion table.table {
    min-width: 350px;
    font-size: 0.93em;
  }
  .cotizador-comparacion th, .cotizador-comparacion td {
    padding: 0.25rem 0.2rem;
    font-size: 0.93em;
  }
  .cotizador-comparacion th:first-child,
  .cotizador-comparacion td:first-child {
    min-width: 55px;
    max-width: 70px;
    font-size: 0.93em;
  }
  .cotizador-comparacion .btn_seleccionar_paquete_tabla {
    min-width: 60px;
    font-size: 0.93em;
    padding: 0.3rem 0.2rem;
  }
  .container-fluid {
    padding-left: 0.1rem !important;
    padding-right: 0.1rem !important;
  }
  .form-group label, .categoria-label {
    font-size: 0.97em;
  }
  .form-control, select, input[type="text"], input[type="number"], input[type="email"] {
    font-size: 0.97em;
    min-width: 0;
    width: 100%;
    box-sizing: border-box;
  }
  .d-flex {
    flex-direction: column !important;
    gap: 0.3rem;
  }
  .mb-2, .mb-3 {
    margin-bottom: 0.5rem !important;
  }
  .modal-dialog {
    max-width: 98vw !important;
    margin: 0.5rem auto;
  }
  .modal-content {
    font-size: 0.97em;
  }
  .preview-cotizacion h3 {
    font-size: 1.1em;
  }
  .preview-cotizacion .table th, .preview-cotizacion .table td {
    font-size: 0.97em;
    padding: 0.3rem 0.2rem;
  }
}

/* Extra: Mejorar scroll horizontal en tablas para móvil */
.cotizador-comparacion {
  overflow-x: auto;
}
.cotizador-comparacion table {
  display: block;
  width: 100%;
  overflow-x: auto;
}
.cotizador-comparacion thead, .cotizador-comparacion tbody, .cotizador-comparacion tfoot {
  display: table;
  width: 100%;
  table-layout: fixed;
}
</style>

<div class="container-fluid" style="padding:0;">
  <h1 class="h3 mb-4 text-gray-800" style="margin-left:1rem;"><i class="fas fa-file-invoice-dollar mr-2"></i>Cotizar Paquetes</h1>
  <div class="cotizador-grid">
    <!-- FILTRAR PAQUETES POR TIPO EVENTO -->
    <div class="cotizador-filtro">
      <div class="mb-3">
        <label for="filter_tipo_evento" class="font-weight-bold">Filtrar paquetes por tipo de evento</label>
        <select id="filter_tipo_evento" class="form-control mb-2">
          <option value="">Todos los tipos</option>
        </select>
      </div>
      <div id="lista_paquetes">Cargando paquetes...</div>
    </div>
    <!-- PAQUETE SELECCIONADO -->
    <div class="cotizador-paquete">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="font-weight-bold"><i class="fas fa-box-open mr-1"></i>Paquete seleccionado</span>
        <button id="btn_cancelar" type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times mr-1"></i>Cancelar</button>
      </div>
      <div class="form-group mb-2">
        <label>Paquete seleccionado</label>
        <input type="text" id="paquete_seleccionado_nombre" class="form-control" readonly placeholder="Seleccione un paquete de la comparación">
      </div>
      <div class="form-group mb-2">
        <label>Proveedor</label>
        <input type="text" id="paquete_seleccionado_proveedor" class="form-control" readonly>
      </div>
      <div class="d-flex justify-content-between">
        <button id="btn_preview" type="button" class="btn btn-secondary"><i class="fas fa-eye mr-1"></i>Previsualizar</button>
        <button id="btn_cotizar" type="button" class="btn btn-primary"><i class="fas fa-check mr-1"></i>Cotizar</button>
      </div>
    </div>
    <!-- CLIENTE -->
    <div class="cotizador-cliente">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="font-weight-bold"><i class="fas fa-user mr-1"></i>Cliente</span>
        <button id="btn_agregar_cliente" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-user-plus mr-1"></i>Agregar Cliente</button>
      </div>
      <div class="form-group mb-2">
        <label>Cliente seleccionado</label>
        <input type="text" id="cliente_seleccionado_nombre" class="form-control" readonly placeholder="No se ha seleccionado cliente">
      </div>
      <div class="form-group mb-2">
        <label>Identificación</label>
        <input type="text" id="cliente_seleccionado_id" class="form-control" readonly>
      </div>
    </div>
    <!-- COMPARACIÓN DE PAQUETES SELECCIONADOS -->
    <div class="cotizador-comparacion">
      <div class="mb-2 font-weight-bold"><i class="fas fa-balance-scale mr-1"></i>Comparación de paquetes seleccionados (máximo 3)</div>
      <div class="mb-2 text-left">
        
      </div>
      <div class="text-muted text-left mb-2">Seleccione hasta 3 paquetes para comparar.</div>
      <div id="comparacion_paquetes"></div>
    </div>
  </div>
</div>

<!-- Modal simple para preview (Bootstrap) -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-xl" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title">Previsualización de cotización</h5>
       <button type="button" class="close" data-dismiss="modal">&times;</button>
     </div>
     <div class="modal-body" id="preview_body"></div>
     <div class="modal-footer">
  <button id="btn_descargar_pdf_preview" type="button" class="btn btn-info"><i class="fas fa-file-pdf mr-1"></i>Descargar PDF</button>
       <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
     </div>
   </div>
 </div>
</div>

<!-- Modal para datos del cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title">Buscar / Agregar Cliente</h5>
       <button type="button" class="close" data-dismiss="modal">&times;</button>
     </div>
     <div class="modal-body">
       <div class="alert alert-info">
         <i class="fas fa-info-circle"></i> Complete la identificación para buscar un cliente existente, o llene todos los campos para crear uno nuevo.
       </div>
       <form id="formCliente" autocomplete="off">
         <div class="form-group">
           <label>Identificación <span class="text-danger">*</span></label>
           <input type="text" id="cliente_identificacion" name="identificacion" class="form-control" placeholder="Ingrese cédula o RUC" />
         </div>

         <div class="form-group">
           <label>Nombres / Empresa <span class="text-danger">*</span></label>
           <input type="text" id="cliente_nombres" name="nombres" class="form-control" />
         </div>

         <div class="form-group">
           <label>Correo <span class="text-danger">*</span></label>
           <input type="email" id="cliente_correo" name="correo" class="form-control" />
         </div>

         <div class="form-group">
           <label>Celular <span class="text-danger">*</span></label>
           <input type="text" id="cliente_celular" name="celular" class="form-control" />
         </div>

         <div class="form-group">
           <label>Ciudad</label>
           <select id="cliente_ciudad" name="ciudad" class="form-control">
             <option value="">-- Seleccione ciudad --</option>
             <option value="Quito">Quito</option>
             <option value="Guayaquil">Guayaquil</option>
             <option value="Cuenca">Cuenca</option>
             <option value="Ambato">Ambato</option>
             <option value="Manta">Manta</option>
             <option value="Portoviejo">Portoviejo</option>
             <option value="Machala">Machala</option>
             <option value="Loja">Loja</option>
             <option value="Riobamba">Riobamba</option>
             <option value="Esmeraldas">Esmeraldas</option>
             <option value="Ibarra">Ibarra</option>
             <option value="Quevedo">Quevedo</option>
             <option value="Santo Domingo">Santo Domingo</option>
             <option value="Latacunga">Latacunga</option>
             <option value="Tulcán">Tulcán</option>
             <option value="Babahoyo">Babahoyo</option>
             <option value="Otavalo">Otavalo</option>
             <option value="Salinas">Salinas</option>
             <option value="Santa Elena">Santa Elena</option>
             <option value="Nueva Loja">Nueva Loja</option>
           </select>
         </div>

         <div class="form-group">
           <label>Dirección</label>
           <input type="text" id="cliente_direccion" name="direccion" class="form-control" />
         </div>
       </form>
     </div>
     <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
  <button id="btn_seleccionar_cliente" type="button" class="btn btn-success"><i class="fas fa-check mr-1"></i>Seleccionar Cliente</button>
     </div>
   </div>
 </div>
</div>

<!-- Modal para confirmar cotización -->
<div class="modal fade" id="modalConfirmarCotizacion" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title">Confirmar Cotización</h5>
       <button type="button" class="close" data-dismiss="modal">&times;</button>
     </div>
     <div class="modal-body">
       <div class="row">
         <div class="col-md-6">
           <h6>Datos del Cliente:</h6>
           <div id="resumen_cliente_cotizacion"></div>
         </div>
         <div class="col-md-6">
           <h6>Datos del Paquete:</h6>
           <div id="resumen_paquete_cotizacion"></div>
         </div>
       </div>
       <div class="mt-3">
         <h6>Resumen de Productos:</h6>
         <div id="resumen_productos_cotizacion"></div>
       </div>
     </div>
     <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancelar</button>
  <button id="btn_confirmar_cotizacion" type="button" class="btn btn-primary"><i class="fas fa-file-pdf mr-1"></i>Confirmar y Generar PDF</button>
     </div>
   </div>
 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/*
  JS del cotizador.
  Notas importantes:
  - Usamos dataType: 'json' en las llamadas AJAX para evitar JSON.parse manual.
  - Todas las respuestas del servidor deben ser JSON válidos.
  - Rutas relativas: '../controles/ajax_cotizacion.php' (ajusta si tu estructura difiere).
*/

(function($){
  const AJAX_URL = '../controles/ajax_cotizacion.php';
  let paquetesSeleccionados = [];
  let paqueteSeleccionadoId = null;
  let paqueteSeleccionadoNombre = '';
  let paqueteSeleccionadoProveedor = '';
  let clienteSeleccionado = null;

  function showError(msg) {
    // Usar SweetAlert2 para mostrar errores
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: msg,
      confirmButtonText: 'Cerrar'
    });
    console.error(msg);
  }

  function showSuccess(msg) {
    // Usar SweetAlert2 para mostrar éxito
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: msg,
      confirmButtonText: 'Cerrar',
      timer: 2000,
      showConfirmButton: false
    });
  }

  // Cargar tipos de evento en el select
  function cargarTipos() {
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: { accion: 'tipos_evento' },
      dataType: 'json', // important: jQuery will parse or call error
      success: function(resp) {
        if (!resp || !resp.success) {
          showError(resp && resp.mensaje ? resp.mensaje : 'Error al cargar tipos');
          return;
        }
        const tipos = resp.tipos || [];
        const $sel = $('#filter_tipo_evento');
        $sel.find('option:not(:first)').remove();
        tipos.forEach(function(t){
          // proteger XSS
          const safe = $('<option/>').text(t).val(t);
          $sel.append(safe);
        });
        // cargar lista inicialmente
        cargarPaquetes('');
        mostrarComparacionPaquetes();
      },
      error: function(xhr, status, err) {
        showError('Error AJAX tipos_evento: ' + (xhr.responseText || status));
      }
    });
  }

  // Cargar paquetes (puede recibir filtro tipo_evento)
  function cargarPaquetes(tipo) {
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: { accion: 'listar_paquetes', tipo_evento: tipo || '' },
      dataType: 'json',
      success: function(resp) {
        if (!resp || !resp.success) {
          showError(resp && resp.mensaje ? resp.mensaje : 'Error al listar paquetes');
          return;
        }
        const paquetes = resp.paquetes || [];
        if (paquetes.length === 0) {
          $('#lista_paquetes').html('<div class="text-muted">No hay paquetes</div>');
          return;
        }
        // Agrupar por categoría si existe campo tipo_evento/categoría
        // Suponiendo que tipo_evento es la categoría principal
        let html = '';
        let lastCategoria = '';
        paquetes.forEach(function(p, idx){
          // Si cambia la categoría, poner separador y label
          if (p.tipo_evento !== lastCategoria) {
            if (idx !== 0) html += `<div class="categoria-separador"></div>`;
            html += `<div class="categoria-label">${$('<span>').text(p.tipo_evento).html()}</div>`;
            lastCategoria = p.tipo_evento;
          } else if (idx !== 0) {
            html += `<div class="paquete-separador"></div>`;
          }
          let descHtml = '';
          if (Array.isArray(p.descripcion_resumida)) {
            descHtml = '<ul class="mb-2">';
            p.descripcion_resumida.forEach(function(item){
              descHtml += `<li>${$('<span>').text(item).html()}</li>`;
            });
            descHtml += '</ul>';
          } else {
            descHtml = `<p>${$('<span>').text(p.descripcion_resumida || '').html()}</p>`;
          }
          const checked = paquetesSeleccionados.includes(p.id_paquete) ? 'checked' : '';
          html += `<div class="card mb-2" style="width:100%;box-sizing:border-box;">
                    <div class="card-body">
                      <h6>Paquete #${p.id_paquete} <small class="text-muted">${$('<span>').text(p.tipo_evento).html()}</small></h6>
                      ${descHtml}
                      <label>
                        <input type="checkbox" class="chk_comparar_paquete" data-id="${p.id_paquete}" ${checked}>
                        Seleccionar para comparar
                      </label>
                    </div>
                   </div>`;
        });
        $('#lista_paquetes').html(html);
      },
      error: function(xhr) {
        showError('Error AJAX listar_paquetes: ' + (xhr.responseText || xhr.statusText));
      }
    });
  }

  // Manejar selección de paquetes para comparar (máx 3)
  $(document).on('change', '.chk_comparar_paquete', function(){
    const id = parseInt($(this).data('id'),10);
    if ($(this).is(':checked')) {
      if (paquetesSeleccionados.length >= 3) {
        showError('Solo puede comparar hasta 3 paquetes.');
        $(this).prop('checked', false);
        return;
      }
      if (!paquetesSeleccionados.includes(id)) paquetesSeleccionados.push(id);
    } else {
      paquetesSeleccionados = paquetesSeleccionados.filter(pid => pid !== id);
    }
    mostrarComparacionPaquetes();
  });

  // Mostrar tabla comparativa de paquetes seleccionados
  function mostrarComparacionPaquetes() {
    if (paquetesSeleccionados.length === 0) {
      $('#comparacion_paquetes').html('<div class="mb-2 text-left"><label for="iva_comparacion" class="font-weight-bold">IVA (%):</label> <input type="number" id="iva_comparacion" value="15" min="0" max="100" class="form-control form-control-sm d-inline-block" style="width:80px;"></div><div class="text-muted text-left"></div>');
      return;
    }
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: { accion: 'comparar_paquetes', ids: JSON.stringify(paquetesSeleccionados) },
      dataType: 'json',
      success: function(resp) {
        if (!resp || !resp.success) {
          $('#comparacion_paquetes').html('<div class="text-danger text-left">Error al cargar comparación.</div>');
          return;
        }
        const categorias = resp.categorias;
        const paquetes = resp.paquetes;
        const proveedores = resp.proveedores || {};
        let html = '<div class="mb-2 text-left"><label for="iva_comparacion" class="font-weight-bold">IVA (%):</label> <input type="number" id="iva_comparacion" value="15" min="0" max="100" class="form-control form-control-sm d-inline-block" style="width:80px;"></div>';
        html += '<div style="width:100%;overflow-x:auto;"><table class="table table-bordered table-sm text-left" style="width:100%;min-width:900px;table-layout:fixed;">';
        // Cabecera
        html += '<thead><tr><th class="text-left" style="width:150px;min-width:120px;max-width:180px;">Categoría / Producto</th>';
        paquetesSeleccionados.forEach(pid => {
          const proveedor = proveedores[pid] ? proveedores[pid] : 'Sin proveedor';
          html += `<th class="text-left">${'Paquete #'+pid}<br><span class="text-info small">Proveedor: ${$('<span>').text(proveedor).html()}</span></th>`;
        });
        html += '</tr></thead><tbody>';
        // Filas por categoría
        categorias.forEach(cat => {
          let maxProductos = 0;
          paquetesSeleccionados.forEach(pid => {
            const productos = (paquetes[pid] && paquetes[pid][cat]) ? paquetes[pid][cat] : [];
            if (productos.length > maxProductos) maxProductos = productos.length;
          });
          for (let i = 0; i < maxProductos; i++) {
            html += `<tr>`;
            if (i === 0) {
              html += `<td rowspan="${maxProductos}" class="align-middle font-weight-bold text-primary text-left" style="background:#f3f8fc;border-right:2px solid #156080;width:150px;min-width:120px;max-width:180px;">${cat}</td>`;
            }
            paquetesSeleccionados.forEach(pid => {
              const productos = (paquetes[pid] && paquetes[pid][cat]) ? paquetes[pid][cat] : [];
              const prod = productos[i];
              if (!prod) {
                html += '<td class="text-muted align-middle text-left" style="background:#f9f9f9;">-</td>';
              } else {
                const subtotal = prod.cantidad_default * prod.precio_unitario;
                html += `<td class="align-middle text-left" style="background:#fff;">
                  <span>${$('<span>').text(prod.nombre).html()}</span>
                  <br><span class="text-secondary">Cant. Producto:</span>
                  <input type="number" min="0" max="${prod.cantidad_disponible}" value="${prod.cantidad_default}" class="form-control form-control-sm cantidad_comparada" data-pid="${pid}" data-prid="${prod.id_producto}" data-precio="${prod.precio_unitario}">
                  <br><span class="text-secondary">Stock disponible:</span> <span class="badge badge-info">${prod.cantidad_disponible}</span>
                  <br><span class="text-secondary">Precio unitario:</span> <span class="badge badge-success">$${parseFloat(prod.precio_unitario).toFixed(2)}</span>
                  <br><span class="text-secondary">Precio total:</span> <span class="badge badge-warning precio_total_comparacion" id="precio_total_${pid}_${prod.id_producto}">$${subtotal.toFixed(2)}</span>
                </td>`;
              }
            });
            html += `</tr>`;
          }
          // Línea separadora entre categorías (excepto la última)
          if (cat !== categorias[categorias.length-1]) {
            html += `<tr style="height:2px;background:#156080;"><td colspan="${paquetesSeleccionados.length+1}" style="padding:0;border:none;background:#156080;"></td></tr>`;
          }
        });
        // Totales por paquete (en la última fila de la tabla)
        html += '<tr style="background:#f3f8fc;"><td class="font-weight-bold text-left">Totales:</td>';
        paquetesSeleccionados.forEach(pid => {
          html += `<td class="text-left">
            <span class="font-weight-bold">Base:</span> <span class="badge badge-primary" id="total_paquete_${pid}">$0.00</span><br>
            <span class="font-weight-bold">IVA:</span> <span class="badge badge-info" id="iva_paquete_${pid}">$0.00</span><br>
            <span class="font-weight-bold">Total + IVA:</span> <span class="badge badge-success" id="total_iva_paquete_${pid}">$0.00</span>
            <br><button type="button" class="btn btn-sm btn-outline-primary mt-2 btn_seleccionar_paquete_tabla" data-id="${pid}" data-nombre="Paquete #${pid}" data-proveedor="${proveedores[pid] ? proveedores[pid] : 'Sin proveedor'}">Seleccionar</button>
          </td>`;
        });
        html += '</tr>';
        html += '</tbody></table></div>';
        $('#comparacion_paquetes').html(html);

        // Función para actualizar totales por paquete
        function actualizarTotalesComparacion() {
          const ivaPorc = parseFloat($('#iva_comparacion').val()) || 0;
          paquetesSeleccionados.forEach(pid => {
            let base = 0;
            $(`.cantidad_comparada[data-pid="${pid}"]`).each(function(){
              const cantidad = parseInt($(this).val() || '0', 10);
              const precio = parseFloat($(this).data('precio'));
              base += cantidad * precio;
            });
            const iva = base * (ivaPorc/100);
            const total = base + iva;
            $(`#total_paquete_${pid}`).text('$' + base.toFixed(2));
            $(`#iva_paquete_${pid}`).text('$' + iva.toFixed(2));
            $(`#total_iva_paquete_${pid}`).text('$' + total.toFixed(2));
          });
        }

        // Evento para actualizar el precio total al cambiar cantidad
        $('.cantidad_comparada').on('input', function() {
          const cantidad = parseInt($(this).val() || '0', 10);
          const precio = parseFloat($(this).data('precio'));
          const pid = $(this).data('pid');
          const prid = $(this).data('prid');
          const max = parseInt($(this).attr('max'), 10);
          let val = cantidad;
          if (isNaN(val) || val < 0) val = 0;
          if (val > max) val = max;
          $(this).val(val);
          const total = val * precio;
            $(`#precio_total_${pid}_${prid}`).text('$' + total.toFixed(2));
            actualizarTotalesComparacion();
          });

          // Evento para actualizar totales al cambiar el IVA
          $('#iva_comparacion').on('input', function(){
            actualizarTotalesComparacion();
          });        // Evento seleccionar paquete desde la tabla
        $('.btn_seleccionar_paquete_tabla').on('click', function(){
          paqueteSeleccionadoId = $(this).data('id');
          paqueteSeleccionadoNombre = $(this).data('nombre');
          paqueteSeleccionadoProveedor = $(this).data('proveedor');
          $('#paquete_seleccionado_nombre').val(paqueteSeleccionadoNombre);
          $('#paquete_seleccionado_proveedor').val(paqueteSeleccionadoProveedor);
          // Mensaje emergente al seleccionar paquete
          Swal.fire({
            icon: 'success',
            title: 'Paquete seleccionado',
            text: 'El paquete ha sido seleccionado para la cotización.',
            timer: 1500,
            showConfirmButton: false
          });
        });

        // Inicializar totales
        actualizarTotalesComparacion();
      },
      error: function(xhr) {
        $('#comparacion_paquetes').html('<div class="text-danger text-left">Error AJAX comparación.</div>');
      }
    });
  }

  // Cargar productos de un paquete
  function cargarProductosPaquete(id_paquete) {
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: { accion: 'productos_paquete', id_paquete: id_paquete },
      dataType: 'json',
      success: function(resp) {
        if (!resp || !resp.success) {
          showError(resp && resp.mensaje ? resp.mensaje : 'Error al cargar productos');
          return;
        }
        const productos = resp.productos || [];
        if (productos.length === 0) {
          $('#productos_paquete').html('<div class="text-muted">Paquete sin productos.</div>');
          return;
        }
        let html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Stock</th><th>Precio</th><th>Cantidad</th></tr></thead><tbody>';
        productos.forEach(function(prod){
          // max es stock; cantidad_default es la cantidad que trae el paquete
          const safeName = $('<span>').text(prod.nombre).html();
          html += `<tr>
                    <td>${safeName}</td>
                    <td>${parseInt(prod.cantidad_disponible,10)}</td>
                    <td>${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                    <td>
                      <input type="number" min="0" max="${parseInt(prod.cantidad_disponible,10)}" value="${parseInt(prod.cantidad_default,10)}" class="form-control form-control-sm cantidad_producto" data-id="${prod.id_producto}">
                    </td>
                   </tr>`;
        });
        html += '</tbody></table>';
        html += `<input type="hidden" id="selected_paquete_id" value="${id_paquete}">`;
        $('#productos_paquete').html(html);
        actualizarResumen();
      },
      error: function(xhr) {
        showError('Error AJAX productos_paquete: ' + (xhr.responseText || xhr.statusText));
      }
    });
  }

  // Obtener items seleccionados desde inputs
  function obtenerItemsSeleccionados() {
    const items = [];
    $('.cantidad_producto').each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        items.push({
          id_producto: parseInt($(this).data('id'),10),
          cantidad: cantidad
        });
      }
    });
    return items;
  }

  // Actualizar resumen en UI (usa precios que están en la tabla visible)
  function actualizarResumen() {
    const rows = $('.cantidad_producto');
    if (rows.length === 0) { $('#resumen_cotizacion').html('No hay productos seleccionados'); return; }
    let base = 0;
    let html = '<ul class="list-group">';
    rows.each(function(){
      const cantidad = parseInt($(this).val() || '0',10);
      if (cantidad <= 0) return;
      const id = $(this).data('id');
      const precio = parseFloat($(this).closest('tr').find('td').eq(2).text() || 0);
      const subtotal = cantidad * precio;
      base += subtotal;
      html += `<li class="list-group-item d-flex justify-content-between"><div>Producto ${id} x ${cantidad}</div><div>${subtotal.toFixed(2)}</div></li>`;
    });
    html += '</ul>';
    let iva = "document.getElementById('iva_comparacion').value";
    console.log(iva);
    let tot_iva= base*iva/100;
    const total = base + iva;
    
    html += `<p class="mt-2"><strong>Base:</strong> ${base.toFixed(2)} <br><strong>IVA (12%):</strong> ${tot_iva.toFixed(2)} <br><strong>Total:</strong> ${total.toFixed(2)}</p>`;
    $('#resumen_cotizacion').html(html);
  }

  // Autocompletar cliente por identificacion (cuando pierde foco)
  $('#cliente_identificacion').on('blur', function(){
    const ci = $(this).val().trim();
    if (!ci) return;
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: { accion: 'buscar_cliente', identificacion: ci },
      dataType: 'json',
      success: function(resp) {
        if (!resp) return;
        if (!resp.success) { /* no existe o error */ return; }
        if (resp.cliente) {
          $('#cliente_nombres').val(resp.cliente.nombres);
          $('#cliente_correo').val(resp.cliente.correo);
          $('#cliente_celular').val(resp.cliente.celular);
          $('#cliente_direccion').val(resp.cliente.direccion);
          $('#cliente_ciudad').val(resp.cliente.ciudad); // autocompletar ciudad
        }
      },
      error: function(xhr) {
        console.warn('Error buscar_cliente', xhr.responseText);
      }
    });
  });

  // Event: seleccionar paquete
  $(document).on('click', '.btn_seleccionar_paquete', function(){
    const id = $(this).data('id');
    if (!id) return;
    cargarProductosPaquete(id);
  });

  // Event: cambiar cantidad -> actualizar resumen
  $(document).on('input', '.cantidad_producto', function(){
    const max = parseInt($(this).attr('max') || '0', 10);
    let v = parseInt($(this).val() || '0', 10);
    if (isNaN(v) || v < 0) v = 0;
    if (v > max) v = max;
    $(this).val(v);
    actualizarResumen();
  });

  // Previsualizar (server genera HTML seguro)
  $('#btn_preview').on('click', function(){
    // Usar el paquete seleccionado desde la tabla de comparación
    const paquete_id = paqueteSeleccionadoId;
    const paquete_nombre = $('#paquete_seleccionado_nombre').val();
    const paquete_proveedor = $('#paquete_seleccionado_proveedor').val();
    
    if (!paquete_id) return showError('Seleccione un paquete desde la tabla de comparación.');
    
    // Obtener los productos y cantidades del paquete seleccionado
    const items = [];
    const ivaPorc = parseFloat($('#iva_comparacion').val()) || 0;
    let base = 0;
    
    $(`.cantidad_comparada[data-pid="${paquete_id}"]`).each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        const precio = parseFloat($(this).data('precio'));
        const subtotal = cantidad * precio;
        base += subtotal;
        items.push({
          id_producto: parseInt($(this).data('prid'),10),
          cantidad: cantidad,
          precio_unitario: precio,
          subtotal: subtotal
        });
      }
    });
    
    if (items.length === 0) return showError('Seleccione al menos un producto con cantidad > 0.');
    
    const iva = base * (ivaPorc/100);
    const total = base + iva;
    
    // Determinar datos del cliente para mostrar
    let datosCliente = '';
    if (clienteSeleccionado) {
      datosCliente = `
        <div class="info-section mb-4">
          <h5>Datos del Cliente</h5>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Nombre/Empresa:</strong> ${clienteSeleccionado.nombres}</p>
              <p><strong>Identificación:</strong> ${clienteSeleccionado.identificacion}</p>
              <p><strong>Correo:</strong> ${clienteSeleccionado.correo}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Celular:</strong> ${clienteSeleccionado.celular}</p>
              <p><strong>Ciudad:</strong> ${clienteSeleccionado.ciudad || 'No especificada'}</p>
              <p><strong>Dirección:</strong> ${clienteSeleccionado.direccion || 'No especificada'}</p>
            </div>
          </div>
        </div>`;
    } else {
      datosCliente = `
        <div class="alert alert-warning mb-4">
          <i class="fas fa-exclamation-triangle"></i> <strong>Cliente no seleccionado.</strong> 
          Para generar una cotización oficial, debe agregar un cliente.
        </div>`;
    }
    
    // Generar HTML de previsualización localmente
    let previewHtml = `
      <div class="preview-cotizacion">
        <div class="text-center mb-4">
          <h3>Previsualización de Cotización</h3>
          <p class="text-muted">${paquete_nombre} - ${paquete_proveedor}</p>
        </div>
        
        ${datosCliente}
        
        <div class="row mb-4">
          <div class="col-md-7">
            <h5>Detalles del Paquete</h5>
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio Unit.</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>`;
    
    items.forEach(item => {
      // Buscar el nombre del producto en la tabla
      const productoElement = $(`.cantidad_comparada[data-prid="${item.id_producto}"]`).closest('td').find('span').first();
      const nombreProducto = productoElement.text() || `Producto ${item.id_producto}`;
      
      previewHtml += `
        <tr>
          <td>${nombreProducto}</td>
          <td>${item.cantidad}</td>
          <td>$${item.precio_unitario.toFixed(2)}</td>
          <td>$${item.subtotal.toFixed(2)}</td>
        </tr>`;
    });
    
    previewHtml += `
              </tbody>
            </table>
          </div>
          <div class="col-md-5">
            <h5>Resumen de Costos</h5>
            <table class="table table-sm">
              <tr>
                <td><strong>Subtotal:</strong></td>
                <td>$${base.toFixed(2)}</td>
              </tr>
              <tr>
                <td><strong>IVA (${ivaPorc}%):</strong></td>
                <td>$${iva.toFixed(2)}</td>
              </tr>
              <tr class="table-primary">
                <td><strong>Total:</strong></td>
                <td><strong>$${total.toFixed(2)}</strong></td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> Esta es una previsualización. ${clienteSeleccionado ? 'Para generar la cotización oficial y el PDF, use el botón "Cotizar".' : 'Para continuar, debe agregar un cliente y luego usar "Cotizar".'}
        </div>
      </div>`;
    
    $('#preview_body').html(previewHtml);
    $('#modalPreview').modal('show');
  });

  // Botón Agregar Cliente - abrir modal de cliente
  $('#btn_agregar_cliente').on('click', function(){
    // Limpiar formulario
    $('#formCliente')[0].reset();
    $('#modalCliente').modal('show');
  });

  // Botón Seleccionar Cliente - validar y seleccionar cliente
  $('#btn_seleccionar_cliente').on('click', function(){
    const cliente = {
      identificacion: $('#cliente_identificacion').val().trim(),
      nombres: $('#cliente_nombres').val().trim(),
      correo: $('#cliente_correo').val().trim(),
      celular: $('#cliente_celular').val().trim(),
      ciudad: $('#cliente_ciudad').val().trim(),
      direccion: $('#cliente_direccion').val().trim()
    };
    
    // Validar campos obligatorios
    if (!cliente.identificacion || !cliente.nombres || !cliente.correo || !cliente.celular) {
      return showError('Por favor complete todos los campos obligatorios (Identificación, Nombres, Correo, Celular).');
    }
    
    // Guardar cliente seleccionado
    clienteSeleccionado = cliente;
    
    // Mostrar en la interfaz
    $('#cliente_seleccionado_nombre').val(cliente.nombres);
    $('#cliente_seleccionado_id').val(cliente.identificacion);
    
    // Cerrar modal
    $('#modalCliente').modal('hide');
    
    showSuccess('Cliente seleccionado correctamente');
  });

  // Botón Cotizar - validar cliente y paquete, luego mostrar confirmación
  $('#btn_cotizar').on('click', function(){
    const paquete_id = paqueteSeleccionadoId;
    
    if (!paquete_id) return showError('Seleccione un paquete desde la tabla de comparación.');
    if (!clienteSeleccionado) return showError('Debe agregar un cliente antes de cotizar.');
    
    // Verificar que hay productos seleccionados
    const items = [];
    $(`.cantidad_comparada[data-pid="${paquete_id}"]`).each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        items.push({
          id_producto: parseInt($(this).data('prid'),10),
          cantidad: cantidad
        });
      }
    });
    
    if (items.length === 0) return showError('Seleccione al menos un producto con cantidad > 0.');
    
    // Mostrar modal de confirmación con resumen
    mostrarResumenCotizacion();
    $('#modalConfirmarCotizacion').modal('show');
  });

  // Botón Cancelar - limpiar todo
  $('#btn_cancelar').on('click', function(){
    if (confirm('¿Está seguro de que desea cancelar y limpiar todos los datos?')) {
      // Limpiar selección de paquetes
      paquetesSeleccionados = [];
      paqueteSeleccionadoId = null;
      paqueteSeleccionadoNombre = '';
      paqueteSeleccionadoProveedor = '';
      clienteSeleccionado = null;
      
      // Limpiar campos
      $('#paquete_seleccionado_nombre').val('');
      $('#paquete_seleccionado_proveedor').val('');
      $('#cliente_seleccionado_nombre').val('');
      $('#cliente_seleccionado_id').val('');
      $('#formCliente')[0].reset();
      
      // Desmarcar checkboxes
      $('.chk_comparar_paquete').prop('checked', false);
      
      // Resetear comparación
      mostrarComparacionPaquetes();
      
      showSuccess('Datos limpiados correctamente');
    }
  });

  // Confirmar y guardar cotización
  $('#btn_confirmar_cotizacion').on('click', function(){
    guardarCotizacion(true); // true para generar PDF
  });

  // Descargar PDF desde previsualización (sin guardar cotización)
  $('#btn_descargar_pdf_preview').on('click', function(){
    const paquete_id = paqueteSeleccionadoId;
    const paquete_nombre = $('#paquete_seleccionado_nombre').val();
    const paquete_proveedor = $('#paquete_seleccionado_proveedor').val();
    
    if (!paquete_id) return showError('Seleccione un paquete desde la tabla de comparación.');
    
    // Obtener los productos y cantidades del paquete seleccionado
    const items = [];
    const ivaPorc = parseFloat($('#iva_comparacion').val()) || 0;
    
    $(`.cantidad_comparada[data-pid="${paquete_id}"]`).each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        items.push({
          id_producto: parseInt($(this).data('prid'),10),
          cantidad: cantidad
        });
      }
    });
    
    if (items.length === 0) return showError('Seleccione al menos un producto con cantidad > 0.');

    // Crear datos para el PDF (usar cliente seleccionado si existe)
    let clienteParaPDF;
    if (clienteSeleccionado) {
      clienteParaPDF = clienteSeleccionado;
    } else {
      clienteParaPDF = {
        nombres: '[Cliente - Previsualización]',
        identificacion: 'PREVIEW',
        correo: 'preview@ejemplo.com',
        celular: 'N/A',
        ciudad: 'N/A',
        direccion: 'N/A'
      };
    }

    const datosTemporales = {
      paquete_id: paquete_id,
      paquete_nombre: paquete_nombre,
      paquete_proveedor: paquete_proveedor,
      items: items,
      iva_porcentaje: ivaPorc,
      cliente_temporal: clienteParaPDF,
      es_preview: !clienteSeleccionado // indicar si es preview o cliente real
    };

    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {
        accion: 'generar_pdf_preview',
        datos: JSON.stringify(datosTemporales)
      },
      dataType: 'json',
      success: function(resp) {
        if (!resp) return showError('Respuesta inválida del servidor.');
        if (resp.success && resp.pdf_url) {
          // Abrir PDF en nueva ventana para descarga
          window.open(resp.pdf_url, '_blank');
          showSuccess('PDF generado correctamente para descarga.');
        } else {
          showError(resp.mensaje || 'Error al generar PDF de previsualización');
        }
      },
      error: function(xhr) {
        showError('Error AJAX generar PDF: ' + (xhr.responseText || xhr.statusText));
      }
    });
  });

  function guardarCotizacion(generar_pdf) {
    // Usar el paquete seleccionado desde la tabla de comparación
    const paquete_id = paqueteSeleccionadoId;
    const cliente = clienteSeleccionado;
    
    // Obtener los productos y cantidades del paquete seleccionado con IVA
    const items = [];
    const ivaPorc = parseFloat($('#iva_comparacion').val()) || 0;
    
    $(`.cantidad_comparada[data-pid="${paquete_id}"]`).each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        items.push({
          id_producto: parseInt($(this).data('prid'),10),
          cantidad: cantidad
        });
      }
    });
    
    if (!paquete_id) return showError('Seleccione un paquete desde la tabla de comparación.');
    if (!cliente) return showError('Seleccione un cliente.');
    if (items.length === 0) return showError('No hay productos seleccionados.');

    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {
        accion: 'guardar',
        paquete_id: paquete_id,
        cliente: JSON.stringify(cliente),
        items: JSON.stringify(items),
        iva_porcentaje: ivaPorc,
        generar_pdf: generar_pdf ? 1 : 0
      },
      dataType: 'json',
      success: function(resp) {
        if (!resp) return showError('Respuesta inválida del servidor.');
        if (resp.success) {
          showSuccess('Cotización guardada correctamente');
          if (resp.pdf_url) window.open(resp.pdf_url, '_blank');
          
          // Cerrar modales
          $('#modalConfirmarCotizacion').modal('hide');
          
          // Limpiar datos
          paquetesSeleccionados = [];
          paqueteSeleccionadoId = null;
          paqueteSeleccionadoNombre = '';
          paqueteSeleccionadoProveedor = '';
          clienteSeleccionado = null;
          $('#paquete_seleccionado_nombre').val('');
          $('#paquete_seleccionado_proveedor').val('');
          $('#cliente_seleccionado_nombre').val('');
          $('#cliente_seleccionado_id').val('');
          $('.chk_comparar_paquete').prop('checked', false);
          mostrarComparacionPaquetes();
        } else {
          showError(resp.mensaje || 'Error al guardar cotización');
        }
      },
      error: function(xhr) {
        showError('Error AJAX guardar: ' + (xhr.responseText || xhr.statusText));
      }
    });
  }

  // Mostrar resumen en modal de confirmación
  function mostrarResumenCotizacion() {
    const paquete_id = paqueteSeleccionadoId;
    const ivaPorc = parseFloat($('#iva_comparacion').val()) || 0;
    
    // Resumen del cliente
    const resumenCliente = `
      <div class="border p-2 rounded">
        <strong>${clienteSeleccionado.nombres}</strong><br>
        <small>ID: ${clienteSeleccionado.identificacion}</small><br>
        <small>Correo: ${clienteSeleccionado.correo}</small><br>
        <small>Celular: ${clienteSeleccionado.celular}</small>
      </div>`;
    $('#resumen_cliente_cotizacion').html(resumenCliente);
    
    // Resumen del paquete
    const resumenPaquete = `
      <div class="border p-2 rounded">
        <strong>${paqueteSeleccionadoNombre}</strong><br>
        <small>Proveedor: ${paqueteSeleccionadoProveedor}</small><br>
        <small>IVA: ${ivaPorc}%</small>
      </div>`;
    $('#resumen_paquete_cotizacion').html(resumenPaquete);
    
    // Resumen de productos
    let base = 0;
    let htmlProductos = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
    
    $(`.cantidad_comparada[data-pid="${paquete_id}"]`).each(function(){
      const cantidad = parseInt($(this).val() || '0', 10);
      if (cantidad > 0) {
        const precio = parseFloat($(this).data('precio'));
        const subtotal = cantidad * precio;
        base += subtotal;
        
        const productoElement = $(this).closest('td').find('span').first();
        const nombreProducto = productoElement.text() || `Producto ${$(this).data('prid')}`;
        
        htmlProductos += `
          <tr>
            <td>${nombreProducto}</td>
            <td>${cantidad}</td>
            <td>$${precio.toFixed(2)}</td>
            <td>$${subtotal.toFixed(2)}</td>
          </tr>`;
      }
    });
    
    const iva = base * (ivaPorc/100);
    const total = base + iva;
    
    htmlProductos += `
      </tbody>
      <tfoot>
        <tr><td colspan="3"><strong>Subtotal:</strong></td><td><strong>$${base.toFixed(2)}</strong></td></tr>
        <tr><td colspan="3"><strong>IVA (${ivaPorc}%):</strong></td><td><strong>$${iva.toFixed(2)}</strong></td></tr>
        <tr class="table-primary"><td colspan="3"><strong>TOTAL:</strong></td><td><strong>$${total.toFixed(2)}</strong></td></tr>
      </tfoot>
    </table>`;
    
    $('#resumen_productos_cotizacion').html(htmlProductos);
  }

  // inicial
  $(function(){
    // si usas SweetAlert asegúrate de incluir su script en la página (o quitar showError/showSuccess)
    cargarTipos();

    // al cambiar filtro
    $('#filter_tipo_evento').on('change', function(){ cargarPaquetes($(this).val()); });
  });

})(jQuery);
</script>
<style>
.preview-cotizacion {
  font-family: Arial, sans-serif;
}

.preview-cotizacion h3 {
  color: #2c3e50;
  border-bottom: 2px solid #3498db;
  padding-bottom: 10px;
}

.preview-cotizacion .table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.preview-cotizacion .table-primary {
  background-color: #d1ecf1 !important;
}

#btn_cancelar {
  border-color: #dc3545;
  color: #dc3545;
}

#btn_cancelar:hover {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
}

#btn_agregar_cliente {
  border-color: #28a745;
  color: #28a745;
}

#btn_agregar_cliente:hover {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
}

.border {
  border: 1px solid #dee2e6 !important;
}

.rounded {
  border-radius: 0.25rem !important;
}

#cliente_seleccionado_nombre::placeholder {
  color: #6c757d;
  font-style: italic;
}

/* Espaciado entre bloques de campos en la vista principal */
.card .card-body > .row > [class^="col-"],
.card .card-body > .form-group,
.card .card-body > .d-flex,
.card .card-body > .form-row {
  margin-bottom: 1.2rem;
}

/* Espaciado entre bloques en modales */
.modal-body > .row > [class^="col-"],
.modal-body > .form-group,
.modal-body > .alert,
.modal-body > .info-section {
  margin-bottom: 1.2rem;
}
</style>
<link rel="stylesheet" href="../recursos/css/cotizaciones.css">