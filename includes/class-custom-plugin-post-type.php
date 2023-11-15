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
        add_action('init', array($this, 'register_taxonomies')); // Tambahkan hook untuk registrasi taksonomi
        // Hook into the 'template_include' action
        add_filter('template_include', array($this, 'custom_questionnaire_template'));
     }
 
     /**
      * Register custom post types
      */
     public function register_post_types()
     {
         // Register Questionnaire Post Type
         register_post_type('questionnaire',
             array(
                 'labels' => array(
                     'name' => __('Questionnaire'),
                     'singular_name' => __('Questionnaire'),
                 ),
                 'menu_icon' => 'dashicons-book',
                 'public' => true,
                 'has_archive' => true,
                 'supports' => array('title', 'thumbnail'),
             )
         );
        // Register Hasil Questionnaire Post Type
        register_post_type('questionnaire_result',
            array(
                'labels' => array(
                    'name' => __('Questionnaire Result'),
                    'singular_name' => __('Questionnaire Result'),
                ),
                'menu_icon' => 'dashicons-book',
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'thumbnail'),
                'show_in_menu' => 'edit.php?post_type=questionnaire', // Menetapkan sub menu
                'menu_position' => 20, // Menetapkan posisi dalam sub menu
            )
        );

     }
 
     /**
      * Register custom taxonomy
      */
     public function register_taxonomies()
     {
         // Register Category Taxonomy for Questionnaire
         register_taxonomy('category_questionnaire', 'questionnaire', array(
             'labels' => array(
                 'name' => __('Categories Questionnaire'),
                 'singular_name' => __('Questionnaire'),
             ),
             'hierarchical' => true,
             'show_ui' => true,
             'show_admin_column' => true,
             'query_var' => true,
             'rewrite' => array('slug' => 'category_questionnaire'),
         ));
     }

    /**
     * Callback function to include custom template for single 'questionnaire'
     */
    public function custom_questionnaire_template($template)
    {
        if (is_singular('questionnaire')) {
            // Path ke file template khusus untuk single 'questionnaire'
            $custom_template = plugin_dir_path(__FILE__) . '../templates/single-questionnaire.php';

            // Periksa apakah file template khusus ada
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }
 }
 
 // Inisialisasi class Custom_Post_Types_Register
 $custom_post_types_register = new Custom_Plugin_Post_Types();
 