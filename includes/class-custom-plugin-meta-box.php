<?php

/**
 * Class Custom_Plugin_Meta_Box
 */
class Custom_Plugin_Meta_Box {
    
    /**
     * Custom_Plugin_Meta_Box constructor.
     */
    public function __construct() {
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('admin_notices', array($this, 'metabox_admin_notice'));
    }

    /**
     * Register meta boxes.
     *
     * @param array $meta_boxes Meta boxes.
     *
     * @return array
     */
    public function register_meta_boxes($meta_boxes) {
        $prefix = '';

        $meta_boxes[] = array(
            'title'      => esc_html__('Hasil', 'online-generator'),
            'id'         => 'untitled',
            'post_types' => array('questionnaire_result'),
            'context'    => 'normal',
            'fields'     => array(
                array(
                    'type' => 'user',
                    'name' => esc_html__('ID Member', 'online-generator'),
                    'id'   => $prefix . 'id_member',
                ),
                array(
                    'type' => 'post',
                    'name' => esc_html__('ID Questionnaire', 'online-generator'),
                    'id'   => 'id_questionnaire',
                    'post_type'  => 'questionnaire',
                    'field_type' => 'select_advanced',
                ),
                array(
                    'type' => 'key_value',
                    'name' => esc_html__('Jawaban', 'online-generator'),
                    'id'   => 'answer',
                ),
            ),
        );

        return $meta_boxes;
    }

    public function metabox_admin_notice(){
        // global $pagenow;
        // if ( $pagenow == 'options-general.php' ) {
        if ( ! is_plugin_active( 'meta-box/meta-box.php' ) ) {
             echo "<div class='notice notice-warning is-dismissible'>
                 <p>Custom Plugin: Install & Aktifkan plugin 'Meta Box – WordPress Custom Fields Framework.'</p>
             </div>";
        }
        // }
    }
}

// Inisialisasi class Custom_Plugin_Meta_Box
new Custom_Plugin_Meta_Box();