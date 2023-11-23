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
            $ans[$key] = [$key, $val];
            $anscheck[$key] = $val;
        }

        $score_satuan = get_post_meta($id_questionnaire, '_cmb2_qa_group_score', true);
        $qnas = get_post_meta($id_questionnaire, '_cmb2_qa_group_qa_group', true);
        $hasil = [];
        $i = 1;
        $nilai = [];
        foreach($qnas as $qna){
            $j = $i++;
            $hasil[$id_questionnaire.$j] = $qna['_cmb2_qa_group_correct_answer'];
            if($qna['_cmb2_qa_group_correct_answer'] == $anscheck['qna_'.$id_questionnaire.$j]) {
                $nilai[] = $score_satuan;
            }
        }

        // Check if the post was inserted successfully
        if (!is_wp_error($post_id)) {
            // Update post meta with questionnaire results
            update_post_meta($post_id, 'id_member', $id_member);
            update_post_meta($post_id, 'id_questionnaire', $id_questionnaire);
            update_post_meta($post_id, 'answer', $ans);
            update_post_meta($post_id, 'score', array_sum($nilai));
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



 // Add custom column to the user list table
 add_filter('manage_users_columns', 'add_custom_column_to_user_list');
 // Display content in the custom column
 add_action('manage_users_custom_column', 'display_content_in_custom_column', 10, 3);
 // Add custom column to the user list table
function add_custom_column_to_user_list($columns) {
    $columns['total_score'] = 'Total Score';
    return $columns;
}

// Display content in the custom column
function display_content_in_custom_column($value, $column_name, $user_id) {
    if ($column_name === 'total_score') {
        // Use the [total_score] shortcode for the specific user
        return calculate_user_total_score($user_id);
    }
}

function theme_register_menus() {
    register_nav_menus(
        array(
            'questionnaire-menu' => esc_html__('Questionnaire Menu', 'your-theme-textdomain'), // Lokasi menu baru 'questionnaire'
        )
    );
}
add_action('after_setup_theme', 'theme_register_menus');

function wss_menu_sidebar_courses() {
    $user_id = get_current_user_id();
    $total_score = calculate_user_total_score($user_id);
    if($total_score >= 1){
    ?>
        <li>
            <a href="?page=for-you">For You</a>
        </li>
    <?php
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
            <p>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-check" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                </svg>
                Questionnaire: <?php echo get_the_title($id_floating); ?>
                </a>
                <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css">
                <?php
                }
                ?>
            </p>
            <!-- <a class="wss-close-button" onclick="tutupNotice()">X</a> -->
        </div>
    </div>
<?php
}
add_action('top-courses', 'questionnaire_notice');
