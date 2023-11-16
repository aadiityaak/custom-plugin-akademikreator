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

        $score = get_post_meta($id_questionnaire, '_cmb2_qa_group_score', true);
        $qnas = get_post_meta($id_questionnaire, '_cmb2_qa_group_qa_group', true);
        $score_satuan = $score/count($qnas);
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
    ?>
        <li>
            <a href="?page=for-you">For You</a>
        </li>
    <?php
}
add_action('wss-left-sidebar-courses', 'wss_menu_sidebar_courses');

function wss_page_courses() {
    $page = $_GET['page'] ?? '';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $user_id = get_current_user_id();
    $user_score = calculate_user_total_score($user_id);
    ?>
    <link href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css" rel="stylesheet" />
    <div class="wss-w-100">
        Score anda <?php echo $user_score; ?>
    </div>
    <?php
    $args = array(
        'post_type' => 'modul_video',
        'meta_query' => array(
            array(
                'key' => 'score',
                'value' => $user_score,
                'compare' => '<=',
                'type' => 'NUMERIC',
            ),
        ),
        'posts_per_page' => 10,
        'paged' => $paged,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            // Loop konten post di sini
            ?>
                <div class="wss-w-50 wss-w-md-33 wss-position-relative wss-p-1">
                    <a class="wss-d-block wss-card" href="<?php echo get_the_permalink(); ?>">
                        <div class="wss-ratio wss-ratio-3x4">
                            <img src="<?php 
                            $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array('220','220'), true );
                            $thumbnail_url = $thumbnail_url[0];
                            echo $thumbnail_url;
                            ?>" alt="<?php echo get_the_title(); ?>"/>
                        </div>

                        <span class="wss-floating-badge">
                            <?php 
                            $first_term_name = get_the_terms( $post->ID, 'category_modul' )[0]->name;
                            echo $first_term_name ;
                            ?>
                        </span>
                        <h3 class="wss-pt-1">
                            <?php echo get_the_title(); ?>
                        </h3>
                    </a>
                </div>
            <?php
        endwhile;
    
        // Tampilkan pagination dengan angka 1, 2, 3, dst.
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'prev_text' => __('Previous', 'textdomain'),
            'next_text' => __('Next', 'textdomain'),
            'type' => 'plain', // Gunakan 'plain' untuk pagination dengan angka
        ));
    
        // Kembalikan settingan global post
        wp_reset_postdata();
    else :
        // Jika tidak ada post yang sesuai
        echo __('No posts found', 'textdomain');
    endif;
    
}
add_action('wss-page-courses', 'wss_page_courses');