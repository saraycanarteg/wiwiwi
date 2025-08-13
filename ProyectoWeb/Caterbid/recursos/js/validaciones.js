/* VALIDACIONES EN TIEMPO REAL - ARCHIVO INDEPENDIENTE */
// Este archivo contiene únicamente las validaciones en tiempo real
// para ser usado junto con formularios.js

$(document).ready(function() {
    // Inicializar validaciones en tiempo real
    initRealTimeValidations();
    
    // Configurar validaciones de seguridad
    setupSecurityValidations();
    
    // Auto-dismiss alerts después de 5 segundos
    setupAlertAutoDismiss();
});

// ========================================
// CONFIGURACIÓN PRINCIPAL DE VALIDACIONES
// ========================================
function initRealTimeValidations() {
    // Configuración de campos con sus reglas de validación
    const fieldRules = {
        '#ruc': {
            type: 'ruc',
            required: true,
            transform: value => value.replace(/\D/g, ''), // Solo números
            events: ['input', 'keyup', 'paste']
        },
        'input[type="email"], #correo': {
            type: 'email',
            required: true,
            events: ['input', 'keyup', 'blur', 'paste']
        },
        '#telefono': {
            type: 'phone',
            required: true,
            transform: value => value.replace(/\D/g, '').slice(0, 10), // Solo 10 números
            events: ['input', 'keyup', 'paste']
        },
        '#precio_unitario': {
            type: 'price',
            required: true,
            transform: value => {
                // Solo números y un punto decimal
                value = value.replace(/[^0-9.]/g, '');
                const parts = value.split('.');
                return parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : value;
            },
            events: ['input', 'keyup', 'blur', 'paste']
        },
        '#cantidad_disponible': {
            type: 'number',
            required: true,
            transform: value => value.replace(/\D/g, ''), // Solo números
            events: ['input', 'keyup', 'paste']
        },
        '#nombre, #nombre_rol': {
            type: 'name',
            required: true,
            events: ['input', 'keyup', 'paste']
        },
        '#descripcion': {
            type: 'description',
            required: false,
            events: ['input', 'keyup', 'blur', 'paste']
        },
        '#direccion': {
            type: 'address',
            required: true,
            events: ['input', 'keyup', 'blur', 'paste']
        },
        '#categoria, #id_proveedor, #categoria_evento, #tipo_evento': {
            type: 'select',
            required: true,
            events: ['change']
        }
    };

    // Aplicar validaciones a los campos
    Object.entries(fieldRules).forEach(([selector, rules]) => {
        rules.events.forEach(event => {
            $(document).on(event, selector, function() {
                let value = $(this).val();
                
                // Aplicar transformación si existe
                if (rules.transform) {
                    value = rules.transform(value);
                    $(this).val(value);
                }
                
                // Validar campo
                validateField(this, rules);
            });
        });
    });
    
    // Validación de formulario completo antes del envío
    $(document).on('submit', 'form', function(e) {
        if (!validateFormCompletely(this)) {
            e.preventDefault();
            return false;
        }
    });
}

// ========================================
// VALIDACIONES DE SEGURIDAD
// ========================================
function setupSecurityValidations() {
    // Anti-SQL Injection para campos de texto
    $(document).on('input blur paste', 'input[type="text"], input[type="email"], textarea', function() {
        // Excluir campos numéricos y específicos
        if (!$(this).is('#precio_unitario, #cantidad_disponible, #telefono, #ruc')) {
            const validation = validateSQLSafety($(this).val());
            if (!validation.isValid) {
                showFieldError($(this), validation.message);
            }
        }
    });
    
    // Prevenir caracteres peligrosos en tiempo real
    $(document).on('keydown', 'input, textarea', function(e) {
        const dangerousChars = ['\'', '"', '<', '>', '&'];
        const char = String.fromCharCode(e.which);
        
        // Permitir punto decimal en campos de precio
        if (char === '.' && $(this).is('#precio_unitario, input[type="number"], .precio-field')) {
            return true; // Permitir punto en campos de precio
        }
        
        if (dangerousChars.includes(char) && !$(this).is('#precio_unitario')) {
            e.preventDefault();
            showTemporaryMessage($(this), 'Carácter no permitido: ' + char);
            return false;
        }
    });

    // Validación de permisos para roles
    $(document).on('change', 'input[name="permisos[]"]', validatePermissions);
    
    // Validación para campos de cantidad en paquetes
    $(document).on('input change', 'input[name^="cantidades"]', function() {
        const valor = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));
        
        if (isNaN(valor) || valor <= 0) {
            showFieldError($(this), 'La cantidad debe ser mayor a 0');
        } else if (max && valor > max) {
            showFieldError($(this), `La cantidad no puede exceder ${max}`);
        } else {
            showFieldSuccess($(this));
        }
    });
}

// ========================================
// FUNCIÓN PRINCIPAL DE VALIDACIÓN
// ========================================
function validateField(element, rules) {
    const $field = $(element);
    const value = $field.val() ? $field.val().trim() : '';
    
    // Limpiar estados anteriores
    removeFieldError($field);
    
    // Campo requerido
    if (rules.required && !value) {
        showFieldError($field, getFieldLabel($field) + ' es requerido');
        return false;
    }
    
    // Si vacío y no requerido, es válido
    if (!value && !rules.required) {
        removeFieldError($field);
        return true;
    }
    
    // Validaciones específicas por tipo
    const validation = getValidationForType(rules.type, value, rules);
    
    if (!validation.isValid) {
        showFieldError($field, validation.message);
        return false;
    }
    
    // Campo válido
    showFieldSuccess($field);
    return true;
}

// ========================================
// VALIDACIONES ESPECÍFICAS POR TIPO
// ========================================
function getValidationForType(type, value, rules = {}) {
    const validations = {
        ruc: () => {
            if (!/^\d+$/.test(value)) {
                return { isValid: false, message: 'RUC solo debe contener números' };
            }
            if (value.length < 10) {
                return { isValid: false, message: `RUC necesita ${10 - value.length} dígitos más` };
            }
            if (value.length > 13) {
                return { isValid: false, message: 'RUC no puede tener más de 13 dígitos' };
            }
            return { isValid: true };
        },
        
        email: () => {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return { isValid: false, message: 'Ingrese un email válido (ej: usuario@dominio.com)' };
            }
            if (value.length > 100) {
                return { isValid: false, message: 'Email demasiado largo (máximo 100 caracteres)' };
            }
            return { isValid: true };
        },
        
        phone: () => {
            if (!/^\d{10}$/.test(value)) {
                const message = value.length < 10 
                    ? `El teléfono necesita ${10 - value.length} dígitos más`
                    : 'El teléfono debe tener exactamente 10 dígitos';
                return { isValid: false, message };
            }
            return { isValid: true };
        },
        
        price: () => {
            const price = parseFloat(value);
            if (isNaN(price)) {
                return { isValid: false, message: 'Ingrese un precio válido' };
            }
            if (price < 0.01) {
                return { isValid: false, message: 'El precio debe ser mayor a $0.01' };
            }
            if (price > 999999.99) {
                return { isValid: false, message: 'El precio no puede ser mayor a $999,999.99' };
            }
            return { isValid: true };
        },
        
        number: () => {
            const number = parseInt(value);
            if (isNaN(number)) {
                return { isValid: false, message: 'Debe ser un número válido' };
            }
            if (number < 0) {
                return { isValid: false, message: 'Debe ser mayor o igual a 0' };
            }
            if (number > 999999) {
                return { isValid: false, message: 'No puede ser mayor a 999,999' };
            }
            return { isValid: true };
        },
        
        name: () => {
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(value)) {
                return { isValid: false, message: 'Solo se permiten letras y espacios' };
            }
            if (value.length < 2) {
                return { isValid: false, message: 'Debe tener al menos 2 caracteres' };
            }
            if (value.length > 100) {
                return { isValid: false, message: 'No puede tener más de 100 caracteres' };
            }
            return { isValid: true };
        },
        
        description: () => {
            if (value && value.length < 5) {
                return { isValid: false, message: 'La descripción debe tener al menos 5 caracteres' };
            }
            if (value.length > 500) {
                return { isValid: false, message: 'La descripción no puede tener más de 500 caracteres' };
            }
            return validateSQLSafety(value);
        },
        
        address: () => {
            if (value.length < 10) {
                return { isValid: false, message: 'La dirección debe tener al menos 10 caracteres' };
            }
            if (value.length > 200) {
                return { isValid: false, message: 'La dirección no puede tener más de 200 caracteres' };
            }
            return validateSQLSafety(value);
        },
        
        select: () => {
            if (value === '' || value === null) {
                return { isValid: false, message: 'Debe seleccionar una opción' };
            }
            return { isValid: true };
        }
    };
    
    return validations[type] ? validations[type]() : { isValid: true };
}

// ========================================
// VALIDACIÓN ANTI-SQL INJECTION
// ========================================
function validateSQLSafety(input) {
    if (!input) return { isValid: true, message: '' };
    
    const inputLower = input.toLowerCase();
    
    // Patrones peligrosos
    const dangerousPatterns = [
        { 
            pattern: /['";\\]/, 
            message: "Caracteres no permitidos: ' \" ; \\" 
        },
        { 
            pattern: /(select|insert|update|delete|drop|create|alter|truncate|union|exec)/i, 
            message: 'Contiene palabras SQL no permitidas' 
        },
        { 
            pattern: /(\w+\s*=\s*\w+)|(\d+\s*=\s*\d+)/i, 
            message: 'Contiene patrones sospechosos' 
        },
        { 
            pattern: /(script|javascript|<|>)/i, 
            message: 'Contiene código no permitido' 
        }
    ];
    
    for (let { pattern, message } of dangerousPatterns) {
        if (pattern.test(input)) {
            return { isValid: false, message };
        }
    }
    
    return { isValid: true, message: '' };
}

// ========================================
// VALIDACIONES ESPECÍFICAS
// ========================================
function validatePermissions() {
    const checkedPermissions = $('input[name="permisos[]"]:checked').length;
    const $container = $('.permissions-container');
    
    if (checkedPermissions === 0) {
        $container.addClass('has-error');
        if ($container.find('.permission-error').length === 0) {
            $container.append('<div class="permission-error text-danger mt-2">Debe seleccionar al menos un permiso</div>');
        }
        return false;
    } else {
        $container.removeClass('has-error').find('.permission-error').remove();
        return true;
    }
}

function validateFormCompletely(form) {
    const $form = $(form);
    let isValid = true;
    
    // Validar campos requeridos visibles
    $form.find('input[required]:visible, textarea[required]:visible, select[required]:visible').each(function() {
        const value = $(this).val();
        if (!value || value.trim() === '') {
            showFieldError($(this), getFieldLabel($(this)) + ' es requerido');
            isValid = false;
        }
    });
    
    // Validar campos con clases de error
    if ($form.find('.is-invalid').length > 0) {
        isValid = false;
    }
    
    // Validación específica para formulario de roles
    if ($form.is('#rolForm') && !validatePermissions()) {
        isValid = false;
    }
    
    // Validación específica para formulario de paquetes
    if ($form.is('#paqueteForm')) {
        const productosSeleccionados = $('input[name="productos[]"]:checked');
        if (productosSeleccionados.length === 0) {
            mensaje('Debe seleccionar al menos un producto para el paquete', 'danger');
            isValid = false;
        }
        
        // Validar cantidades de productos seleccionados
        let cantidadesValidas = true;
        productosSeleccionados.each(function() {
            const idProducto = $(this).val();
            const cantidadInput = $(`input[name="cantidades[${idProducto}]"]`);
            const cantidad = parseInt(cantidadInput.val());
            const maxStock = parseInt(cantidadInput.attr('max'));
            
            if (isNaN(cantidad) || cantidad <= 0) {
                showFieldError(cantidadInput, 'La cantidad debe ser mayor a 0');
                cantidadesValidas = false;
            } else if (maxStock && cantidad > maxStock) {
                showFieldError(cantidadInput, `La cantidad no puede exceder el stock disponible (${maxStock})`);
                cantidadesValidas = false;
            }
        });
        
        if (!cantidadesValidas) {
            isValid = false;
        }
    }
    
    if (!isValid) {
        // Hacer scroll al primer campo con error y enfocarlo
        const $firstError = $form.find('.is-invalid').first();
        if ($firstError.length > 0) {
            $firstError.focus();
            $firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        showFormError('Por favor, corrija los errores señalados');
    }
    
    return isValid;
}

// ========================================
// FUNCIONES AUXILIARES PARA MOSTRAR ESTADOS
// ========================================
function showFieldError($field, message) {
    $field.removeClass('is-valid').addClass('is-invalid');
    
    // Remover mensaje anterior si existe
    $field.siblings('.invalid-feedback').remove();
    
    // Agregar nuevo mensaje
    $field.after('<div class="invalid-feedback d-block">' + message + '</div>');
}

function showFieldSuccess($field) {
    $field.removeClass('is-invalid').addClass('is-valid');
    $field.siblings('.invalid-feedback').remove();
}

function removeFieldError($field) {
    $field.removeClass('is-invalid is-valid');
    $field.siblings('.invalid-feedback').remove();
}

function showTemporaryMessage($field, message) {
    // Remover mensajes temporales anteriores
    $field.siblings('.temp-message').remove();
    
    const $tempMsg = $('<div class="temp-message text-danger small mt-1">' + message + '</div>');
    $field.after($tempMsg);
    
    setTimeout(() => {
        $tempMsg.fadeOut(() => $tempMsg.remove());
    }, 2000);
}

function showFormError(message) {
    $('.form-error').remove();
    
    const $errorDiv = $('<div class="form-error alert alert-danger alert-dismissible fade show mt-3">' + 
                      message + 
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                      '</div>');
    
    $('form').first().before($errorDiv);
    
    setTimeout(() => {
        $errorDiv.fadeOut(() => $errorDiv.remove());
    }, 5000);
}

// ========================================
// FUNCIONES AUXILIARES
// ========================================
function getFieldLabel($field) {
    const fieldId = $field.attr('id');
    const $label = $('label[for="' + fieldId + '"]');
    
    if ($label.length > 0) {
        return $label.text().replace(/\*+/g, '').trim();
    }
    
    // Mapeo de nombres de campos comunes
    const fieldNames = {
        'nombre': 'Nombre',
        'ruc': 'RUC', 
        'correo': 'Email',
        'telefono': 'Teléfono',
        'direccion': 'Dirección', 
        'precio_unitario': 'Precio',
        'cantidad_disponible': 'Cantidad',
        'descripcion': 'Descripción', 
        'nombre_rol': 'Nombre del rol',
        'categoria': 'Categoría',
        'id_proveedor': 'Proveedor',
        'categoria_evento': 'Categoría de evento',
        'tipo_evento': 'Tipo de evento'
    };
    
    return fieldNames[fieldId] || 'Este campo';
}

function setupAlertAutoDismiss() {
    // Auto-dismiss alerts después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.form-error)');
        alerts.forEach(function(alert) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                $(alert).fadeOut();
            }
        });
    }, 5000);
}

// ========================================
// FUNCIÓN PÚBLICA PARA LIMPIAR VALIDACIONES
// ========================================
function clearAllFieldValidations(formSelector) {
    $(formSelector + ' .is-invalid, ' + formSelector + ' .is-valid').removeClass('is-invalid is-valid');
    $(formSelector + ' .invalid-feedback').remove();
    $(formSelector + ' .temp-message').remove();
    $(formSelector + ' .form-error').remove();
    $('.permissions-container').removeClass('has-error').find('.permission-error').remove();
}

// Función pública para validar un campo específico desde fuera
function validateSpecificField(fieldSelector, rules) {
    const $field = $(fieldSelector);
    if ($field.length > 0) {
        return validateField($field[0], rules);
    }
    return false;
}

// ========================================
// FUNCIONES ESPECÍFICAS PARA MENSAJE
// ========================================
function mensaje(text, tipo) {
    $('.alert').remove();
    const alertHtml = `<div class="alert alert-${tipo} alert-dismissible fade show">
                       ${text}
                       <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                       </div>`;
    
    if ($('.main-title').length > 0) {
        $('.main-title').after(alertHtml);
    } else {
        $('body').prepend(alertHtml);
    }
    
    setTimeout(() => $('.alert').fadeOut(), 4000);
}