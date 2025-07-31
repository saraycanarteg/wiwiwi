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
  function obtenerImagenProducto(nombre) {
    const productos = {
      "Bruschetta Gourmet": "assets/img/menu/menu-item-1.png",
      "Tabla de Quesos": "assets/img/menu/menu-item-2.png",
      "Nachos Diamonds": "assets/img/menu/menu-item-3.png",
      "Alitas BBQ": "assets/img/menu/menu-item-4.png",
      "Ceviche Fresco": "assets/img/menu/menu-item-5.png",
      "Rollitos Primavera": "assets/img/menu/menu-item-6.png",
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

  function actualizarTotal() {
    const total = (cantidad * precioUnitario).toFixed(2);
    document.getElementById('modalCarritoTotal').textContent = 'Total: $' + total;
  }

  window.agregarAlCarrito = function(nombre, precio) {
    cantidad = 1;
    precioUnitario = parseFloat(precio);
    document.getElementById('modalCarritoNombre').textContent = nombre;
    document.getElementById('modalCarritoPrecio').textContent = '$' + precioUnitario.toFixed(2) + ' c/u';
    document.getElementById('modalCarritoImg').src = obtenerImagenProducto(nombre);
    document.getElementById('modalCarritoCantidad').textContent = cantidad;
    document.getElementById('modalCarritoInstrucciones').value = '';
    document.getElementById('modalCarrito').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    document.getElementById('btnAgregarAlCarrito').dataset.nombre = nombre;
    document.getElementById('btnAgregarAlCarrito').dataset.precio = precioUnitario;
    actualizarTotal();
  };

  document.getElementById('cerrarModalCarrito').onclick = function() {
    document.getElementById('modalCarrito').style.display = 'none';
    document.body.style.overflow = '';
  };

  document.getElementById('btnMas').onclick = function() {
    if (cantidad < 30) {
      cantidad++;
      document.getElementById('modalCarritoCantidad').textContent = cantidad;
      actualizarTotal();
    }
  };
  document.getElementById('btnMenos').onclick = function() {
    if (cantidad > 1) {
      cantidad--;
      document.getElementById('modalCarritoCantidad').textContent = cantidad;
      actualizarTotal();
    }
  };

  document.getElementById('btnAgregarAlCarrito').onclick = function() {
    const nombre = this.dataset.nombre;
    const precio = parseFloat(this.dataset.precio);
    const instrucciones = document.getElementById('modalCarritoInstrucciones').value;
    const item = { nombre, precio, cantidad, instrucciones, imagen: obtenerImagenProducto(nombre) };

    let carrito = JSON.parse(localStorage.getItem('carritoYummy') || '[]');
    const idx = carrito.findIndex(p => p.nombre === nombre && p.instrucciones === instrucciones);
    if (idx >= 0) {
      carrito[idx].cantidad += cantidad;
    } else {
      carrito.push(item);
    }
    localStorage.setItem('carritoYummy', JSON.stringify(carrito));

    document.getElementById('modalCarrito').style.display = 'none';
    document.body.style.overflow = '';
    cantidad = 1;
    alert('¡Producto agregado al carrito!');
  };
});