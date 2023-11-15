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

// Fungsi untuk menangani AJAX request
function save_hasil_questionnaire() {
    // Periksa apakah request adalah POST dan memiliki data yang diperlukan
    if (isset($_POST['formData'])) {
        // Lakukan logika penyimpanan atau manipulasi data sesuai kebutuhan Anda
        $formData = $_POST['formData'] ?? 'data tidak di submit';

        // Simpan atau proses data sesuai kebutuhan

        // Contoh: Menyimpan data menggunakan metode update_post_meta
        // update_post_meta($formId, '_selected_value', $selectedValue);

        // Beri respons ke klien
        // echo 'Data berhasil disimpan';
        print_r($formData);
    } else {
        // Jika data tidak lengkap, beri respons error
        echo 'Error: Data tidak lengkap';
    }

    // Penting: Jangan lupa akhiri proses AJAX dengan exit
    exit();
}

// Daftarkan fungsi di WordPress untuk AJAX request
add_action('wp_ajax_update_hasil_questionnaire', 'save_hasil_questionnaire');
add_action('wp_ajax_nopriv_update_hasil_questionnaire', 'save_hasil_questionnaire');