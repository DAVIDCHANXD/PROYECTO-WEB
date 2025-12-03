// FRONTEND/JS/app.js

const mapTipos = {
  1: 'Perro',
  2: 'Gato',
  3: 'Otros'
};

/**
 * 
 * @param {Object} animal - Objeto con los datos del animal.
 * @returns {HTMLElement} - Columna con la card lista para agregar al DOM.
 */
function crearCardAnimal(animal) {
  const col  = document.createElement('div');
  col.className = 'col-sm-6 col-lg-4';

  const card = document.createElement('div');
  card.className = 'card h-100 shadow-sm border-0';

  const foto = (animal.ruta_foto && animal.ruta_foto.trim() !== '')
    ? animal.ruta_foto
    : 'https://via.placeholder.com/600x400?text=En+adopcion';

  const img = document.createElement('img');
  img.src = foto;
  img.alt = `Foto de ${animal.nombre || 'Animal'}`;
  img.className = 'card-img-top';
  card.appendChild(img);

  const body = document.createElement('div');
  body.className = 'card-body';

  const title = document.createElement('h5');
  title.className = 'card-title';
  title.textContent = animal.nombre || 'Sin nombre';

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

  const desc = document.createElement('p');
  desc.className = 'card-text small';
  desc.textContent = animal.descripcion || 'Sin descripción';

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

  const especieLower = especieTexto.toLowerCase();
  let especieFiltro = 'otros';
  if (especieLower.startsWith('perro')) especieFiltro = 'perro';
  else if (especieLower.startsWith('gato')) especieFiltro = 'gato';
  col.setAttribute('data-especie', especieFiltro);

  return col;
}
