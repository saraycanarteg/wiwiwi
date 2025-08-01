document.getElementById("registerForm").addEventListener("submit", function(event) {
    const pass1 = document.getElementById("registerPassword").value;
    const pass2 = document.getElementById("registerConfirmPassword").value;

    if (pass1 !== pass2) {
        event.preventDefault(); // Detiene el envío del formulario
        alert("⚠️ Las contraseñas no coinciden.");
    } else if (pass1.length < 6) {
        event.preventDefault();
        alert("⚠️ La contraseña debe tener al menos 6 caracteres.");
    }
});

        // Efecto suave para la navegación al hacer scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navegacion-principal');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(61, 75, 107, 0.95)';
            } else {
                navbar.style.background = 'rgba(61, 75, 107, 0.95)';
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