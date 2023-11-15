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
        add_filter('manage_questionnaire_result_posts_columns', array($this, 'add_custom_columns_questionnaire_result'));
        add_action('manage_questionnaire_result_posts_custom_column', array($this, 'display_custom_columns_questionnaire_result'), 10, 2);
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

    // Add this code to your theme's functions.php file or in a custom plugin

    public function add_custom_columns_questionnaire_result($columns) {
        // Add custom columns
        $columns['score'] = 'Score';
        $columns['id_member'] = 'Member (Responden)';
        $columns['id_questionnaire'] = 'Questionnaire';

        return $columns;
    }
    

    public function display_custom_columns_questionnaire_result($column, $post_id) {
        // Display custom column values
        switch ($column) {
            case 'score':
                $score = get_post_meta($post_id, 'score', true);
                echo esc_html($score);
                break;
            case 'id_member':
                $id_member = get_post_meta($post_id, 'id_member', true);
                $user_info = get_userdata($id_member);
                $username = $user_info->user_login;
                echo esc_html($username);
                break;
            case 'id_questionnaire':
                $id_questionnaire = get_post_meta($post_id, 'id_questionnaire', true);
                $title_questionnaire = get_the_title($id_questionnaire);
                $url_questionnaire = get_the_permalink($id_questionnaire);
                echo '<a target="_blank" href="'.$url_questionnaire.'">'.$title_questionnaire.'</a>';
                break;
            // Add more cases for additional columns if needed
        }
    }
    
 }
 
 // Inisialisasi class Custom_Post_Types_Register
 $custom_post_types_register = new Custom_Plugin_Post_Types();
 