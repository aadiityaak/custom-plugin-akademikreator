<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       {REPLACE_ME_URL}
 * @since      1.0.0
 *
 * @package    Custom_Plugin
 * @subpackage Custom_Plugin/includes
 */

class Custom_Plugin_CMB2 {

    /**
     * Custom_Plugin_CMB2 constructor.
     */
    public function __construct() {
        add_action('cmb2_init', array($this, 'register_metabox_questionnaire'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_styles'));
    }

    /**
     * Register metabox with CMB2.
     */
    public function register_metabox_questionnaire() {
        $prefix = '_cmb2_qa_group_';

        $cmb = new_cmb2_box(array(
            'id'           => $prefix . 'metabox',
            'title'        => esc_html__('Pertanyaan dan Jawaban', 'your-text-domain'),
            'object_types' => array('questionnaire'), // Sesuaikan dengan jenis pos yang ingin Anda tambahkan metabox ini.
        ));

        // $cmb->add_field(array(
        //     'name' => esc_html__('Score', 'your-text-domain'),
        //     'id'   => $prefix . 'score',
        //     'type' => 'text',
        //     'default' => 10, // Set nilai default di sini
        //     'description' => esc_html__('Score per jawaban benar', 'your-text-domain'),
        //     // Add any other necessary options here
        // ));

        $group_field_id = $cmb->add_field(array(
            'id'          => $prefix . 'qa_group',
            'type'        => 'group',
            // 'description' => esc_html__('Tambah pertanyaan dan jawaban', 'your-text-domain'),
            'options'     => array(
                'group_title'   => esc_html__('Pertanyaan {#}', 'your-text-domain'),
                'add_button'    => esc_html__('Tambah Pertanyaan', 'your-text-domain'),
                'remove_button' => esc_html__('Hapus Pertanyaan', 'your-text-domain'),
                'sortable'      => true,
            ),
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('Pertanyaan', 'your-text-domain'),
            'id'   => $prefix . 'question',
            'type' => 'wysiwyg',
            'options' => array(
                'textarea_rows' => 4,
            ),
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('Jawaban', 'your-text-domain'),
            'id'   => $prefix . 'answer',
            'type' => 'text',
            'repeatable' => true,
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('Condition', 'your-text-domain'),
            'id'   => $prefix . 'condition',
            'description' => esc_html__('Centang jika ingin pertanyaan ini ditampilkan jika kondisi tertentu', 'your-text-domain'),
            'type' => 'checkbox',
            'classes'    => 'parent-condition', // Extra cmb2-wrap classes
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('Nomor Pertanyaan', 'your-text-domain'),
            'id'   => $prefix . 'number_question',
            'type' => 'text',
            'after'=> 'Isi dengan nomor urut pertanyaan.',
            'classes'    => 'if-condition', // Extra cmb2-wrap classes
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('Jawaban', 'your-text-domain'),
            'id'   => $prefix . 'and_answer',
            'type' => 'text',
            'repeatable' => true,
            'classes'    => 'if-condition', // Extra cmb2-wrap classes
        ));


        $group_penentu = $cmb->add_field(array(
            'id'          => $prefix . 'courses',
            'type'        => 'group',
            // 'description' => esc_html__('Tambah pertanyaan dan jawaban', 'your-text-domain'),
            'options'     => array(
                'group_title'   => esc_html__('Kondisi {#}', 'your-text-domain'),
                'add_button'    => esc_html__('Tambah Kondisi', 'your-text-domain'),
                'remove_button' => esc_html__('Hapus Kondisi', 'your-text-domain'),
                'sortable'      => true,
            ),
        ));

        $cmb->add_group_field($group_penentu, array(
            'name' => esc_html__('Nomor Pertanyaan', 'your-text-domain'),
            'id'   => $prefix . 'number_question',
            'type' => 'text',
            'after'=> 'Isi dengan nomor urut pertanyaan.',
        ));

        $cmb->add_group_field($group_penentu, array(
            'name' => esc_html__('Jawaban', 'your-text-domain'),
            'id'   => $prefix . 'and_answer',
            'type' => 'text',
            // 'repeatable' => true,
        ));
        // Ganti 'your-text-domain' dengan domain teks tema atau plugin Anda
        $cmb->add_group_field($group_penentu, array(
            'name' => esc_html__('Pilih Post', 'your-text-domain'),
            'id'   => $prefix . 'selected_post',
            'type' => 'multicheck',
            'show_option_none' => true,
            'options_cb' => 'get_mpcs_course_options', // Fungsi yang akan menghasilkan daftar post
            'classes' => 'list-order', // Ganti dengan kelas CSS yang diinginkan
        ));
        $cmb->add_group_field($group_penentu, array(
            'name' => 'Urutan Post',
            'id'   => 'urutan_post_field',
            'type' => 'text', // Ganti dengan 'hidden' jika ingin tersembunyi
            'classes' => 'order-fyp', // Ganti dengan kelas CSS yang diinginkan
        ));
    }
    

    /**
     * Enqueue custom styles only on 'post-new.php' for 'questionnaire' post type.
     */
    public function enqueue_custom_styles($hook) {
        global $post_type;
        if (('post.php' === $hook || 'post-new.php' === $hook) && 'questionnaire' === $post_type) {
            wp_enqueue_style('custom-questionnaire-style', plugin_dir_url(__FILE__) . '../admin/css/custom-plugin-questionnaire.css', array(), $this->version, 'all');
        }
    }
}

// Inisialisasi class
$custom_plugin_cmb2 = new Custom_Plugin_CMB2();
// Fungsi untuk menghasilkan daftar post
function get_mpcs_course_options() {
    $post_options = array();

    global $wpdb;

    // Ganti 'mpcs-course' dengan nama post type yang sesuai
    $post_type = 'mpcs-course';

    // Query untuk mendapatkan post ID, post title, dan urutan (jika tersedia)
    $query = "SELECT ID, post_title, meta_value
              FROM $wpdb->posts
              LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = 'urutan_post_field') 
              WHERE post_type = %s AND post_status = 'publish'
              ORDER BY CAST(meta_value AS UNSIGNED), post_title";
    
    $results = $wpdb->get_results($wpdb->prepare($query, $post_type));
    
    // Cek apakah terdapat hasil
    if ($results) {
        foreach ($results as $result) {
            $post_id = $result->ID;
            $post_title = $result->post_title;
            $post_options[$post_id] = $post_title;
        }
    }
    return $post_options;
}