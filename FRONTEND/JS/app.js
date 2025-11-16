// Array en memoria (si quieres, luego lo cambias por peticiones a backend)
let items = [];

// Referencias al DOM
const form = document.getElementById('item-form');
const nombreInput = document.getElementById('nombre');
const descripcionInput = document.getElementById('descripcion');
const editIndexInput = document.getElementById('edit-index');
const btnGuardar = document.getElementById('btn-guardar');
const btnCancelar = document.getElementById('btn-cancelar');
const tbody = document.getElementById('items-tbody');
const emptyMsg = document.getElementById('empty-msg');

// Renderizar la tabla
function renderTabla() {
    tbody.innerHTML = '';

    if (items.length === 0) {
        emptyMsg.classList.remove('d-none');
        return;
    }

    emptyMsg.classList.add('d-none');

    items.forEach((item, index) => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.nombre}</td>
            <td>${item.descripcion || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-info me-1" data-action="edit" data-index="${index}">
                    Editar
                </button>
                <button class="btn btn-sm btn-outline-danger" data-action="delete" data-index="${index}">
                    Eliminar
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });
}

// Limpiar formulario y salir de modo edición
function resetForm() {
    form.reset();
    editIndexInput.value = '';
    btnGuardar.textContent = 'Guardar';
    btnGuardar.classList.remove('btn-primary');
    btnGuardar.classList.add('btn-success');
    btnCancelar.classList.add('d-none');
}

// Guardar (CREATE / UPDATE)
form.addEventListener('submit', function (e) {
    e.preventDefault();

    const nombre = nombreInput.value.trim();
    const descripcion = descripcionInput.value.trim();

    if (!nombre) return;

    const item = { nombre, descripcion };
    const editIndex = editIndexInput.value;

    if (editIndex === '') {
        // CREATE
        items.push(item);
    } else {
        // UPDATE
        items[parseInt(editIndex, 10)] = item;
    }

    renderTabla();
    resetForm();
});

// Cancelar edición
btnCancelar.addEventListener('click', function () {
    resetForm();
});

// Delegación de eventos para botones Editar / Eliminar
tbody.addEventListener('click', function (e) {
    const btn = e.target.closest('button');
    if (!btn) return;

    const action = btn.dataset.action;
    const index = parseInt(btn.dataset.index, 10);

    if (action === 'edit') {
        const item = items[index];
        nombreInput.value = item.nombre;
        descripcionInput.value = item.descripcion;

        editIndexInput.value = index;
        btnGuardar.textContent = 'Actualizar';
        btnGuardar.classList.remove('btn-success');
        btnGuardar.classList.add('btn-primary');
        btnCancelar.classList.remove('d-none');
    }

    if (action === 'delete') {
        if (confirm('¿Eliminar este elemento?')) {
            items.splice(index, 1);
            renderTabla();
            resetForm();
        }
    }
});

// Inicial
renderTabla();
