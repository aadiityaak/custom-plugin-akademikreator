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
// Fungsi untuk mendapatkan ID video YouTube dari URL
function get_youtube_video_id($url) {
    $video_id = '';
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    
    if (preg_match($pattern, $url, $matches)) {
        $video_id = $matches[1];
    }

    return $video_id;
}

function wss_fyp_single($post_id) {
    ob_start();
    global $post;
    $url_modul = get_post_meta($post_id, 'url_modul', true);
    $score_modul = get_post_meta($post_id, 'score', true);

    // Mengambil ID video dari URL
    $video_id = get_youtube_video_id($url_modul);
    ?>
    <h1 class="wss-heading-page"><?php echo get_the_title($post_id); ?></h1>
    <div class="wss-container mg-main-blog nxsingle-post wss-pt-1">
        <main id="primary" class="site-main">
        <?php
            $user_id = get_current_user_id();
            $total_score = calculate_user_total_score($user_id);
            // Memasang dalam tag <iframe>
            if($score_modul < $total_score) {
                if ($video_id) {
                    $embed_url = "https://www.youtube.com/embed/$video_id";
                    echo '<div class="video-container"><iframe src="' . esc_url($embed_url) . '" style="width:100%; border-radius: 10px;border:none;"></iframe></div>';
                }
            } else {
                ?>
                <div style="text-align:center;">
                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script><lottie-player src="https://lottie.host/37eddff0-50b8-4cae-8ad9-f8533727a3bb/XqytbiWQjx.json" background="##FFFFFF" speed="1" style="width: 300px; height: 300px;margin:0 auto;" loop autoplay direction="1" mode="normal"></lottie-player>
                    <div class="wss-text-light">
                        Jawab lebih banyak questionnaire untuk melihat modul ini <a class="wss-btn-warning" href="?page=questionnaire">Questionnaire</a>
                    </div>
                </div>
                <?php
            }
        ?>
        </main><!-- #main -->
    </div>
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css">
    <?php
    return ob_get_clean();
}
