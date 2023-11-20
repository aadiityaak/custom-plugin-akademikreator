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
            'name' => esc_html__('Score', 'your-text-domain'),
            'id'   => $prefix . 'score',
            'type' => 'text',
            'default' => 10, // Set nilai default di sini
            'description' => esc_html__('Score per jawaban benar', 'your-text-domain'),
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
            'name' => esc_html__('A', 'your-text-domain'),
            'id'   => $prefix . 'answer_a',
            'type' => 'text',
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('B', 'your-text-domain'),
            'id'   => $prefix . 'answer_b',
            'type' => 'text',
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('C', 'your-text-domain'),
            'id'   => $prefix . 'answer_c',
            'type' => 'text',
        ));

        $cmb->add_group_field($group_field_id, array(
            'name' => esc_html__('D', 'your-text-domain'),
            'id'   => $prefix . 'answer_d',
            'type' => 'text',
        ));

        // Field untuk memilih jawaban yang benar
        $cmb->add_group_field($group_field_id, array(
            'name'    => esc_html__('Jawaban Benar', 'your-text-domain'),
            'id'      => $prefix . 'correct_answer',
            'type'    => 'select',
            'options' => array(
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
                'd' => 'D',
            ),
            'display_cb' => array($this, 'display_correct_answer_field'), // Metode untuk menampilkan field.
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
