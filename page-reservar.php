<?php
$mesas = array(
    'Verde',
    'Fucsia',
    'Cyan',
    'Amarillo',
    'Púrpura',
    'Aurora',
    'Galaxy',
    'Negro'
);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
    <a href="<?php echo esc_url(home_url('/')); ?>">
        NEON COCREADORES
    </a>

    <nav>
        <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
        <a href="<?php echo esc_url(home_url('/reservar/')); ?>">Reservar</a>
    </nav>
</header>

<main class="reservation-page">

    <p class="eyebrow">CONFIRMA TU LUGAR</p>

    <h1>
        RESERVA TU<br>
        <span>ASIENTO</span>
    </h1>

    <p class="reservation-intro">
        8 mesas · 6 lugares cada una · 48 personas
    </p>

    <section class="tables-grid">

        <?php foreach ($mesas as $mesa) : ?>

            <button
                class="table-card table-<?php echo esc_attr(strtolower($mesa)); ?>"
                data-table="<?php echo esc_attr($mesa); ?>"
            >
                <strong>Mesa <?php echo esc_html($mesa); ?></strong>

                <small>6/6 lugares</small>

                <span class="seat-dots">
                    ○ ○ ○ ○ ○ ○
                </span>
            </button>

        <?php endforeach; ?>

    </section>

    <section id="seat-panel" class="seat-panel" hidden>
        <button id="back-button" class="back-button">
            ← Volver al mapa
        </button>

        <p class="eyebrow">SELECCIONA TU LUGAR</p>

        <h2 id="selected-table-title">
            Mesa
        </h2>

        <p>
            Elige uno de los 6 lugares disponibles.
        </p>

        <div id="seat-list" class="seat-list"></div>

        <form id="reservation-form" class="reservation-form" hidden>
            <h3>Completa tus datos</h3>

            <label>
                Nombre completo
                <input type="text" id="reservation-name" required placeholder="Tu nombre">
            </label>

            <label>
                Correo
                <input type="email"  id="reservation-email" required placeholder="tu@correo.com">
            </label>

            <label>
                Área o departamento
                <input type="text"  id="reservation-area"  required placeholder="Tu área">
            </label>

            <button type="submit" class="submit-button">
                Confirmar reserva
            </button>
        </form>
    </section>

</main>

<script>
const ajaxUrl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
const nonce = <?php echo wp_json_encode(wp_create_nonce('neon_reserva')); ?>;

const tableButtons = document.querySelectorAll('.table-card');
const seatPanel = document.querySelector('#seat-panel');
const seatList = document.querySelector('#seat-list');
const selectedTableTitle = document.querySelector('#selected-table-title');
const reservationForm = document.querySelector('#reservation-form');
const backButton = document.querySelector('#back-button');

let selectedTable = '';
let selectedSeat = '';

function actualizarTarjetaMesa(mesa, reservados) {
    const tarjeta = Array.from(tableButtons).find(function(button) {
        return button.dataset.table === mesa;
    });

    if (!tarjeta) {
        return;
    }

    const disponibles = 6 - reservados.length;
    const contador = tarjeta.querySelector('small');
    const puntos = tarjeta.querySelector('.seat-dots');

    contador.textContent = disponibles + '/6 lugares';

    puntos.textContent = Array.from(
        { length: 6 },
        (_, index) => reservados.includes(index + 1) ? '●' : '○'
    ).join(' ');
}

function cargarDisponibilidad() {
    tableButtons.forEach(function(button) {
        const mesa = button.dataset.table;

        const data = new URLSearchParams({
            action: 'neon_obtener_reservas',
            mesa: mesa,
            nonce: nonce
        });

        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                actualizarTarjetaMesa(mesa, result.data);
            }
        });
    });
}

cargarDisponibilidad();

tableButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        selectedTable = button.dataset.table;
        selectedTableTitle.textContent = 'Mesa ' + selectedTable;

        seatPanel.hidden = false;
        reservationForm.hidden = true;
        seatList.innerHTML = 'Cargando lugares...';

        const data = new URLSearchParams({
            action: 'neon_obtener_reservas',
            mesa: selectedTable,
            nonce: nonce
        });

        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            const reservados = result.success ? result.data : [];
            actualizarTarjetaMesa(selectedTable, reservados);

            seatList.innerHTML = '';

            for (let number = 1; number <= 6; number++) {
                const seat = document.createElement('button');

                seat.type = 'button';
                seat.className = 'seat-button';
                seat.textContent = reservados.includes(number)
                    ? 'Ocupado'
                    : 'Lugar ' + number;

                if (reservados.includes(number)) {
                    seat.disabled = true;
                    seat.classList.add('reserved');
                }

                seat.addEventListener('click', function() {
                    selectedSeat = number;

                    document
                        .querySelectorAll('.seat-button')
                        .forEach(item => item.classList.remove('selected'));

                    seat.classList.add('selected');
                    reservationForm.hidden = false;
                });

                seatList.appendChild(seat);
            }
        });

        seatPanel.scrollIntoView({
            behavior: 'smooth'
        });
    });
});

backButton.addEventListener('click', function() {
    seatPanel.hidden = true;
    reservationForm.hidden = true;
});

reservationForm.addEventListener('submit', function(event) {
    event.preventDefault();

    const data = new URLSearchParams({
        action: 'neon_guardar_reserva',
        mesa: selectedTable,
        asiento: selectedSeat,
        nombre: document.querySelector('#reservation-name').value,
        correo: document.querySelector('#reservation-email').value,
        area: document.querySelector('#reservation-area').value,
        nonce: nonce
    });

    fetch(ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Reserva guardada correctamente.');
            window.location.reload();
        } else {
            alert(result.data);
        }
    });
});
</script>

<?php wp_footer(); ?>

</body>
</html>