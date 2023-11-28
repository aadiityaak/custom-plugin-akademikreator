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

        $cmb->add_field(array(
            'name' => esc_html__('Description', 'your-text-domain'),
            'id'   => $prefix . 'description',
            'type' => 'text',
            'description' => esc_html__('Ditampilkan didalam notice', 'your-text-domain'),
            // Add any other necessary options here
        ));

        $cmb->add_field(array(
            'name' => esc_html__('Button Text', 'your-text-domain'),
            'id'   => $prefix . 'button',
            'type' => 'text',
            'description' => esc_html__('Ditampilkan didalam notice', 'your-text-domain'),
            // Add any other necessary options here
        ));

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
            'name' => esc_html__('If Question', 'your-text-domain'),
            'id'   => $prefix . 'number_question',
            'type' => 'select',
            'options_cb' => 'get_mpcs_question_options',
            'classes' => 'list-question', // Ganti dengan kelas CSS yang diinginkan
            // 'after'=> 'Isi dengan nomor urut pertanyaan.',
        ));

        $cmb->add_group_field($group_penentu, array(
            'name' => esc_html__('And Answer', 'your-text-domain'),
            'id'   => $prefix . 'and_answer',
            'type' => 'text',
            'type' => 'select',
            'options_cb' => 'get_mpcs_answer_options',
            'classes' => 'list-answer', // Ganti dengan kelas CSS yang diinginkan
        ));
        $cmb->add_group_field($group_penentu, array(
            'name' => esc_html__('Rekomendasi Modul', 'your-text-domain'),
            'id'   => $prefix . 'selected_post',
            'type' => 'multicheck',
            'show_option_none' => true,
            'options_cb' => 'get_mpcs_course_options',
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

function get_mpcs_question_options($field) {
    $post_options = array();

    global $wpdb;
    $list_soals = get_post_meta($field->object_id, '_cmb2_qa_group_qa_group', true);
    // Cek apakah terdapat hasil
    if ($list_soals) {
        $i = 1;
        foreach ($list_soals as $result) {
            $id = $i++;
            $post_options[$id] = $result['_cmb2_qa_group_question'];
        }
    }
    return $post_options;
}

function get_mpcs_answer_options($field) {
    $post_options = array();

    global $wpdb;
    $list_soals = get_post_meta($field->object_id, '_cmb2_qa_group_qa_group', true);

    // Cek apakah terdapat hasil
    if ($list_soals) {
        $i = 1;
        foreach ($list_soals as $result) {
            $j = $i++;
            // echo '<pre>';
            // print_r($result['_cmb2_qa_group_answer']);
            // echo '</pre>';
            $jawabans = $result['_cmb2_qa_group_answer'];
            if($jawabans){
                $x = 1;
                foreach($jawabans as $data) {
                    $y = $x++;
                    // print_r($data);
                    $post_options[$data] = '('.$j.') '.$data;
                }
            }
        }
    }
    return $post_options;
}

// Fungsi untuk menghasilkan daftar post
function get_mpcs_course_options($field) {
    $post_options = array();

    global $wpdb;

    // Ganti 'mpcs-course' dengan nama post type yang sesuai
    $post_type = 'mpcs-course';

    // Mengekstrak nomor grup dari ID dengan ekspresi reguler
    $meta = get_post_meta($field->object_id, '_cmb2_qa_group_courses', true);
    $full_id = $field->args( 'id' );
    $number = str_replace('_cmb2_qa_group_courses_','',$full_id);
    $number = str_replace('__cmb2_qa_group_selected_post','',$number);
    $urutan = $meta[$number]['urutan_post_field'] ?? '';
    // print_r($urutan);
    $group_number = isset($matches[1]) ? intval($matches[1]) : 0;

    // Ganti '_cmb2_qa_group_courses[%d][urutan_post_field]' sesuai dengan kunci meta yang sesuai
    $meta_key = sprintf('_cmb2_qa_group_courses[%d][urutan_post_field]', $group_number);

    // Query untuk mendapatkan post ID, post title, dan urutan (jika tersedia)
    $query = "SELECT ID, post_title, meta_value
              FROM $wpdb->posts
              LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = %s) 
              WHERE post_type = %s AND post_status = 'publish'
              ORDER BY CAST(meta_value AS UNSIGNED), post_title";
    
    $results = $wpdb->get_results($wpdb->prepare($query, $meta_key, $post_type));
    
    // Cek apakah terdapat hasil
    if ($results) {
        foreach ($results as $result) {
            $post_id = $result->ID;
            $post_title = $result->post_title;
            $post_options[$post_id] = $post_title;
        }
    }

    // Pisahkan string urutan menjadi array
    $urutan_array = explode(',', $urutan);

    // Buat array baru untuk menyimpan hasil
    $result_post_options = array();

    // Proses nilai yang sesuai urutan
    foreach ($urutan_array as $id) {
        if (isset($post_options[$id])) {
            $result_post_options[$id] = $post_options[$id];
            unset($post_options[$id]);
        }
    }

    // Gabungkan nilai yang tidak sesuai urutan di bawahnya
    $result_post_options += $post_options;
    return $result_post_options;
}
