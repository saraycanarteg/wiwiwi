function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById('eye_' + fieldId.split('_')[1]);
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Función para limpiar el formulario de contraseñas
        function clearPasswordForm() {
            $('#formPassword')[0].reset();
            $('#password_strength').empty();
            $('#password_match').empty();
        }

        // Verificar fortaleza de contraseña
        function checkPasswordStrength(password) {
            const strength = $('#password_strength');
            let score = 0;
            let message = '';
            let className = '';

            if (password.length >= 6) score++;
            if (password.match(/[a-z]/)) score++;
            if (password.match(/[A-Z]/)) score++;
            if (password.match(/[0-9]/)) score++;
            if (password.match(/[^a-zA-Z0-9]/)) score++;

            switch(score) {
                case 0:
                case 1:
                    message = '<i class="fas fa-exclamation-triangle"></i> Muy débil';
                    className = 'text-danger';
                    break;
                case 2:
                    message = '<i class="fas fa-minus-circle"></i> Débil';
                    className = 'text-warning';
                    break;
                case 3:
                    message = '<i class="fas fa-check-circle"></i> Regular';
                    className = 'text-info';
                    break;
                case 4:
                    message = '<i class="fas fa-thumbs-up"></i> Buena';
                    className = 'text-success';
                    break;
                case 5:
                    message = '<i class="fas fa-star"></i> Excelente';
                    className = 'text-success font-weight-bold';
                    break;
            }

            strength.html(`<small class="${className}">${message}</small>`);
        }

        // Verificar coincidencia de contraseñas
        function checkPasswordMatch() {
            const nueva = $('#password_nueva').val();
            const confirmar = $('#password_confirmar').val();
            const match = $('#password_match');

            if (confirmar === '') {
                match.empty();
                return;
            }

            if (nueva === confirmar) {
                match.html('<small class="text-success"><i class="fas fa-check"></i> Las contraseñas coinciden</small>');
            } else {
                match.html('<small class="text-danger"><i class="fas fa-times"></i> Las contraseñas no coinciden</small>');
            }
        }

        $(document).ready(function() {
            // Verificar fortaleza en tiempo real
            $('#password_nueva').on('input', function() {
                checkPasswordStrength($(this).val());
            });

            // Verificar coincidencia en tiempo real
            $('#password_confirmar').on('input', checkPasswordMatch);
            $('#password_nueva').on('input', checkPasswordMatch);

            // Manejar el envío del formulario de cambio de contraseña
            $('#formPassword').on('submit', function(e) {
                e.preventDefault();
                
                // Validar que todos los campos estén llenos
                if (!$('#password_actual').val() || !$('#password_nueva').val() || !$('#password_confirmar').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor, completa todos los campos'
                    });
                    return;
                }
                
                // Validar que las contraseñas coincidan
                if ($('#password_nueva').val() !== $('#password_confirmar').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Las contraseñas nuevas no coinciden'
                    });
                    return;
                }
                
                // Validar longitud mínima de contraseña
                if ($('#password_nueva').val().length < 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Contraseña muy corta',
                        text: 'La nueva contraseña debe tener al menos 6 caracteres'
                    });
                    return;
                }

                // Deshabilitar el botón durante el proceso
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');

                $.ajax({
                    url: '../controles/ajax_perfil.php',
                    type: 'POST',
                    data: {
                        accion: 'cambiar_password',
                        password_actual: $('#password_actual').val(),
                        password_nueva: $('#password_nueva').val()
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: data.mensaje,
                                    confirmButtonColor: '#4e73df'
                                }).then(() => {
                                    $('#formPassword')[0].reset();
                                    $('#password_strength').empty();
                                    $('#password_match').empty();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.mensaje,
                                    confirmButtonColor: '#4e73df'
                                });
                            }
                        } catch (e) {
                            console.error('Error al procesar respuesta:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar la respuesta del servidor',
                                confirmButtonColor: '#4e73df'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            confirmButtonColor: '#4e73df'
                        });
                    },
                    complete: function() {
                        // Rehabilitar el botón
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });