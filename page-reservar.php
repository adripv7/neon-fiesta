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
                <input type="text" required placeholder="Tu nombre">
            </label>

            <label>
                Correo
                <input type="email" required placeholder="tu@correo.com">
            </label>

            <label>
                Área o departamento
                <input type="text" required placeholder="Tu área">
            </label>

            <button type="submit" class="submit-button">
                Confirmar reserva
            </button>
        </form>
    </section>

</main>

<script>
const tableButtons = document.querySelectorAll('.table-card');
const seatPanel = document.querySelector('#seat-panel');
const seatList = document.querySelector('#seat-list');
const selectedTableTitle = document.querySelector('#selected-table-title');
const reservationForm = document.querySelector('#reservation-form');
const backButton = document.querySelector('#back-button');

let selectedTable = '';
let selectedSeat = '';

tableButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        selectedTable = button.dataset.table;
        selectedTableTitle.textContent = 'Mesa ' + selectedTable;

        seatList.innerHTML = '';
        reservationForm.hidden = true;

        for (let number = 1; number <= 6; number++) {
            const seat = document.createElement('button');

            seat.type = 'button';
            seat.className = 'seat-button';
            seat.textContent = 'Lugar ' + number;

            seat.addEventListener('click', function() {
                selectedSeat = number;

                document.querySelectorAll('.seat-button').forEach(function(item) {
                    item.classList.remove('selected');
                });

                seat.classList.add('selected');
                reservationForm.hidden = false;
            });

            seatList.appendChild(seat);
        }

        seatPanel.hidden = false;
        seatPanel.scrollIntoView({ behavior: 'smooth' });
    });
});

backButton.addEventListener('click', function() {
    seatPanel.hidden = true;
    reservationForm.hidden = true;
});

reservationForm.addEventListener('submit', function(event) {
    event.preventDefault();

    alert(
        'Reserva seleccionada: Mesa ' +
        selectedTable +
        ', Lugar ' +
        selectedSeat
    );
});
</script>

<?php wp_footer(); ?>

</body>
</html>