<?php

/**
 * Masukkan semua function tambahan disini
 *
 * @link       {REPLACE_ME_URL}
 * @since      1.0.0
 *
 * @package    Custom_Plugin
 * @subpackage Custom_Plugin/includes
 */

// Pastikan ini dijalankan setelah WordPress selesai memuat plugin
add_action('admin_init', 'check_cmb2_plugin');

function check_cmb2_plugin() {
    // Periksa apakah plugin CMB2 terinstall
    if (!is_plugin_active('cmb2/init.php')) {
        // Tampilkan pesan alert kepada admin
        add_action('admin_notices', 'cmb2_missing_notice');
    }
}

function cmb2_missing_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Plugin CMB2 belum terinstall. Silakan instal dan aktifkan untuk menggunakan fitur ini. Pesan dari plugin <b>Custom Plugin</b> <small>by Websweetstudio.com</small>', 'custom-plugin'); ?></p>
    </div>
    <?php
}