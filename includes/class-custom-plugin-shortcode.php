<?php

/**
 * Class Custom_Plugin_Shortcode
 */
class Custom_Plugin_Shortcode {
    
    /**
     * Custom_Plugin_Shortcode constructor.
     */
    public function __construct() {
        add_shortcode('total_score', array($this, 'total_score_shortcode')); // [custom-plugin]
    }

    /**
     * Shortcode callback to display text.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     *
     * @return string
     */
    public function total_score_shortcode() {
        // Get the current user ID
        $current_user_id = get_current_user_id();
    
        // Query arguments
        $args = array(
            'post_type'      => 'questionnaire_result',
            'posts_per_page' => -1, // Get all posts
            'meta_query'     => array(
                array(
                    'key'   => 'id_member',
                    'value' => $current_user_id,
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
        return 'Total Score: ' . $total_score;
    }
}

// Inisialisasi class Custom_Plugin_Shortcode
new Custom_Plugin_Shortcode();
