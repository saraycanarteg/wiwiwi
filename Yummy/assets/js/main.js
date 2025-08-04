/**
* Template Name: Yummy
* Template URL: https://bootstrapmade.com/yummy-bootstrap-restaurant-website-template/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

})();
document.addEventListener('DOMContentLoaded', function() {
  // Función para obtener imagen del producto
  function obtenerImagenProducto(nombre) {
    const productos = {
      "Iced Coffee Pink": "assets/img/menu/bf1.png",
      "Limonada de Naranja Frutilla": "assets/img/menu/bf2.png",
      "Blue Lemonade": "assets/img/menu/bf3.png",
      "Milshake Barbie": "assets/img/menu/bf4.png",
      "Limonada de Café": "assets/img/menu/bf5.png",
      "Limonada de Fresa": "assets/img/menu/bf6.png",
      "Desayuno Americano": "assets/img/menu/menu-item-1.png",
      "Panqueques Deluxe": "assets/img/menu/menu-item-2.png",
      "Avocado Toast": "assets/img/menu/menu-item-3.png",
      "Omelette Gourmet": "assets/img/menu/menu-item-4.png",
      "Smoothie Bowl": "assets/img/menu/menu-item-5.png",
      "Croissant Relleno": "assets/img/menu/menu-item-6.png",
      "Hamburguesa Diamonds": "assets/img/menu/menu-item-1.png",
      "Ensalada César": "assets/img/menu/menu-item-2.png",
      "Pasta Alfredo": "assets/img/menu/menu-item-3.png",
      "Sándwich Club": "assets/img/menu/menu-item-4.png",
      "Tacos Mexicanos": "assets/img/menu/menu-item-5.png",
      "Sopa del Día": "assets/img/menu/menu-item-6.png",
      "Estafado de res": "assets/img/menu/menu-item-1.png",
      "Salmon de maracuya": "assets/img/menu/menu-item-2.png",
      "Pollo campestre": "assets/img/menu/menu-item-3.png",
      "Pasta pesto": "assets/img/menu/menu-item-4.png",
      "Costillas BBQ": "assets/img/menu/menu-item-5.png",
      "Risotto de champiñones": "assets/img/menu/menu-item-6.png"
    };
    return productos[nombre] || "assets/img/menu/menu-item-1.png";
  }

  let cantidad = 1;
  let precioUnitario = 0;

  // Referencias a elementos
  const modal = document.getElementById('modalCarrito');
  const btnCerrar = document.getElementById('cerrarModalCarrito');
  const btnMenos = document.getElementById('btnMenos');
  const btnMas = document.getElementById('btnMas');
  const cantidadDisplay = document.getElementById('modalCarritoCantidad');
  const totalDisplay = document.getElementById('modalCarritoTotal');
  const nombreProducto = document.getElementById('modalCarritoNombre');
  const precioProducto = document.getElementById('modalCarritoPrecio');
  const imagenProducto = document.getElementById('modalCarritoImg');
  const instruccionesProducto = document.getElementById('modalCarritoInstrucciones');
  const btnAgregar = document.getElementById('btnAgregarAlCarrito');

  // Función para actualizar cantidad y total
  function actualizarCantidad() {
    cantidadDisplay.textContent = cantidad;
    const total = (precioUnitario * cantidad).toFixed(2);
    totalDisplay.textContent = `$${total}`;
    
    // Deshabilitar/habilitar botones
    btnMenos.disabled = cantidad <= 1;
    btnMas.disabled = cantidad >= 30;
  }

  // Función para actualizar total (alias para compatibilidad)
  function actualizarTotal() {
    actualizarCantidad();
  }

  // Función global para abrir modal con datos del producto
  window.agregarAlCarrito = function(nombre, precio, imagen = null) {
    cantidad = 1;
    precioUnitario = parseFloat(precio);
    
    // Actualizar contenido del modal
    nombreProducto.textContent = nombre;
    precioProducto.textContent = `$${precioUnitario.toFixed(2)} c/u`;
    imagenProducto.src = imagen || obtenerImagenProducto(nombre);
    cantidadDisplay.textContent = cantidad;
    instruccionesProducto.value = '';
    
    // Guardar datos en el botón para uso posterior
    btnAgregar.dataset.nombre = nombre;
    btnAgregar.dataset.precio = precioUnitario;
    
    // Mostrar modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Actualizar total
    actualizarCantidad();
  };

  // Cerrar modal
  btnCerrar.onclick = function() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  };

  // Cerrar modal al hacer clic fuera
  modal.onclick = function(e) {
    if (e.target === modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  };

  // Cerrar modal con tecla Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.style.display === 'flex') {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  });

  // Aumentar cantidad
  btnMas.onclick = function() {
    if (cantidad < 30) {
      cantidad++;
      actualizarCantidad();
    }
  };

  // Disminuir cantidad
  btnMenos.onclick = function() {
    if (cantidad > 1) {
      cantidad--;
      actualizarCantidad();
    }
  };

  // Agregar al carrito
  btnAgregar.onclick = function() {
    const nombre = this.dataset.nombre;
    const precio = parseFloat(this.dataset.precio);
    const instrucciones = instruccionesProducto.value;
    const item = { 
      nombre, 
      precio, 
      cantidad, 
      instrucciones, 
      imagen: obtenerImagenProducto(nombre) 
    };

    let carrito = JSON.parse(localStorage.getItem('carritoYummy') || '[]');
    const idx = carrito.findIndex(p => p.nombre === nombre && p.instrucciones === instrucciones);
    
    if (idx >= 0) {
      carrito[idx].cantidad += cantidad;
    } else {
      carrito.push(item);
    }
    
    localStorage.setItem('carritoYummy', JSON.stringify(carrito));

    // Cerrar modal y resetear
    modal.style.display = 'none';
    document.body.style.overflow = '';
    cantidad = 1;
    
    // Mensaje de confirmación mejorado
    const totalItem = (precio * cantidad).toFixed(2);
    alert(`¡Producto agregado al carrito!\n${cantidad}x ${nombre}\nTotal: $${totalItem}`);
  };
});
      let tipoDireccion = 'manual';
        let metodoPago = 'tarjeta';

        function abrirFormularioPedido() {
            document.getElementById('modalPedido').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function cerrarFormulario() {
            document.getElementById('modalPedido').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function seleccionarTipoDireccion(tipo) {
            tipoDireccion = tipo;
            
            // Actualizar botones
            document.querySelectorAll('.direccion-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Mostrar/ocultar secciones
            document.getElementById('direccion-manual').classList.toggle('active', tipo === 'manual');
            document.getElementById('direccion-ubicacion').classList.toggle('active', tipo === 'ubicacion');
        }

        function seleccionarPago(tipo) {
            metodoPago = tipo;
            
            // Actualizar métodos de pago
            document.querySelectorAll('.payment-method').forEach(method => method.classList.remove('active'));
            event.target.classList.add('active');
            
            // Mostrar/ocultar detalles
            document.getElementById('tarjeta-details').classList.toggle('active', tipo === 'tarjeta');
            document.getElementById('efectivo-details').classList.toggle('active', tipo === 'efectivo');
        }

        function obtenerUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        alert(`Ubicación detectada:\nLatitud: ${lat.toFixed(6)}\nLongitud: ${lng.toFixed(6)}\n\n(En un proyecto real, esto se enviaría al servidor)`);
                    },
                    function(error) {
                        alert('No se pudo obtener la ubicación. Por favor, ingresa la dirección manualmente.');
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        }

        // Formatear número de tarjeta
        document.getElementById('numeroTarjeta').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Formatear vencimiento
        document.getElementById('vencimiento').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Validación del formulario
        document.getElementById('formularioPedido').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            
            // Validar campos obligatorios
            const campos = [
                { id: 'cedula', error: 'error-cedula', validacion: (val) => val.length === 10 && /^\d+$/.test(val) },
                { id: 'nombre', error: 'error-nombre', validacion: (val) => val.trim().length > 0 },
                { id: 'celular', error: 'error-celular', validacion: (val) => val.length === 10 && /^\d+$/.test(val) }
            ];

            if (tipoDireccion === 'manual') {
                campos.push({ id: 'direccion', error: 'error-direccion', validacion: (val) => val.trim().length > 0 });
            }

            if (metodoPago === 'tarjeta') {
                campos.push(
                    { id: 'numeroTarjeta', validacion: (val) => val.replace(/\s/g, '').length >= 16 },
                    { id: 'titularTarjeta', validacion: (val) => val.trim().length > 0 },
                    { id: 'vencimiento', validacion: (val) => val.length === 5 },
                    { id: 'cvv', validacion: (val) => val.length >= 3 }
                );
            }

            campos.forEach(campo => {
                const elemento = document.getElementById(campo.id);
                const errorElemento = campo.error ? document.getElementById(campo.error) : null;
                
                if (!campo.validacion(elemento.value)) {
                    elemento.classList.add('error');
                    if (errorElemento) errorElemento.style.display = 'block';
                    isValid = false;
                } else {
                    elemento.classList.remove('error');
                    if (errorElemento) errorElemento.style.display = 'none';
                }
            });

            if (isValid) {
                // Simular envío del pedido
                mostrarConfirmacion();
            }
        });

        function mostrarConfirmacion() {
            const modalContainer = document.querySelector('.modal-container');
            modalContainer.innerHTML = `
                <div class="success-animation">
                    <div class="success-icon">✅</div>
                    <h2 style="color: #27ae60; margin-bottom: 16px;">¡Pedido Confirmado!</h2>
                    <p style="margin-bottom: 20px;">Tu pedido ha sido recibido y está en proceso.</p>
                    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                        <strong>Número de pedido: #${Math.floor(Math.random() * 10000)}</strong><br>
                        <small>Tiempo estimado de entrega: 25-35 minutos</small>
                    </div>
                    <button class="submit-btn" onclick="cerrarFormulario()">Cerrar</button>
                </div>
            `;
        }

        // Cerrar con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarFormulario();
            }
        });

        // Cerrar al hacer clic fuera
        document.getElementById('modalPedido').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarFormulario();
            }
        });