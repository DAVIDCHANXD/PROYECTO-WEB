// FRONTEND/JS/app.js

    document.addEventListener('DOMContentLoaded', () => {
    cargarAnimales();
});

    function cargarAnimales() {
    const contenedor = document.getElementById('lista-animales');
    if (!contenedor) return;

    contenedor.innerHTML = '<p>Cargando animales...</p>';

    fetch('BACKEND/EVENTS/listar_animales.php')
    .then(res => res.json())
    .then(animales => {
    contenedor.innerHTML = '';

    if (!animales || animales.length === 0) {
        contenedor.innerHTML = '<p>No hay animales disponibles por ahora.</p>';
        return;
    }

    const row = document.createElement('div');
    row.className = 'row';

    animales.forEach(a => {
        row.appendChild(crearCardAnimal(a));
    });

    contenedor.appendChild(row);
    })
    .catch(err => {
    console.error(err);
    contenedor.innerHTML =
        '<p class="text-danger">Error al cargar los animales. Intenta más tarde.</p>';
    });
}

    function crearCardAnimal(animal) {
    const col = document.createElement('div');
    col.className = 'col-md-4 mb-4';

    const card = document.createElement('div');
    card.className = 'card h-100 shadow-sm card-animal';

    if (animal.ruta_foto) {
    const img = document.createElement('img');
    img.src = animal.ruta_foto;
    img.alt = `Foto de ${animal.nombre}`;
    img.className = 'card-img-top';
    card.appendChild(img);
    }

    const body = document.createElement('div');
    body.className = 'card-body';

    const title = document.createElement('h5');
    title.className = 'card-title';
    title.textContent = animal.nombre;

    const subtitle = document.createElement('p');
    subtitle.className = 'card-subtitle mb-2 text-muted';
    subtitle.textContent = `${animal.especie} · ${animal.raza}`;

    const desc = document.createElement('p');
    desc.className = 'card-text';
    desc.textContent = animal.descripcion || 'Sin descripción';

    const badge = document.createElement('span');
    badge.className = 'badge bg-success';
    badge.textContent = animal.estado_salud || 'Saludable';

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

    return col;
}
