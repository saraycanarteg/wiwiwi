/* FUNCIONES GENERALES */
$(document).ready(function() {
    // Enviar formulario
    $('#proveedorForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const texto = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
        
        $.ajax({
            url: '../controles/ajax_proveedores.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(r) {
                mensaje(r.mensaje, r.success ? 'success' : 'danger');
                if (r.success) {
                    if ($('input[name="accion"]').val() === 'crear') limpiar();
                    cargarTabla();
                }
            },
            error: function() { mensaje('Error de conexión', 'danger'); },
            complete: function() { btn.html(texto).prop('disabled', false); }
        });
    });
    
    // Editar
    $(document).on('click', '.btn-editar', function() {
        $.get('../controles/ajax_proveedores.php?accion=obtener&id=' + $(this).data('id'), function(r) {
            if (r.success) {
                $('#nombre').val(r.data.nombre);
                $('#ruc').val(r.data.ruc);
                $('#correo').val(r.data.correo);
                $('#telefono').val(r.data.telefono);
                $('#direccion').val(r.data.direccion);
                $('input[name="accion"]').val('editar');
                $('#proveedorForm').append('<input type="hidden" name="id_proveedor" value="' + r.data.id_proveedor + '">');
                $('.form-container h3').text('Editar Proveedor');
                $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
            }
        }, 'json');
    });
    
    // Cambiar estado
    $(document).on('click', '.btn-toggle', function() {
        if (!confirm('¿Confirmar acción?')) return;
        $.post('../controles/ajax_proveedores.php', {
            accion: 'cambiar_estado',
            id: $(this).data('id'),
            estado: $(this).data('estado')
        }, function(r) {
            mensaje(r.mensaje, r.success ? 'success' : 'danger');
            if (r.success) cargarTabla();
        }, 'json');
    });
    
    cargarTabla(); // Solo cargar si necesario
});

function cargarTabla() {
    $.get('../controles/ajax_proveedores.php?accion=cargar_tabla', function(html) {
        $('#tabla-proveedores').html(html);
    });
}

function mensaje(text, tipo) {
    $('.alert').remove();
    $('.main-title').after(`<div class="alert alert-${tipo} alert-dismissible fade show">${text}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
    setTimeout(() => $('.alert').fadeOut(), 4000);
}

function limpiar() {
    $('#proveedorForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_proveedor"]').remove();
    $('.form-container h3').text('Nuevo Proveedor');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar');
}

// Validación en tiempo real del RUC
document.getElementById('ruc').addEventListener('input', function() {
    const ruc = this.value;
    const isValid = /^\d{10,13}$/.test(ruc);
    
    if (ruc.length > 0 && !isValid) {
        this.setCustomValidity('El RUC debe contener entre 10 y 13 dígitos');
    } else {
        this.setCustomValidity('');
    }
});

// Validación del formulario antes del envío
document.getElementById('proveedorForm').addEventListener('submit', function(e) {
    const ruc = document.getElementById('ruc').value;
    const correo = document.getElementById('correo').value;
    
    if (!/^\d{10,13}$/.test(ruc)) {
        e.preventDefault();
        alert('El RUC debe contener entre 10 y 13 dígitos');
        return;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        e.preventDefault();
        alert('Por favor ingrese un correo electrónico válido');
        return;
    }
});

// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);