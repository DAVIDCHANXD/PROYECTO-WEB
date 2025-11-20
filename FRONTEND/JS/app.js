// FRONTEND/JS/app.js

// Cuando la página termina de cargar, configuramos los filtros
document.addEventListener('DOMContentLoaded', () => {
  const botones = document.querySelectorAll('#filtros-animales .nav-link');
  const cards   = document.querySelectorAll('#lista-animales [data-especie]');

  if (!botones.length || !cards.length) {
    // No estamos en la página de animales
    return;
  }

  botones.forEach(btn => {
    btn.addEventListener('click', () => {
      const filtro = btn.getAttribute('data-filter');

      // Marcar botón activo
      botones.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Mostrar / ocultar tarjetas según el filtro
      cards.forEach(card => {
        const especie = card.getAttribute('data-especie');

        if (filtro === 'todos' || especie === filtro) {
          card.classList.remove('d-none');
        } else {
          card.classList.add('d-none');
        }
      });
    });
  });
});

// Función para el botón "Actualizar lista"
function cargarAnimales() {
  // Simplemente recarga la página para volver a pedir los datos al servidor
  window.location.reload();
}
