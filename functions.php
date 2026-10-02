<?php

function neon_fiesta_cargar_estilos() {
    wp_enqueue_style(
        'neon-fiesta-estilos',
        get_stylesheet_uri(),
        array(),
        '1.0'
    );
}

add_action(
    'wp_enqueue_scripts',
    'neon_fiesta_cargar_estilos'
);

<?php

function neon_fiesta_cargar_estilos() {
    wp_enqueue_style(
        'neon-fiesta-estilos',
        get_stylesheet_uri(),
        array(),
        '1.0'
    );
}

add_action(
    'wp_enqueue_scripts',
    'neon_fiesta_cargar_estilos'
);