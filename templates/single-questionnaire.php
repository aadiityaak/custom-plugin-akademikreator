<?php

/**
 * Masukkan semua function tambahan disini
 *
 * @link       {REPLACE_ME_URL}
 * @since      1.0.0
 *
 * @package    Custom_Plugin
 * @subpackage Custom_Plugin/includes
 */

function wss_questionnaire_single($post_id) {
    ob_start();
    global $post;
    $magic_elementor_blog_layout = get_theme_mod('magic_elementor_blog_layout', 'rightside');

    if (is_active_sidebar('sidebar-1') && $magic_elementor_blog_layout != 'fullwidth' && 'post' == get_post_type()) {
        $magic_elementor_blog_column = 'mg-grid-9';
    } else {
        $magic_elementor_blog_column = 'mg-grid-12';
    }
    $user_id = get_current_user_id();
    $id_floating = get_option('selected_questionnaire');
    $args = array(
        'post_type'      => 'questionnaire_result', // Ganti dengan jenis posting Anda
        'posts_per_page' => 1, // Jumlah posting yang ingin diambil
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'   => 'id_member',
                'value' => $user_id,
                'compare' => '=',
            ),
            array(
                'key'   => 'id_questionnaire',
                'value' => $id_floating,
                'compare' => '=',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        // Ada posting yang sesuai dengan kriteria
        while ($query->have_posts()) {
            $query->the_post();
            $result_meta = get_post_meta($post->ID, 'answer', true);
            // echo '<pre>';
            // print_r($result_meta);
            // echo '</pre>';
        }
        wp_reset_postdata(); // Reset global post data
    }
    ?>

    <div class="wss-container mg-main-blog nxsingle-post">
        <div class="mg-flex">
            <div class="<?php echo esc_attr($magic_elementor_blog_column); ?>">
                <main id="primary" class="site-main">
                    <?php
                    
                    $qnas = get_post_meta($post_id, '_cmb2_qa_group_qa_group', true);
                    $i = 1;
                    $user_info = get_userdata($user_id);

                    if ($user_info) {
                        $display_name = $user_info->display_name;
                        // echo $display_name;
                        // Calculate the duration
                        $registration_date = $user_info->user_registered;
                        $registration_timestamp = strtotime($registration_date);
                        $current_timestamp = current_time('timestamp');
                        
                        $duration_in_seconds = $current_timestamp - $registration_timestamp;

                        // Convert seconds to a human-readable format
                        $duration = human_time_diff($registration_timestamp, $current_timestamp);

                        // echo 'User registered on: ' . date('F j, Y', $registration_timestamp) . '<br>';
                        // echo 'Duration: ' . $duration;
                    }
                    ?>
                    <form class="questionnaire-frame wss-mb-2" data-id="<?php echo $post_id; ?>">
                        <div class="wss-card wss-question-frame wss-mb-2">
                            <h2 class="wss-title wss-p-1"><b><?php echo $display_name;?></b> (<?php echo $duration;?>)</h2>
                        </div>
                        <?php
                        foreach($qnas as $qna){
                            $j = $i++;
                            // echo '<pre>';
                            //     print_r($qna);
                            // echo '</pre>';
                            $condition = $qna['_cmb2_qa_group_condition'] ? 'wss-d-none' : '';
                            $condition_key = $qna['_cmb2_qa_group_number_question'] ?? '';
                            $condition_val = $qna['_cmb2_qa_group_and_answer'] ? implode(' ',$qna['_cmb2_qa_group_and_answer']) : '';
                            ?>
                            <div class="wss-card wss-question-frame wss-mb-2 <?php echo $condition; ?>" data-condition-key="<?php echo $condition_key; ?>" data-condition-val="<?php echo $condition_val; ?>">
                                <h3 class="question-title"><?php echo $qna['_cmb2_qa_group_question']; ?></h3>
                                <div class="wss-d-flex wsss-flex-wrap">
                                    <?php
                                    $options = $qna['_cmb2_qa_group_answer'] ?? [];
                                    $x = 1;
                                    foreach($options as $option){
                                        $y = $x++;
                                        $checked = ( $option == $result_meta['qna_'.$post_id.$j][1] ) ? 'checked' : '';
                                        // echo $option .'=='. $result_meta['qna_'.$post_id.$j][1];
                                        ?>
                                        <div class="wss-w-50 d-flex wss-p-1">
                                            <input class="wss-d-none change-parent" id="<?php echo $post_id.$j.$y; ?>" data-question="<?php echo $j; ?>" type="radio" name="<?php echo 'qna_'.$post_id.$j; ?>" value="<?php echo $option; ?>" aria-label="Checkbox for following text input" <?php echo $checked; ?>>
                                            <label class="wss-pl-2 wss-d-block wss-cursor-pointer" for="<?php echo $post_id.$j.$y; ?>">
                                                <span><?php echo $option; ?></span>
                                            </label>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <button class="wss-btn-submit" type="submit">
                            <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-upload wss-d-iline-block" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
                                <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                            </span> Submit
                        </button>
                    </form>
                    <?php

                    ?>

                </main><!-- #main -->
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css">
    <?php
    return ob_get_clean();
}
