// Validación del formulario de registro
document.getElementById("registerForm")?.addEventListener("submit", function(event) {
    const pass1 = document.getElementById("registerPassword").value;
    const pass2 = document.getElementById("registerConfirmPassword").value;

    if (pass1 !== pass2) {
        event.preventDefault();
        alert("⚠️ Las contraseñas no coinciden.");
    } else if (pass1.length < 6) {
        event.preventDefault();
        alert("⚠️ La contraseña debe tener al menos 6 caracteres.");
    }
});

// Efecto suave para la navegación al hacer scroll
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navegacion-principal');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(61, 75, 107, 0.95)';
        } else {
            navbar.style.background = 'rgba(61, 75, 107, 0.95)';
        }
    }
});

// Efectos hover para botones
document.querySelectorAll('.boton-autenticacion').forEach(button => {
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Función helper para mostrar errores
function showError(element, message) {
    element.classList.add('is-invalid');
    const feedback = element.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
        feedback.style.display = 'block';
    }
}

// Función helper para limpiar errores
function clearError(element) {
    element.classList.remove('is-invalid');
    const feedback = element.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = '';
        feedback.style.display = 'none';
    }
}

// Validación en tiempo real para login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('formularioLogin');
    const email = document.getElementById('email');
    const password = document.getElementById('contrasena');

    if (loginForm && email && password) {
        // Validación de email en tiempo real
        email.addEventListener('blur', function() {
            const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
            if (!email.value.trim()) {
                showError(email, 'El correo electrónico es obligatorio.');
            } else if (!emailRegex.test(email.value.trim())) {
                showError(email, 'Ingrese un correo válido (ejemplo@dominio.com).');
            } else {
                clearError(email);
            }
        });

        // Limpiar error al comenzar a escribir
        email.addEventListener('input', function() {
            if (email.classList.contains('is-invalid') && email.value.trim()) {
                const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
                if (emailRegex.test(email.value.trim())) {
                    clearError(email);
                }
            }
        });

        // Validación de contraseña en tiempo real
        password.addEventListener('blur', function() {
            if (!password.value) {
                showError(password, 'La contraseña es obligatoria.');
            } else if (password.value.length < 6) {
                showError(password, 'La contraseña debe tener al menos 6 caracteres.');
            } else {
                clearError(password);
            }
        });

        // Limpiar error al comenzar a escribir
        password.addEventListener('input', function() {
            if (password.classList.contains('is-invalid') && password.value.length >= 6) {
                clearError(password);
            }
        });

        // Validación final al enviar el formulario
        loginForm.addEventListener('submit', function(e) {
            let valid = true;
            
            // Validar email
            const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
            if (!email.value.trim()) {
                showError(email, 'El correo electrónico es obligatorio.');
                valid = false;
            } else if (!emailRegex.test(email.value.trim())) {
                showError(email, 'Ingrese un correo válido (ejemplo@dominio.com).');
                valid = false;
            }
            
            // Validar contraseña
            if (!password.value) {
                showError(password, 'La contraseña es obligatoria.');
                valid = false;
            } else if (password.value.length < 6) {
                showError(password, 'La contraseña debe tener al menos 6 caracteres.');
                valid = false;
            }
            
            if (!valid) e.preventDefault();
        });
    }
});

// Validación en tiempo real para registro
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const username = document.getElementById('registerUsername');
    const email = document.getElementById('registerEmail');
    const password = document.getElementById('registerPassword');
    const confirmPassword = document.getElementById('registerConfirmPassword');

    if (!registerForm || !username || !email || !password || !confirmPassword) {
        return; // Si no están todos los elementos, salir
    }

    // Validación de nombre de usuario
    function validateUsername() {
        const usernameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,}$/;
        if (!username.value.trim()) {
            showError(username, 'El nombre de usuario es obligatorio.');
            return false;
        } else if (!usernameRegex.test(username.value.trim())) {
            showError(username, 'El nombre debe tener al menos 3 letras y no contener números ni caracteres especiales.');
            return false;
        } else {
            clearError(username);
            return true;
        }
    }

    // Validación de email
    function validateEmail() {
        const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
        if (!email.value.trim()) {
            showError(email, 'El correo electrónico es obligatorio.');
            return false;
        } else if (!emailRegex.test(email.value.trim())) {
            showError(email, 'Ingrese un correo válido (ejemplo@dominio.com).');
            return false;
        } else {
            clearError(email);
            return true;
        }
    }

    // Validación de contraseña
    function validatePassword() {
        if (!password.value) {
            showError(password, 'La contraseña es obligatoria.');
            return false;
        } else if (password.value.length < 6) {
            showError(password, 'La contraseña debe tener al menos 6 caracteres.');
            return false;
        } else {
            clearError(password);
            // Si hay texto en confirmar contraseña, validarlo también
            if (confirmPassword.value) {
                validateConfirmPassword();
            }
            return true;
        }
    }

    // Validación de confirmación de contraseña
    function validateConfirmPassword() {
        if (!confirmPassword.value) {
            showError(confirmPassword, 'Debe confirmar la contraseña.');
            return false;
        } else if (confirmPassword.value !== password.value) {
            showError(confirmPassword, 'Las contraseñas no coinciden.');
            return false;
        } else if (confirmPassword.value.length < 6) {
            showError(confirmPassword, 'La contraseña debe tener al menos 6 caracteres.');
            return false;
        } else {
            clearError(confirmPassword);
            return true;
        }
    }

    // Event listeners para validación en tiempo real
    username.addEventListener('blur', validateUsername);
    username.addEventListener('input', function() {
        if (username.classList.contains('is-invalid') && username.value.trim().length >= 3) {
            validateUsername();
        }
    });

    email.addEventListener('blur', validateEmail);
    email.addEventListener('input', function() {
        if (email.classList.contains('is-invalid') && email.value.trim()) {
            validateEmail();
        }
    });

    password.addEventListener('blur', validatePassword);
    password.addEventListener('input', function() {
        if (password.classList.contains('is-invalid') && password.value.length >= 6) {
            validatePassword();
        }
    });

    confirmPassword.addEventListener('blur', validateConfirmPassword);
    confirmPassword.addEventListener('input', function() {
        if (confirmPassword.classList.contains('is-invalid') && confirmPassword.value === password.value) {
            validateConfirmPassword();
        }
    });

    // Validación final al enviar el formulario
    registerForm.addEventListener('submit', function(e) {
        let valid = true;
        
        if (!validateUsername()) valid = false;
        if (!validateEmail()) valid = false;
        if (!validatePassword()) valid = false;
        if (!validateConfirmPassword()) valid = false;
        
        if (!valid) e.preventDefault();
    });
});

// main.js - Funciones de validación en tiempo real con tooltips

// Función para mostrar errores con tooltip
function showError(element, message) {
    element.classList.add('is-invalid');
    element.classList.remove('is-valid');
    
    // Crear o actualizar tooltip
    if (element._tooltip) {
        element._tooltip.dispose();
    }
    
    element.setAttribute('data-bs-toggle', 'tooltip');
    element.setAttribute('data-bs-placement', 'top');
    element.setAttribute('title', message);
    
    // Inicializar tooltip con Bootstrap
    element._tooltip = new bootstrap.Tooltip(element, {
        trigger: 'manual',
        customClass: 'error-tooltip'
    });
    element._tooltip.show();
}

// Función para mostrar éxito
function showSuccess(element) {
    element.classList.add('is-valid');
    element.classList.remove('is-invalid');
    
    // Remover tooltip de error
    if (element._tooltip) {
        element._tooltip.dispose();
        element._tooltip = null;
    }
    element.removeAttribute('data-bs-toggle');
    element.removeAttribute('data-bs-placement');
    element.removeAttribute('title');
}

// Función para limpiar validación
function clearValidation(element) {
    element.classList.remove('is-valid', 'is-invalid');
    
    // Remover tooltip
    if (element._tooltip) {
        element._tooltip.dispose();
        element._tooltip = null;
    }
    element.removeAttribute('data-bs-toggle');
    element.removeAttribute('data-bs-placement');
    element.removeAttribute('title');
}

// VALIDACIONES PARA LOGIN MODAL
function validateLoginEmail() {
    const emailInput = document.getElementById('email');
    if (!emailInput) return;

    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        
        if (email === '') {
            showError(this, 'El correo electrónico no puede estar vacío.');
            return false;
        }
        
        // Validar formato de email (debe tener @ y punto)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(this, 'El correo debe tener un formato válido (ejemplo@dominio.com).');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Limpiar validación al empezar a escribir
    emailInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });
}

// VALIDACIONES PARA REGISTER MODAL
function validateRegisterName() {
    const nameInput = document.getElementById('registerName');
    if (!nameInput) return;

    nameInput.addEventListener('blur', function() {
        const name = this.value.trim();
        
        if (name === '') {
            showError(this, 'El nombre completo no puede estar vacío.');
            return false;
        }
        
        // Validar que solo contenga letras y espacios
        const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!nameRegex.test(name)) {
            showError(this, 'El nombre solo puede contener letras y espacios.');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Validación en tiempo real mientras escribe
    nameInput.addEventListener('input', function() {
        const name = this.value;
        const nameRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
        
        if (!nameRegex.test(name)) {
            // Remover caracteres no válidos
            this.value = name.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        }
        
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });
}

function validateRegisterEmail() {
    const emailInput = document.getElementById('registerEmail');
    if (!emailInput) return;

    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        
        if (email === '') {
            showError(this, 'El correo electrónico no puede estar vacío.');
            return false;
        }
        
        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(this, 'El correo debe tener un formato válido (ejemplo@dominio.com).');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Limpiar validación al empezar a escribir
    emailInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });
}

function validateRegisterPassword() {
    const passwordInput = document.getElementById('registerPassword');
    if (!passwordInput) return;

    passwordInput.addEventListener('blur', function() {
        const password = this.value;
        
        if (password === '') {
            showError(this, 'La contraseña no puede estar vacía.');
            return false;
        }
        
        // Validar longitud mínima
        if (password.length < 8) {
            showError(this, 'La contraseña debe tener al menos 8 caracteres.');
            return false;
        }
        
        // Validar que tenga al menos un símbolo permitido (. , - _ @)
        const symbolRegex = /[.,\-_@]/;
        if (!symbolRegex.test(password)) {
            showError(this, 'La contraseña debe contener al menos uno de estos símbolos: . , - _ @');
            return false;
        }
        
        // Validar que no tenga caracteres especiales no permitidos
        const invalidCharsRegex = /[!#$%^&*()+=\[\]{}|\\:";'<>?/~`]/;
        if (invalidCharsRegex.test(password)) {
            showError(this, 'La contraseña solo puede contener letras, números y los símbolos: . , - _ @');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Validación en tiempo real para caracteres no permitidos
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const invalidCharsRegex = /[!#$%^&*()+=\[\]{}|\\:";'<>?/~`]/;
        
        if (invalidCharsRegex.test(password)) {
            // Remover caracteres no válidos
            this.value = password.replace(/[!#$%^&*()+=\[\]{}|\\:";'<>?/~`]/g, '');
        }
        
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });
}

function validateConfirmPassword() {
    const confirmPasswordInput = document.getElementById('registerConfirmPassword');
    const passwordInput = document.getElementById('registerPassword');
    if (!confirmPasswordInput || !passwordInput) return;

    confirmPasswordInput.addEventListener('blur', function() {
        const confirmPassword = this.value;
        const password = passwordInput.value;
        
        if (confirmPassword === '') {
            showError(this, 'Debe confirmar la contraseña.');
            return false;
        }
        
        if (confirmPassword !== password) {
            showError(this, 'Las contraseñas no coinciden.');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Limpiar validación al empezar a escribir
    confirmPasswordInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });

    // También validar cuando cambie la contraseña original
    passwordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value !== '' && confirmPasswordInput.value !== this.value) {
            confirmPasswordInput.dispatchEvent(new Event('blur'));
        }
    });
}

function validateRegisterAddress() {
    const addressInput = document.getElementById('registerAddress');
    if (!addressInput) return;

    addressInput.addEventListener('blur', function() {
        const address = this.value.trim();
        
        if (address === '') {
            showError(this, 'La dirección no puede estar vacía.');
            return false;
        }
        
        showSuccess(this);
        return true;
    });

    // Limpiar validación al empezar a escribir
    addressInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            clearValidation(this);
        }
    });
}

// Función para inicializar todas las validaciones
function initializeValidations() {
    // Validaciones para login modal
    validateLoginEmail();
    
    // Validaciones para register modal
    validateRegisterName();
    validateRegisterEmail();
    validateRegisterPassword();
    validateConfirmPassword();
    validateRegisterAddress();
}

// Validación final antes del envío del formulario de registro
function validateRegisterForm() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Obtener todos los campos
        const name = document.getElementById('registerName');
        const email = document.getElementById('registerEmail');
        const password = document.getElementById('registerPassword');
        const confirmPassword = document.getElementById('registerConfirmPassword');
        const address = document.getElementById('registerAddress');
        
        // Disparar validaciones para todos los campos
        const fields = [name, email, password, confirmPassword, address];
        
        fields.forEach(field => {
            if (field) {
                field.dispatchEvent(new Event('blur'));
                if (field.classList.contains('is-invalid')) {
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            
            // Mostrar alerta con tooltip
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
            alertDiv.innerHTML = `
                <strong>Error:</strong> Por favor, corrija los errores en el formulario antes de continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-remover la alerta después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    });
}

// Validación final antes del envío del formulario de login
function validateLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const email = document.getElementById('loginEmail');
        
        if (email) {
            email.dispatchEvent(new Event('blur'));
            if (email.classList.contains('is-invalid')) {
                e.preventDefault();
                
                // Mostrar alerta
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                alertDiv.innerHTML = `
                    <strong>Error:</strong> Por favor, ingrese un correo electrónico válido.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }
    });
}

// Limpiar validaciones cuando se abran los modales
function setupModalCleanup() {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (loginModal) {
        loginModal.addEventListener('show.bs.modal', function() {
            // Limpiar campos del login
            const email = document.getElementById('loginEmail');
            const password = document.getElementById('loginPassword');
            
            if (email) {
                clearValidation(email);
                email.value = '';
            }
            if (password) {
                clearValidation(password);
                password.value = '';
            }
        });
    }
    
    if (registerModal) {
        registerModal.addEventListener('show.bs.modal', function() {
            // Limpiar campos del register
            const fields = ['registerName', 'registerEmail', 'registerPassword', 'registerConfirmPassword', 'registerAddress'];
            
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    clearValidation(field);
                    field.value = '';
                }
            });
        });
    }
}

// CSS personalizado para tooltips de error
function addCustomCSS() {
    const style = document.createElement('style');
    style.textContent = `
        .error-tooltip .tooltip-inner {
            background-color: #dc3545;
            color: white;
            font-size: 12px;
            max-width: 250px;
        }
        .error-tooltip .tooltip-arrow::before {
            border-top-color: #dc3545;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-control.is-valid {
            border-color: #198754;
        }
    `;
    document.head.appendChild(style);
}

// Inicializar cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    addCustomCSS();
    initializeValidations();
    validateRegisterForm();
    validateLoginForm();
    setupModalCleanup();
});