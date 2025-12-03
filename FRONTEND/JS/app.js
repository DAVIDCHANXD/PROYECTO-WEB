// FRONTEND/JS/app.js
// Cuando cargue la página, intentamos llenar #lista-animales
document.addEventListener('DOMContentLoaded', () => {
  cargarAnimales();
});

// Mapa simple de tipos por ID (si tu BD usa id numérico)
const mapTipos = {
  1: 'Perro',
  2: 'Gato',
  3: 'Otros'
};

function cargarAnimales(contenedor) {
  // Si no me mandan contenedor, lo busco yo
  contenedor = contenedor || document.getElementById('lista-animales');
  if (!contenedor) return;

  contenedor.innerHTML =
    '<div class="col-12 text-center">' +
      '<div class="spinner-border" role="status"></div>' +
      '<p class="mt-2">Cargando animales...</p>' +
    '</div>';

  fetch(API_ANIMALES_URL + '?modo=json')
    .then(res => {
      if (!res.ok) {
        throw new Error('HTTP ' + res.status);
      }
      return res.json();
    })
    .then(data => {
      // Permitimos dos formatos:
      //  a) { ok:true, animales:[...] }
      //  b) [ ...animales... ] directo
      let animales = [];

      if (Array.isArray(data)) {
        animales = data;
      } else if (data && Array.isArray(data.animales)) {
        if (data.ok === false) {
          throw new Error(data.error || 'Error en la respuesta del servidor');
        }
        animales = data.animales;
      } else {
        throw new Error('Formato de respuesta no válido');
      }

      contenedor.innerHTML = '';

      if (!animales.length) {
        contenedor.innerHTML =
          '<div class="col-12">' +
            '<div class="alert alert-info">No hay animales disponibles por ahora.</div>' +
          '</div>';
        return;
      }

      animales.forEach(a => {
        contenedor.appendChild(crearCardAnimal(a));
      });
    })
    .catch(err => {
      console.error(err);
      contenedor.innerHTML =
        '<div class="col-12">' +
          '<div class="alert alert-danger">Error al cargar los animales: '
          + (err.message || '') +
          '</div>' +
        '</div>';
    });
}

function crearCardAnimal(animal) {
  const col  = document.createElement('div');
  col.className = 'col-sm-6 col-lg-4';

  const card = document.createElement('div');
  card.className = 'card h-100 shadow-sm border-0';

  // --- FOTO ---
  // Soporta "ruta_foto" o "foto" como nombre de campo
  const foto =
    (animal.ruta_foto && animal.ruta_foto.trim() !== '') ? animal.ruta_foto :
    (animal.foto && animal.foto.trim() !== '') ? animal.foto :
    'https://via.placeholder.com/600x400?text=En+adopcion';

  const img = document.createElement('img');
  img.src = foto;
  img.alt = `Foto de ${animal.nombre || 'Animal'}`;
  img.className = 'card-img-top';
  card.appendChild(img);

  const body = document.createElement('div');
  body.className = 'card-body';

  // --- TÍTULO (nombre) ---
  const title = document.createElement('h5');
  title.className = 'card-title';
  title.textContent = animal.nombre || 'Sin nombre';

  // --- ESPECIE / TIPO ---
  // Puede venir como:
  //  - id_tipo (numérico)
  //  - tipo
  //  - especie
  let especieTexto = 'Otros';

  if (animal.id_tipo) {
    const tipoId = Number(animal.id_tipo || 0);
    especieTexto = mapTipos[tipoId] || 'Otros';
  } else if (animal.tipo) {
    especieTexto = String(animal.tipo).trim();
  } else if (animal.especie) {
    especieTexto = String(animal.especie).trim();
  }

  const raza = (animal.raza || '').trim();
  const subtitle = document.createElement('p');
  subtitle.className = 'card-subtitle mb-2 text-muted small';
  subtitle.textContent = raza ? `${especieTexto} · ${raza}` : especieTexto;

  // --- DESCRIPCIÓN ---
  const desc = document.createElement('p');
  desc.className = 'card-text small';
  desc.textContent = animal.descripcion || 'Sin descripción';

  // --- ESTADO DE SALUD (opcional) ---
  const estado = (animal.estado_salud || animal.estado || 'Saludable').trim();
  const badge = document.createElement('span');
  badge.className = 'badge bg-success';
  badge.textContent = estado;

  body.appendChild(title);
  body.appendChild(subtitle);
  body.appendChild(desc);
  body.appendChild(badge);

  const footer = document.createElement('div');
  footer.className = 'card-footer bg-transparent border-0';

  const btn = document.createElement('a');
  btn.href = '#contacto';
  btn.className = 'btn btn-primary w-100';
  btn.textContent = 'Quiero adoptar';

  footer.appendChild(btn);
  card.appendChild(body);
  card.appendChild(footer);
  col.appendChild(card);

  // Atributo para filtros (Perros / Gatos / Otros)
  const especieLower = especieTexto.toLowerCase();
  let especieFiltro = 'otros';
  if (especieLower.startsWith('perro')) especieFiltro = 'perro';
  else if (especieLower.startsWith('gato')) especieFiltro = 'gato';
  col.setAttribute('data-especie', especieFiltro);

  return col;
}
