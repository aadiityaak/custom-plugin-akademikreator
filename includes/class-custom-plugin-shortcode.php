<?php

/**
 * Class Custom_Plugin_Shortcode
 */



add_shortcode('total_score', 'total_score_shortcode');

// Function to calculate the total score
function calculate_user_total_score($user_id) {
    $selected_questionnaire = get_option('selected_questionnaire');
    // Query arguments
    $args = array(
        'post_type'      => 'questionnaire_result',
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'AND', // Menambahkan operator logika AND di antara klausa-klausa
            array(
                'key'   => 'id_member',
                'value' => $user_id,
            ),
            array(
                'key'   => 'id_questionnaire',
                'value' => $selected_questionnaire,
            ),
        ),
    );

    // Query the posts
    $questionnaire_results = new WP_Query($args);

    // Initialize total score
    $total_score = 0;

    // Loop through the posts and accumulate the scores
    if ($questionnaire_results->have_posts()) {
        while ($questionnaire_results->have_posts()) {
            $questionnaire_results->the_post();

            // Get the score from post meta
            $score = get_post_meta(get_the_ID(), 'score', true);

            // Add the score to the total
            $total_score += intval($score);
        }

        // Reset post data
        wp_reset_postdata();
    }

    // Return the total score
    return $total_score;
}

// Shortcode function
function total_score_shortcode($atts) {
    // Get the current user ID if not provided in the shortcode [total_score id_member="2"]
    $user_id = isset($atts['id_member']) ? intval($atts['id_member']) : get_current_user_id();

    // Use the common function to calculate the total score
    $total_score = calculate_user_total_score($user_id);

    // Return the total score
    return 'Total Score for User ' . $user_id . ': ' . $total_score;
}

function wss_primary_menu_shortcode() {
    // Menampilkan menu utama
    return wp_nav_menu(array(
        'theme_location' => 'questionnaire-menu',
        'container' => 'nav',
        'container_class' => 'questionnaire-menu-container',
        'menu_class' => 'questionnaire-menu wss-d-flex wss-align-item-end',
        'echo' => false, // Mengembalikan output sebagai string, bukan mencetak langsung
    ));
}

// Mendaftarkan shortcode
add_shortcode('wss-primary-menu', 'wss_primary_menu_shortcode');