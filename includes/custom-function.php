<?php

/**
 * Masukkan semua function tambahan disini
 *
 * @link       https://websweetstudio.com
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
    // Check if the request is a POST request and has the required data
    if (isset($_POST['formData']) && is_array($_POST['formData'])) {
        // Perform necessary data manipulation
        $formData = $_POST['formData'] ?? [];
        $id_questionnaire = key($formData); // Use key() to get the first key
        $id_member = get_current_user_id();

        // Define post data
        $new_post = array(
            'post_title'    => '#' . rand(1111111, 9999999) . date('U'),
            'post_status'   => 'publish',
            'post_type'     => 'questionnaire_result',
        );

        $args = array(
            'post_type'      => 'questionnaire_result',
            'posts_per_page' => 1, // Only retrieve one post
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'   => 'id_member',
                    'value' => $id_member,
                ),
                array(
                    'key'   => 'id_questionnaire',
                    'value' => $id_questionnaire,
                ),
            ),
        );
        
        // Create a new WP_Query instance
        $query = new WP_Query($args);
        
        // Check if there are posts found
        if ($query->have_posts()) {
            // Loop through the posts
            while ($query->have_posts()) {
                $query->the_post();
                // Get the post ID
                $post_id = get_the_ID();
            }
            // Reset post data
            wp_reset_postdata();
        } else {
            // Insert the post into the database
            $post_id = wp_insert_post($new_post);
        }


        // Save answers
        $ans = $anscheck = [];
        foreach ($formData[$id_questionnaire] as $key => $val) {
            $ans[$key] = [$key, $val, $id_questionnaire];
            $anscheck[$key] = $val;
        }

        $score_satuan = get_post_meta($id_questionnaire, '_cmb2_qa_group_score', true);
        $qnas = get_post_meta($id_questionnaire, '_cmb2_qa_group_qa_group', true);

        // Check if the post was inserted successfully
        if (!is_wp_error($post_id)) {
            // Update post meta with questionnaire results
            update_post_meta($post_id, 'id_member', $id_member);
            update_post_meta($post_id, 'id_questionnaire', $id_questionnaire);
            update_post_meta($post_id, 'answer', $ans);
            // Respond with success message or other relevant data
            echo 'Post inserted successfully with ID: ' . $post_id;
        } else {
            // Respond with an error message
            echo 'Error inserting post: ' . $post_id->get_error_message();
        }
    } else {
        // If data is incomplete, respond with an error
        echo 'Error: Data tidak lengkap';
    }

    // Important: Always exit the AJAX process
    exit();
}

// Daftarkan fungsi di WordPress untuk AJAX request
add_action('wp_ajax_update_hasil_questionnaire', 'save_hasil_questionnaire');
// add_action('wp_ajax_nopriv_update_hasil_questionnaire', 'save_hasil_questionnaire');

function wss_menu_sidebar_courses() {
    $user_id = get_current_user_id();
    $id_floating = get_option('selected_questionnaire');
    $args = array(
        'post_type'      => 'questionnaire_result', // Ganti dengan jenis posting Anda
        'posts_per_page' => 1, // Jumlah posting yang ingin diambil
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'   => 'id_member',
                'value' => $user_id,
                'compare' => '=',
            ),
            array(
                'key'   => 'id_questionnaire',
                'value' => $id_floating,
                'compare' => '=',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    $active = (isset($_GET['page']) && $_GET['page'] == 'for-you') ? 'active' : '';
    if ($query->have_posts()) {
        $count = $query->found_posts;
        if($count >= 1){
            ?>
            <li>
                <a class="<?php echo $active; ?>" href="?page=for-you">For You</a>
            </li>
            <?php
        }
        wp_reset_postdata(); // Reset global post data
    }

}
add_action('wss-left-sidebar-courses', 'wss_menu_sidebar_courses');

function wss_page_courses() {
    $page = $_GET['page'] ?? '';
    
    if($page == 'for-you') {
        echo wss_fyp_page();
    } else if($page == 'questionnaire') {
        echo wss_questionnaire_page();
    } else if($page == 'single-questionnaire') {
        $id = $_GET['id'] ?? '';
        echo wss_questionnaire_single($id);
        ?>
        <style>
            .ac-filter-course {
                display: none !important;
            }
        </style>
        <?php
    } else if($page == 'single-fyp') {
        $id = $_GET['id'] ?? '';
        echo wss_fyp_single($id);
    }
}
add_action('wss-page-courses', 'wss_page_courses');

function tambah_submenu_questionnaire() {
    add_submenu_page(
        'edit.php?post_type=questionnaire', // menu utama
        'Floating Questionnaire', // judul submenu
        'Floating Questionnaire', // teks menu
        'manage_options', // capability yang diperlukan untuk mengakses
        'pilih_questionnaire', // slug menu
        'tampilkan_form_pilih_questionnaire' // fungsi yang menampilkan kontennya
    );
}
add_action('admin_menu', 'tambah_submenu_questionnaire');

// Fungsi untuk menampilkan konten submenu
function tampilkan_form_pilih_questionnaire() {
    ?>
    <div class="wrap">
        <h2>Pilih Questionnaire</h2>
        <form method="post" action="">
            <label for="select_questionnaire">Pilih Questionnaire:</label>
            <select name="select_questionnaire" id="select_questionnaire">
                <option>-</option>
                <?php
                // Ambil daftar questionnaire dari database
                $questionnaires = get_posts(array('post_type' => 'questionnaire', 'posts_per_page' => -1));
                
                foreach ($questionnaires as $questionnaire) {
                    $selected = (get_option('selected_questionnaire') == $questionnaire->ID) ? 'selected' : '';
                    echo '<option value="' . $questionnaire->ID . '" '.$selected.'>' . $questionnaire->post_title . '</option>';
                }
                ?>
            </select>
            <br>
            <input type="submit" name="save_questionnaire" class="button button-primary" value="Save">
        </form>
    </div>
    <?php
}

// Tanggapi saat formulir disubmit
function proses_form_pilih_questionnaire() {
    if (isset($_POST['save_questionnaire'])) {
        $selected_questionnaire_id = $_POST['select_questionnaire'];

        // Lakukan apa yang perlu Anda lakukan dengan questionnaire yang dipilih, misalnya menyimpan di pengaturan
        update_option('selected_questionnaire', $selected_questionnaire_id);

        // Tambahkan pesan sukses
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Questionnaire berhasil disimpan!</p></div>';
        });
    }
}
add_action('admin_init', 'proses_form_pilih_questionnaire');


function questionnaire_notice() {
    $id_floating = get_option('selected_questionnaire');
    $page = $_GET['page'] ?? '';
?>
    <div class="wss-container" >
        <div class="wss-notice" id="questionnaire-notice">

                <?php 
                if($page == 'single-questionnaire'){ ?>
                    <a href="?">
                        <svg style="top:-2px;position:relative;" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        </svg> Courses
                    </a>
                <?php
                } else {
                ?>
                <a href="?page=single-questionnaire&id=<?php echo $id_floating; ?>">
                <span style="display: flex;vertical-align: middle;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="white" style="padding-right:10px;" class="bi bi-collection-play" viewBox="0 0 16 16">
                        <path d="M2 3a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 0-1h-11A.5.5 0 0 0 2 3m2-2a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7A.5.5 0 0 0 4 1m2.765 5.576A.5.5 0 0 0 6 7v5a.5.5 0 0 0 .765.424l4-2.5a.5.5 0 0 0 0-.848l-4-2.5z"/>
                        <path d="M1.5 14.5A1.5 1.5 0 0 1 0 13V6a1.5 1.5 0 0 1 1.5-1.5h13A1.5 1.5 0 0 1 16 6v7a1.5 1.5 0 0 1-1.5 1.5zm13-1a.5.5 0 0 0 .5-.5V6a.5.5 0 0 0-.5-.5h-13A.5.5 0 0 0 1 6v7a.5.5 0 0 0 .5.5z"/>
                    </svg>
                    <span style="display: inline-block;vertical-align: middle;">
                        <b><?php echo get_the_title($id_floating); ?></b><br/>
                        <span><?php echo get_post_meta($id_floating, '_cmb2_qa_group_description', true); ?></span>
                    </span>
                </span>
                </a>
                <a class="wss-red-button right-notice" href="?page=single-questionnaire&id=<?php echo $id_floating; ?>">
                <?php echo get_post_meta($id_floating, '_cmb2_qa_group_button', true); ?>
                </a>
                <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css">
                <?php
                }
                ?>
            <!-- <a class="wss-close-button" onclick="tutupNotice()">X</a> -->
        </div>
    </div>
<?php
}
add_action('top-courses', 'questionnaire_notice');

// Fungsi untuk menambahkan menu "Courses Menu"
function add_courses_menu() {
    register_nav_menu('courses-menu', __('Courses Menu'));
}

// Panggil fungsi saat tema diaktifkan
add_action('after_setup_theme', 'add_courses_menu');


function list_fyp(){
    // fungsi FYP
    global $post;
    $user_id = get_current_user_id();
    $id_floating = get_option('selected_questionnaire');
    $args = array(
        'post_type'      => 'questionnaire_result', // Ganti dengan jenis posting Anda
        'posts_per_page' => 1, // Jumlah posting yang ingin diambil
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'   => 'id_member',
                'value' => $user_id,
                'compare' => '=',
            ),
            array(
                'key'   => 'id_questionnaire',
                'value' => $id_floating,
                'compare' => '=',
            ),
        ),
    );

    $query_questionnaire_result = new WP_Query($args);
    $result_meta = [];
    if ($query_questionnaire_result->have_posts()) {
        // Ada posting yang sesuai dengan kriteria
        while ($query_questionnaire_result->have_posts()) {
            $query_questionnaire_result->the_post();
            $result_metas = get_post_meta($post->ID, 'answer', true);
            // print_r($result_metas);
            foreach($result_metas as $data){
                $id = $data[0] ?? '';
                $id = str_replace('qna_','', $id);
                $id = str_replace($data[2],'', $id);
                $jawab = $data[1] ?? '';
                $result_meta[$id] = $jawab;
            }
        }
        wp_reset_postdata(); // Reset global post data
    }

    $kunci_queries = get_post_meta($id_floating, '_cmb2_qa_group_courses', true);
    $kunci_query =[];
    foreach($kunci_queries as $data){
        $id_question = $data['_cmb2_qa_group_number_question'];
        $jawab_question = $data['_cmb2_qa_group_and_answer'];
        $kunci_query[$id_question][$jawab_question][] = $data['_cmb2_qa_group_selected_post'];
    }
    $id = $kunci_query[$id][$jawab] ?? [];
    return $id;
}

function theme_register_menus() {
    register_nav_menus(
        array(
            'questionnaire-menu' => esc_html__('Questionnaire Menu', 'your-theme-textdomain'), // Lokasi menu baru 'questionnaire'
        )
    );
}
add_action('after_setup_theme', 'theme_register_menus');

add_action('admin_menu', 'add_export_button');

function add_export_button() {
    add_submenu_page(
        'edit.php?post_type=questionnaire',
        'Export Hasil',
        'Export Hasil',
        'manage_options',
        'export-xlsx',
        'export_to_xlsx'
    );
}

function export_to_xlsx() {
    // Load PhpSpreadsheet library
    require_once 'PhpSpreadsheet/phpstan-conditional.php';

    // Ambil data post type
    $posts = get_posts(array('post_type' => 'questionnaire_result', 'numberposts' => -1));

    // Buat objek Spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->setCellValue('A1', 'Title');
    $sheet->setCellValue('B1', 'Content');
    // Tambahkan kolom metanya sesuai kebutuhan

    // Isi data
    $row = 2;
    foreach ($posts as $post) {
        $sheet->setCellValue('A' . $row, $post->post_title);
        $sheet->setCellValue('B' . $row, $post->post_content);
        // Isi kolom metanya sesuai kebutuhan
        $row++;
    }

    // Simpan sebagai file XLSX
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('export.xlsx');

    // Redirect atau tampilkan pesan sukses
    echo 'Export berhasil. <a href="' . admin_url('admin.php?page=export-xlsx') . '">Kembali</a>';
}