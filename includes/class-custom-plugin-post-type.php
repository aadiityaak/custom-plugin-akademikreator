<?php
/**
 *
 * @link       {REPLACE_ME_URL}
 * @since      1.0.0
 *
 * @package    Custom_Plugin
 * @subpackage Custom_Plugin/includes
 */

class Custom_Plugin_Post_Types
{
    public function __construct()
    {
        // Hook into the 'init' action
        add_action('init', array($this, 'register_post_types'));
    }

    /**
     * Register custom post types
     */
    public function register_post_types()
    {
        // Register Blog Post Type
        register_post_type('questionnaire',
            array(
                'labels' => array(
                    'name' => __('Questionnaire'),
                    'singular_name' => __('Questionnaire'),
                ),
                'menu_icon' => 'dashicons-book',
                'public' => true,
                'has_archive' => true,
                'supports' => array('title','thumbnail'),
            )
        );

        // Register Mobil Post Type
        // register_post_type('mobil',
        //     array(
        //         'labels' => array(
        //             'name' => __('Mobil'),
        //             'singular_name' => __('Mobil'),
        //         ),
        //         'public' => true,
        //         'has_archive' => true,
        //         'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        //     )
        // );
    }
}

// Inisialisasi class Custom_Post_Types_Register
$custom_post_types_register = new Custom_Plugin_Post_Types();