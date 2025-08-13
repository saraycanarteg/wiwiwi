<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.html");
    exit();
}
require_once '../../includes/verificar_permisos.php';
requierePermiso('historial_cotizaciones');

require_once '../../config/database.php';
if (!isset($conn) || $conn->connect_error) {
    echo '<div class="alert alert-danger">Error de conexión a la base de datos.</div>';
    exit();
}
?>
<div class="container-fluid px-2" style="max-width: 1300px; margin: 0 auto;">
  <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-history mr-2"></i>Historial de Cotizaciones</h1>

  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary" style="flex:1 1 200px;min-width:180px;"><i class="fas fa-search mr-1"></i>Buscar Cliente</h6>
      <div style="flex:0 0 auto;">
        <button type="button" id="btnMostrarTodas" class="btn btn-info btn-sm mt-2 mt-md-0" style="white-space:nowrap;">
          <i class="fas fa-list mr-1"></i>Mostrar Todas las Cotizaciones
        </button>
      </div>
    </div>
    <div class="card-body">
      <form id="formBuscar">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group mb-2">
              <label for="busqueda" style="font-size:0.97em;white-space:nowrap;">Cédula/RUC o Nombre del Cliente</label>
              <input type="text" id="busqueda" class="form-control" 
                     placeholder="Ingrese número de identificación o nombre del cliente" style="min-width:0;">
            </div>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-group w-100 mb-2" style="margin-bottom:0;">
              <button type="submit" class="btn btn-primary btn-block" style="min-width:120px;white-space:nowrap;">
                <i class="fas fa-search"></i> Buscar
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow mb-4 w-100" style="max-width:100vw;">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table mr-1"></i>Resultados de Búsqueda</h6>
    </div>
    <div class="card-body p-0">
      <div id="tabla_resultados" class="w-100">
        <div class="text-center text-muted py-4">
          <i class="fas fa-search fa-2x mb-2"></i>
          <p>Ingrese un valor de búsqueda para ver las cotizaciones del cliente o use "Mostrar Todas" para ver todas las cotizaciones</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para mostrar productos -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductosTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductosTitle"><i class="fas fa-boxes mr-1"></i>Productos de la Cotización</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalProductosBody">
        <!-- Aquí se cargarán los productos -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para visualizar cotización completa -->
<div class="modal fade" id="modalVisualizarCotizacion" tabindex="-1" role="dialog" aria-labelledby="modalVisualizarTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalVisualizarTitle"><i class="fas fa-eye mr-1"></i>Visualización de Cotización</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalVisualizarCotizacionBody">
        <!-- Aquí se cargará la cotización completa -->
      </div>
      <div class="modal-footer flex-wrap">
        <button type="button" id="btnGenerarPDFModal" class="btn btn-info mb-2 mb-md-0">
          <i class="fas fa-file-pdf mr-1"></i>Generar PDF
        </button>
        <button type="button" class="btn btn-secondary mb-2 mb-md-0" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Librerías PDF - usar CDN que funcionan juntas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
// Verificar que las librerías estén cargadas
$(document).ready(function() {
    console.log('jQuery cargado:', typeof $ !== 'undefined');
    
    // Esperar un poco para que las librerías se carguen
    setTimeout(function() {
        console.log('jsPDF disponible:', typeof window.jspdf !== 'undefined');
        console.log('autoTable disponible:', typeof window.jspdf?.jsPDF?.API?.autoTable !== 'undefined');
        
        if (typeof window.jspdf === 'undefined') {
            console.error('jsPDF no se cargó correctamente');
        }
    }, 1000);
});

(function($){
  // Ruta AJAX corregida
  const AJAX_URL = '../controles/ajax_historial.php';
  let cotizacionActual = null;

  function showError(msg) {
    // Usar SweetAlert2 para mostrar errores
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: msg,
      confirmButtonColor: '#d33'
    });
  }

  function showSuccess(msg) {
    // Usar SweetAlert2 para mostrar éxito
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: msg,
      timer: 2000,
      showConfirmButton: false
    });
  }

  function getEstadoBadge(estado) {
    let badgeClass = '';
    let texto = '';
    
    switch(estado) {
      case 'enviada':
        badgeClass = 'badge-warning';
        texto = 'Enviada';
        break;
      case 'aceptada':
        badgeClass = 'badge-success';
        texto = 'Aceptada';
        break;
      case 'rechazada':
        badgeClass = 'badge-danger';
        texto = 'Rechazada';
        break;
      default:
        badgeClass = 'badge-secondary';
        texto = estado;
    }
    
    return `<span class="badge ${badgeClass}">${texto}</span>`;
  }

  function renderTabla(datos) {
    if (!Array.isArray(datos) || datos.length === 0) {
      $('#tabla_resultados').html(`
        <div class="text-center text-muted py-4">
          <i class="fas fa-inbox fa-2x mb-2"></i>
          <p>No se encontraron cotizaciones.</p>
        </div>
      `);
      return;
    }

    let html = `
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-dark">
            <tr>
              <th><i class="fas fa-hashtag"></i> ID</th>
              <th><i class="fas fa-calendar"></i> Fecha</th>
              <th><i class="fas fa-user"></i> Cliente</th>
              <th><i class="fas fa-id-card"></i> Cédula/RUC</th>
              <th><i class="fas fa-gift"></i> Tipo de Evento</th>
              <th><i class="fas fa-user-tie"></i> Proveedor</th>
              <th><i class="fas fa-dollar-sign"></i> Total</th>
              <th><i class="fas fa-flag"></i> Estado</th>
              <th><i class="fas fa-cogs"></i> Acciones</th>
            </tr>
          </thead>
          <tbody>`;

    datos.forEach(function(row){
      html += `
        <tr>
          <td><strong>#${row.id_cotizacion}</strong></td>
          <td>${row.fecha_formateada}</td>
          <td>${row.nombres}</td>
          <td>${row.identificacion}</td>
          <td>${row.tipo_evento}</td>
          <td>${row.proveedor_nombre ? row.proveedor_nombre : '<span class="text-muted">Sin proveedor</span>'}</td>
          <td><strong>$${parseFloat(row.total).toFixed(2)}</strong></td>
          <td>${getEstadoBadge(row.estado)}</td>
          <td>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-info btn-sm" onclick="verProductos(${row.id_cotizacion})" title="Ver productos">
                <i class="fas fa-list"></i>
              </button>
              <!-- Botón de visualizar cotización eliminado -->
              <button type="button" class="btn btn-danger btn-sm" onclick="generarPDF(${row.id_cotizacion})" title="Generar PDF">
                <i class="fas fa-file-pdf"></i>
              </button>
            </div>
          </td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    $('#tabla_resultados').html(html);
  }

  // Función global para ver productos
  window.verProductos = function(idCotizacion) {
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {accion: 'obtener_productos', id_cotizacion: idCotizacion},
      dataType: 'json',
      success: function(resp) {
        if (!resp.success) {
          return showError(resp.mensaje || 'Error al obtener productos');
        }
        
        if (!resp.productos || resp.productos.length === 0) {
          $('#modalProductosBody').html(`
            <div class="text-center text-muted py-4">
              <i class="fas fa-box-open fa-2x mb-2"></i>
              <p>No se encontraron productos para esta cotización.</p>
            </div>
          `);
        } else {
          let html = `
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead class="thead-light">
                  <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>`;

          let total = 0;
          resp.productos.forEach(function(producto) {
            html += `
              <tr>
                <td><strong>${producto.nombre}</strong></td>
                <td><span class="badge badge-info">${producto.categoria}</span></td>
                <td><small>${producto.descripcion || 'Sin descripción'}</small></td>
                <td>${producto.cantidad}</td>
                <td>$${parseFloat(producto.precio_unitario).toFixed(2)}</td>
                <td><strong>$${parseFloat(producto.subtotal).toFixed(2)}</strong></td>
              </tr>`;
            total += parseFloat(producto.subtotal);
          });

          html += `
                  <tr class="table-dark">
                    <td colspan="5"><strong>TOTAL</strong></td>
                    <td><strong>$${total.toFixed(2)}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>`;
          
          $('#modalProductosBody').html(html);
        }
        
        $('#modalProductos').modal('show');
      },
      error: function(xhr) {
        showError('Error AJAX al obtener productos: ' + (xhr.responseText || xhr.statusText));
      }
    });
  };

  // Función global para visualizar cotización completa
  window.visualizarCotizacion = function(idCotizacion) {
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {accion: 'obtener_cotizacion_completa', id_cotizacion: idCotizacion},
      dataType: 'json',
      success: function(resp) {
        if (!resp.success) {
          return showError(resp.mensaje || 'Error al obtener cotización');
        }
        
        cotizacionActual = resp.cotizacion;
        const cotizacion = resp.cotizacion;
        
        // Calcular totales - CORREGIDO para usar IVA fijo
        const ivaPorc = 15; // IVA fijo del 15% para Ecuador
        const totalConIva = parseFloat(cotizacion.total);
        const totalSinIva = totalConIva / (1 + ivaPorc/100);
        const totalIva = totalConIva - totalSinIva;
        
        let html = `
          <div class="cotizacion-preview">
            <div class="text-center mb-4">
              <h3>Cotización #${cotizacion.id_cotizacion}</h3>
              <p class="text-muted">Fecha: ${cotizacion.fecha_formateada}</p>
              <p class="text-muted">Estado: ${getEstadoBadge(cotizacion.estado)}</p>
            </div>
            
            <div class="row mb-4">
              <div class="col-md-6">
                <h5>Datos del Cliente</h5>
                <div class="border p-3 rounded">
                  <p><strong>Nombre/Empresa:</strong> ${cotizacion.nombres}</p>
                  <p><strong>Identificación:</strong> ${cotizacion.identificacion}</p>
                  <p><strong>Correo:</strong> ${cotizacion.correo}</p>
                  <p><strong>Celular:</strong> ${cotizacion.celular}</p>
                  <p><strong>Ciudad:</strong> ${cotizacion.ciudad || 'No especificada'}</p>
                  <p><strong>Dirección:</strong> ${cotizacion.direccion || 'No especificada'}</p>
                </div>
              </div>
              <div class="col-md-6">
                <h5>Datos del Paquete</h5>
                <div class="border p-3 rounded">
                  <p><strong>Tipo de Evento:</strong> ${cotizacion.tipo_evento}</p>
                  <p><strong>Proveedor:</strong> ${cotizacion.proveedor_nombre || 'Sin proveedor'}</p>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <h5>Productos de la Cotización</h5>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>Producto</th>
                      <th>Categoría</th>
                      <th>Cantidad</th>
                      <th>Precio Unit.</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>`;

        cotizacion.productos.forEach(function(producto) {
          html += `
            <tr>
              <td><strong>${producto.nombre}</strong></td>
              <td><span class="badge badge-info">${producto.categoria}</span></td>
              <td>${producto.cantidad}</td>
              <td>$${parseFloat(producto.precio_unitario).toFixed(2)}</td>
              <td>$${parseFloat(producto.subtotal).toFixed(2)}</td>
            </tr>`;
        });

        html += `
                  </tbody>
                </table>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 offset-md-6">
                <table class="table table-sm">
                  <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>$${totalSinIva.toFixed(2)}</td>
                  </tr>
                  <tr>
                    <td><strong>IVA (${ivaPorc}%):</strong></td>
                    <td>$${totalIva.toFixed(2)}</td>
                  </tr>
                  <tr class="table-primary">
                    <td><strong>Total:</strong></td>
                    <td><strong>$${parseFloat(cotizacion.total).toFixed(2)}</strong></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>`;
        
        $('#modalVisualizarCotizacionBody').html(html);
        $('#modalVisualizarCotizacion').modal('show');
      },
      error: function(xhr) {
        showError('Error AJAX al obtener cotización: ' + (xhr.responseText || xhr.statusText));
      }
    });
  };

  // Función global para generar PDF - CORREGIDA
  window.generarPDF = function(idCotizacion) {
    console.log('Generando PDF para cotización:', idCotizacion);
    
    // Verificar que jsPDF esté disponible
    if (typeof window.jspdf === 'undefined') {
      showError('Las librerías de PDF no están disponibles. Por favor, recarga la página.');
      return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
      title: 'Generando PDF...',
      text: 'Por favor espere mientras se genera el documento',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Obtener datos completos de la cotización
    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {accion: 'obtener_cotizacion_completa', id_cotizacion: idCotizacion},
      dataType: 'json',
      success: function(resp) {
        console.log('Respuesta del servidor:', resp);
        if (!resp.success) {
          Swal.close();
          return showError(resp.mensaje || 'Error al obtener datos de la cotización');
        }
        
        try {
          generarPDFLocal(resp.cotizacion);
          Swal.close();
          showSuccess('PDF generado correctamente');
        } catch (error) {
          Swal.close();
          console.error('Error al generar PDF:', error);
          showError('Error al generar el archivo PDF: ' + error.message);
        }
      },
      error: function(xhr, status, error) {
        Swal.close();
        console.error('Error AJAX:', {xhr, status, error});
        console.error('Response text:', xhr.responseText);
        showError('Error al obtener datos para el PDF. Revisa la consola para más detalles.');
      }
    });
  };

  // Función para generar PDF localmente usando jsPDF - CORREGIDA
  function generarPDFLocal(cotizacion) {
    console.log('Generando PDF local con datos:', cotizacion);
    
    try {
      // Verificar que jsPDF esté disponible
      if (!window.jspdf || !window.jspdf.jsPDF) {
        throw new Error('jsPDF no está disponible');
      }
      
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      // Verificar que autoTable esté disponible
      if (typeof doc.autoTable !== 'function') {
        console.error('autoTable no está disponible, generando PDF simple');
        return generarPDFSimple(cotizacion, doc);
      }

      // Configuración de colores y fuentes
      const primaryColor = [41, 128, 185];
      const grayColor = [128, 128, 128];
      
      console.log('Configurando encabezado...');
      
      // Encabezado
      doc.setFontSize(20);
      doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
      doc.text('COTIZACIÓN', 20, 25);
      
      doc.setFontSize(12);
      doc.setTextColor(0, 0, 0);
      doc.text(`#${cotizacion.id_cotizacion}`, 160, 25);
      
      // Fecha y estado
      doc.setFontSize(10);
      doc.setTextColor(grayColor[0], grayColor[1], grayColor[2]);
      doc.text(`Fecha: ${cotizacion.fecha_formateada}`, 20, 35);
      doc.text(`Estado: ${cotizacion.estado.toUpperCase()}`, 20, 42);
      
      // Línea separadora
      doc.setLineWidth(0.5);
      doc.setDrawColor(primaryColor[0], primaryColor[1], primaryColor[2]);
      doc.line(20, 48, 190, 48);
      
      console.log('Agregando información del cliente...');
      
      // Información del cliente
      doc.setFontSize(14);
      doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
      doc.text('INFORMACIÓN DEL CLIENTE', 20, 60);
      
      doc.setFontSize(10);
      doc.setTextColor(0, 0, 0);
      let yPos = 70;
      
      doc.text(`Nombre/Empresa: ${cotizacion.nombres}`, 20, yPos);
      yPos += 7;
      doc.text(`Identificación: ${cotizacion.identificacion}`, 20, yPos);
      yPos += 7;
      doc.text(`Correo: ${cotizacion.correo}`, 20, yPos);
      yPos += 7;
      doc.text(`Celular: ${cotizacion.celular}`, 20, yPos);
      yPos += 7;
      if (cotizacion.ciudad) {
        doc.text(`Ciudad: ${cotizacion.ciudad}`, 20, yPos);
        yPos += 7;
      }
      if (cotizacion.direccion) {
        doc.text(`Dirección: ${cotizacion.direccion}`, 20, yPos);
        yPos += 7;
      }
      
      // Información del evento
      yPos += 5;
      doc.setFontSize(14);
      doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
      doc.text('INFORMACIÓN DEL EVENTO', 20, yPos);
      
      yPos += 10;
      doc.setFontSize(10);
      doc.setTextColor(0, 0, 0);
      doc.text(`Tipo de Evento: ${cotizacion.tipo_evento}`, 20, yPos);
      yPos += 7;
      if (cotizacion.proveedor_nombre) {
        doc.text(`Proveedor: ${cotizacion.proveedor_nombre}`, 20, yPos);
        yPos += 7;
      }
      
      console.log('Generando tabla de productos...');
      
      // Tabla de productos
      yPos += 10;
      
      // Preparar datos para la tabla
      const tableHeaders = ['Producto', 'Categoría', 'Cantidad', 'Precio Unit.', 'Subtotal'];
      const tableData = [];
      
      if (cotizacion.productos && cotizacion.productos.length > 0) {
        cotizacion.productos.forEach(producto => {
          tableData.push([
            producto.nombre || '',
            producto.categoria || '',
            producto.cantidad ? producto.cantidad.toString() : '0',
            `$${parseFloat(producto.precio_unitario || 0).toFixed(2)}`,
            `$${parseFloat(producto.subtotal || 0).toFixed(2)}`
          ]);
        });
      }
      
      // Generar tabla con autoTable
      doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: yPos,
        styles: {
          fontSize: 9,
          cellPadding: 3,
        },
        headStyles: {
          fillColor: primaryColor,
          textColor: 255,
          fontStyle: 'bold'
        },
        alternateRowStyles: {
          fillColor: [245, 245, 245]
        },
        margin: { left: 20, right: 20 }
      });
      
      console.log('Agregando totales...');
      
      // Calcular totales
      const totalConIva = parseFloat(cotizacion.total) || 0;
      const ivaPorc = 15;
      const totalSinIva = totalConIva / (1 + ivaPorc/100);
      const totalIva = totalConIva - totalSinIva;
      
      // Posición después de la tabla
      const finalY = doc.lastAutoTable.finalY + 10;
      
      // Totales
      const totalsX = 130;
      doc.setFontSize(10);
      doc.setTextColor(0, 0, 0);
      
      doc.text('Subtotal:', totalsX, finalY);
      doc.text(`$${totalSinIva.toFixed(2)}`, totalsX + 40, finalY);
      
      doc.text(`IVA (${ivaPorc}%):`, totalsX, finalY + 7);
      doc.text(`$${totalIva.toFixed(2)}`, totalsX + 40, finalY + 7);
      
      // Total con fondo
      doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
      doc.rect(totalsX - 5, finalY + 12, 65, 8, 'F');
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(12);
      doc.setFont(undefined, 'bold');
      doc.text('TOTAL:', totalsX, finalY + 18);
      doc.text(`$${totalConIva.toFixed(2)}`, totalsX + 40, finalY + 18);
      
      // Pie de página
      const pageHeight = doc.internal.pageSize.height;
      doc.setTextColor(grayColor[0], grayColor[1], grayColor[2]);
      doc.setFontSize(8);
      doc.setFont(undefined, 'normal');
      doc.text('Este documento es una cotización válida por 30 días.', 20, pageHeight - 20);
      doc.text(`Generado el: ${new Date().toLocaleString('es-ES')}`, 20, pageHeight - 15);
      
      console.log('Descargando PDF...');
      
      // Descargar el PDF
      const nombreArchivo = `cotizacion_${cotizacion.id_cotizacion}_${new Date().toISOString().slice(0, 10)}.pdf`;
      doc.save(nombreArchivo);
      
      console.log('PDF generado exitosamente');
      
    } catch (error) {
      console.error('Error detallado al generar PDF:', error);
      throw error;
    }
  }

  // Función fallback para generar PDF simple sin autoTable
  function generarPDFSimple(cotizacion, doc) {
    console.log('Generando PDF simple sin autoTable');
    
    const primaryColor = [41, 128, 185];
    const grayColor = [128, 128, 128];
    
    // Encabezado
    doc.setFontSize(20);
    doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
    doc.text('COTIZACIÓN', 20, 25);
    
    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text(`#${cotizacion.id_cotizacion}`, 160, 25);
    
    // Información básica
    let yPos = 40;
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    
    doc.text(`Fecha: ${cotizacion.fecha_formateada}`, 20, yPos);
    yPos += 7;
    doc.text(`Estado: ${cotizacion.estado.toUpperCase()}`, 20, yPos);
    yPos += 10;
    
    // Información del cliente
    doc.setFontSize(12);
    doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
    doc.text('INFORMACIÓN DEL CLIENTE', 20, yPos);
    yPos += 10;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    doc.text(`Nombre: ${cotizacion.nombres}`, 20, yPos);
    yPos += 7;
    doc.text(`Identificación: ${cotizacion.identificacion}`, 20, yPos);
    yPos += 7;
    doc.text(`Correo: ${cotizacion.correo}`, 20, yPos);
    yPos += 7;
    doc.text(`Celular: ${cotizacion.celular}`, 20, yPos);
    yPos += 15;
    
    // Información del evento
    doc.setFontSize(12);
    doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
    doc.text('INFORMACIÓN DEL EVENTO', 20, yPos);
    yPos += 10;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    doc.text(`Tipo de Evento: ${cotizacion.tipo_evento}`, 20, yPos);
    yPos += 7;
    if (cotizacion.proveedor_nombre) {
      doc.text(`Proveedor: ${cotizacion.proveedor_nombre}`, 20, yPos);
      yPos += 15;
    } else {
      yPos += 8;
    }
    
    // Productos (formato simple)
    doc.setFontSize(12);
    doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
    doc.text('PRODUCTOS', 20, yPos);
    yPos += 10;
    
    doc.setFontSize(9);
    doc.setTextColor(0, 0, 0);
    
    if (cotizacion.productos && cotizacion.productos.length > 0) {
      cotizacion.productos.forEach(producto => {
        const linea = `${producto.nombre} - Cant: ${producto.cantidad} - $${parseFloat(producto.precio_unitario || 0).toFixed(2)} = $${parseFloat(producto.subtotal || 0).toFixed(2)}`;
        doc.text(linea, 20, yPos);
        yPos += 6;
      });
    }
    
    // Total
    yPos += 10;
    const totalConIva = parseFloat(cotizacion.total) || 0;
    const ivaPorc = 15;
    const totalSinIva = totalConIva / (1 + ivaPorc/100);
    const totalIva = totalConIva - totalSinIva;
    
    doc.setFontSize(10);
    doc.text(`Subtotal: $${totalSinIva.toFixed(2)}`, 130, yPos);
    yPos += 7;
    doc.text(`IVA (${ivaPorc}%): $${totalIva.toFixed(2)}`, 130, yPos);
    yPos += 7;
    
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text(`TOTAL: $${totalConIva.toFixed(2)}`, 130, yPos);
    
    // Pie de página
    const pageHeight = doc.internal.pageSize.height;
    doc.setTextColor(grayColor[0], grayColor[1], grayColor[2]);
    doc.setFontSize(8);
    doc.setFont(undefined, 'normal');
    doc.text('Este documento es una cotización válida por 30 días.', 20, pageHeight - 20);
    doc.text(`Generado el: ${new Date().toLocaleString('es-ES')}`, 20, pageHeight - 15);
    
    // Descargar el PDF
    const nombreArchivo = `cotizacion_${cotizacion.id_cotizacion}_${new Date().toISOString().slice(0, 10)}.pdf`;
    doc.save(nombreArchivo);
    
    console.log('PDF simple generado exitosamente');
  }

  // Mostrar todas las cotizaciones
  $('#btnMostrarTodas').on('click', function(){
    // Mostrar loading
    $('#tabla_resultados').html(`
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-2">Cargando todas las cotizaciones...</p>
      </div>
    `);

    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {accion: 'listar_todas'},
      dataType: 'json',
      success: function(resp) {
        if (!resp.success) {
          return showError(resp.mensaje || 'Error al cargar cotizaciones');
        }
        renderTabla(resp.datos);
      },
      error: function(xhr) {
        $('#tabla_resultados').html(`
          <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Error al cargar las cotizaciones</p>
          </div>
        `);
        showError('Error AJAX listar todas: ' + (xhr.responseText || xhr.statusText));
      }
    });
  });

  // Generar PDF desde modal - MEJORADO
  $('#btnGenerarPDFModal').on('click', function(){
    if (cotizacionActual && cotizacionActual.id_cotizacion) {
      generarPDF(cotizacionActual.id_cotizacion);
    } else {
      showError('No hay cotización seleccionada para generar PDF');
    }
  });

  $('#formBuscar').on('submit', function(e){
    e.preventDefault();
    let valor = $('#busqueda').val().trim();
    if (!valor) {
      showError('Ingrese cédula/RUC o nombre para buscar.');
      return;
    }

    // Mostrar loading
    $('#tabla_resultados').html(`
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Buscando...</span>
        </div>
        <p class="mt-2">Buscando cotizaciones...</p>
      </div>
    `);

    $.ajax({
      url: AJAX_URL,
      method: 'POST',
      data: {accion:'buscar', valor:valor},
      dataType: 'json',
      success: function(resp) {
        if (!resp.success) {
          return showError(resp.mensaje || 'Error en búsqueda');
        }
        renderTabla(resp.datos);
      },
      error: function(xhr) {
        $('#tabla_resultados').html(`
          <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Error al realizar la búsqueda</p>
          </div>
        `);
        showError('Error AJAX búsqueda: ' + (xhr.responseText || xhr.statusText));
      }
    });
  });

  // Permitir buscar con Enter
  $('#busqueda').on('keypress', function(e) {
    if (e.which === 13) {
      $('#formBuscar').submit();
    }
  });

})(jQuery);
</script>

<style>
/* Estilos generales mejorados para prevenir desbordamientos */
* {
  box-sizing: border-box;
}

/* Contenido adaptable - ajuste automático */
.container-fluid {
  width: 100% !important;
  max-width: 100vw !important;
  margin: 0 auto;
  padding-left: 1rem;
  padding-right: 1rem;
  overflow-x: hidden;
}

.card {
  width: 100% !important;
  max-width: 100% !important;
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.card-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  width: 100%;
  min-height: 60px;
}

.card-header h6 {
  flex: 1 1 auto;
  min-width: 0;
  margin: 0;
  word-break: break-word;
  overflow-wrap: break-word;
}

.card-header .btn {
  flex: 0 0 auto;
  min-width: fit-content;
}

.card-body {
  padding: 1rem;
  width: 100%;
  overflow: hidden;
}

/* Formulario responsive */
.row {
  margin: 0;
  width: 100%;
}

.col-md-8, .col-md-4 {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  width: 100%;
  max-width: 100%;
}

/* Ajustes para el contenido del mensaje inicial */
.text-center {
  padding: 1rem;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.text-center p {
  margin-bottom: 0.5rem;
  line-height: 1.4;
  font-size: 0.95rem;
}

.text-center i {
  margin-bottom: 0.5rem;
  display: block;
}

.cotizacion-preview {
  font-family: Arial, sans-serif;
}

.cotizacion-preview h3 {
  color: #2c3e50;
  border-bottom: 2px solid #3498db;
  padding-bottom: 10px;
}

.cotizacion-preview .table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.cotizacion-preview .table-primary {
  background-color: #d1ecf1 !important;
}

.border {
  border: 1px solid #dee2e6 !important;
}

.rounded {
  border-radius: 0.25rem !important;
}

/* Mejoras para botones - prevenir desbordamientos */
.btn {
  word-wrap: break-word;
  word-break: keep-all;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
  box-sizing: border-box;
  padding: 0.375rem 0.75rem;
}

.btn-group {
  display: flex;
  flex-wrap: wrap;
  gap: 2px;
  align-items: center;
  justify-content: center;
}

.btn-group .btn {
  margin: 0;
  flex: 0 0 auto;
  min-width: 32px;
  padding: 0.25rem 0.5rem;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  line-height: 1.5;
  min-width: 28px;
}

/* Estados de carga y mensajes adaptativos */
.spinner-border {
  width: 2rem;
  height: 2rem;
  margin: 0 auto 0.5rem auto;
  display: block;
}

.text-muted {
  font-size: 0.9rem;
  line-height: 1.4;
  text-align: center;
  padding: 0.5rem;
}

.text-danger {
  font-size: 0.9rem;
  line-height: 1.4;
  text-align: center;
  padding: 0.5rem;
}

/* Iconos adaptativos */
.fa-2x {
  font-size: 1.8em !important;
  margin-bottom: 0.5rem;
  display: block;
}

/* Botón "Mostrar Todas" mejorado */
#btnMostrarTodas {
  background-color: #17a2b8;
  border-color: #17a2b8;
  color: white;
  min-width: 160px;
  max-width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 0.9rem;
  box-sizing: border-box;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: keep-all;
}

#btnMostrarTodas:hover {
  background-color: #138496;
  border-color: #117a8b;
}

/* Formularios y controles */
.form-group {
  margin-bottom: 1rem;
  overflow: hidden;
}

.form-group label {
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
  white-space: normal;
  word-break: break-word;
  overflow-wrap: break-word;
  display: block;
  max-width: 100%;
}

.form-control {
  min-width: 0;
  max-width: 100%;
  box-sizing: border-box;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Tabla responsive mejorada */
#tabla_resultados {
  width: 100%;
  overflow: hidden;
}

#tabla_resultados .table-responsive {
  width: 100% !important;
  min-width: 100% !important;
  overflow-x: auto;
}

#tabla_resultados table.table {
  width: 100% !important;
  min-width: 1200px !important;
  table-layout: auto;
}

#tabla_resultados th, #tabla_resultados td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  vertical-align: middle;
  max-width: 220px;
}

#tabla_resultados th {
  text-align: left;
}

#tabla_resultados td {
  text-align: left;
}

@media (max-width: 1300px) {
  .container-fluid {
    max-width: 100vw !important;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  #tabla_resultados table.table {
    min-width: 900px !important;
  }
}

@media (max-width: 1200px) {
  #tabla_resultados th, #tabla_resultados td {
    font-size: 0.95em;
    padding: 0.4rem;
    max-width: 140px;
  }
}

@media (max-width: 900px) {
  #tabla_resultados th, #tabla_resultados td {
    font-size: 0.9em;
    padding: 0.3rem;
    max-width: 100px;
  }
  .card-header, .card-body {
    padding-left: 0.7rem !important;
    padding-right: 0.7rem !important;
  }
}

@media (max-width: 700px) {
  .container-fluid {
    padding-left: 0.2rem;
    padding-right: 0.2rem;
  }
  #tabla_resultados table.table {
    min-width: 600px !important;
  }
  .card-header, .card-body {
    padding-left: 0.3rem !important;
    padding-right: 0.3rem !important;
  }
}
</style>
<link rel="stylesheet" href="../recursos/css/cotizaciones.css">