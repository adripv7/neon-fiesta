<?php
/**
 * Plugin Name: Neon Fiesta Reservas
 * Description: Sistema de reservas para mesas y lugares.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function neon_fiesta_crear_tabla() {
    global $wpdb;

    $tabla = $wpdb->prefix . 'neon_reservas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        mesa varchar(50) NOT NULL,
        asiento int(11) NOT NULL,
        nombre varchar(150) NOT NULL,
        correo varchar(150) NOT NULL,
        area varchar(150) NOT NULL,
        creado_en datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY mesa_asiento (mesa, asiento)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}

register_activation_hook(
    __FILE__,
    'neon_fiesta_crear_tabla'
);