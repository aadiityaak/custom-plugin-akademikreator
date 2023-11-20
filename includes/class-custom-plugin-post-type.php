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
        // add_filter('template_include', array($this, 'custom_questionnaire_template'));
        add_filter('manage_questionnaire_result_posts_columns', array($this, 'add_custom_columns_questionnaire_result'));
        add_action('manage_questionnaire_result_posts_custom_column', array($this, 'display_custom_columns_questionnaire_result'), 10, 2);

        add_filter('manage_questionnaire_posts_columns', array($this, 'add_custom_columns_questionnaire'));
        add_action('manage_questionnaire_posts_custom_column', array($this, 'display_custom_columns_questionnaire'), 10, 2);

        add_filter('manage_modul_video_posts_columns', array($this, 'add_custom_columns_modul_video'));
        add_action('manage_modul_video_posts_custom_column', array($this, 'display_custom_columns_modul_video'), 10, 2);
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
         // Register Questionnaire Post Type
         register_post_type('modul_video',
             array(
                 'labels' => array(
                     'name' => __('Modul Video'),
                     'singular_name' => __('Modul Video'),
                 ),
                 'menu_icon' => 'dashicons-video-alt3',
                 'public' => true,
                 'has_archive' => false,
                 'supports' => array('title', 'thumbnail'),
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
                 'singular_name' => __('Categories Questionnaire'),
             ),
             'hierarchical' => true,
             'show_ui' => true,
             'show_admin_column' => true,
             'query_var' => true,
             'rewrite' => array('slug' => 'category_questionnaire'),
         ));

         register_taxonomy('category_modul', 'modul_video', array(
            'labels' => array(
                'name' => __('Categories Moduls'),
                'singular_name' => __('Categories Moduls'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'category_modul'),
        ));
     }

    /**
     * Callback function to include custom template for single 'questionnaire'
     */
    public function custom_questionnaire_template($template)
    {
        if (is_singular('questionnaire')) {
            // Path ke file template khusus untuk single 'questionnaire'
            $single_template = plugin_dir_path(__FILE__) . '../templates/single-questionnaire.php';

            // Periksa apakah file template khusus ada
            if (file_exists($single_template)) {
                return $single_template;
            }
        }
        if (is_archive('questionnaire')) {
            // Path ke file template khusus untuk single 'questionnaire'
            $archive_template = plugin_dir_path(__FILE__) . '../templates/archive-questionnaire.php';

            // Periksa apakah file template khusus ada
            if (file_exists($archive_template)) {
                return $archive_template;
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

    public function add_custom_columns_questionnaire($columns) {
        // Add custom columns
        $columns['score'] = 'Total Score';
        $columns['questionnaire'] = 'Questionnaire';
        return $columns;
    }

    
    

    public function display_custom_columns_questionnaire($column, $post_id) {
        // Display custom column values
        switch ($column) {
            case 'score':
                $questionnaire = get_post_meta($post_id, '_cmb2_qa_group_qa_group', true);
                $score = get_post_meta($post_id, '_cmb2_qa_group_score', true);
                echo count($questionnaire) * $score;
                break;
            case 'questionnaire':
                $questionnaire = get_post_meta($post_id, '_cmb2_qa_group_qa_group', true);
                echo count($questionnaire);
                break;
            // Add more cases for additional columns if needed
        }
    }

    public function add_custom_columns_modul_video($columns) {
        // Add custom columns
        $columns['score'] = 'Score Minimal';
        $columns['url_modul'] = 'Modul URL';
        return $columns;
    }

    
    

    public function display_custom_columns_modul_video($column, $post_id) {
        // Display custom column values
        switch ($column) {
            case 'score':
                $score = get_post_meta($post_id, 'score', true);
                echo esc_html($score);
                break;
            case 'url_modul':
                $questionnaire = get_post_meta($post_id, 'url_modul', true);
                echo $questionnaire;
                break;
            // Add more cases for additional columns if needed
        }
    }
    
 }
 
 // Inisialisasi class Custom_Post_Types_Register
 $custom_post_types_register = new Custom_Plugin_Post_Types();
 