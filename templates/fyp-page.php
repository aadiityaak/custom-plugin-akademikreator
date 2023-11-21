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

function wss_fyp_page(){
    ob_start();
    $magic_elementor_lite_blog_layout = get_theme_mod('magic_elementor_blog_layout', 'rightside');
    $magic_elementor_lite_blog_style = get_theme_mod('magic_elementor_lite_blog_style', 'grid');
    global $post;
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = array(
        'post_type'      => 'mpcs-course',
        'paged'          => $paged,    // Current page number
        'posts_per_page' => 6,      // Number of posts per page

        // Tambahan argumen lain sesuai kebutuhan
    );
    
    $questionnaire_query = new WP_Query( $args );
    ?>
    

    <div class="wss-container mg-main-blog wss-pt-2 wss-mb-2">
        <main id="primary" class="site-main">

            <?php 
            if ($questionnaire_query->have_posts()) : ?>
                <div class="columns mpcs-cards">
                    <?php
                    /* Start the Loop */
                    while ($questionnaire_query->have_posts()) :
                        $questionnaire_query->the_post();
                        $post_id = $post->ID;
                        $url = '?page=single-questionnaire&id='.$post_id;
                        ?>
                        
                        <div class="column col-4 col-sm-12">
                            <div class="card s-rounded">
                            <div class="card-image">
                                <a href="<?php echo $url; ?>" class="wss-ratio wss-ratio-3x4" alt="<?php the_title_attribute(); ?>">
                                <img src="
                                <?php 
                                $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array('220','220'), true );
                                $thumbnail_url = $thumbnail_url[0];
                                echo $thumbnail_url;
                                ?>" alt="<?php echo get_the_title(); ?>"/>
                                </a>
                            </div>
                            <div class="card-header">
                                <div class="card-title">
                                <h2 class="h5"><a href="<?php echo $url; ?>"><?php the_title() ?></a></h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php the_excerpt() ?>
                            </div>
                            <div class="card-footer">
                                <span class="course-author">
                                <a href="#"><?php echo get_the_author_ID(); ?></a>
                                </span>

                                <!-- <span class="price float-right">$15.00/m</span> -->
                                <div class="mpcs-progress-ring" data-value="5" data-color="red">
                                    <div class="inner">
                                    <div class="stat">4</div>
                                    </div>
                                </div>

                            </div>
                            </div>
                        </div>
                        
                        <?php

                    endwhile;

                    ?>
                </div>
            <?php
                the_posts_pagination();

            else :

                ?>
                <div style="text-center">
                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script><lottie-player src="https://lottie.host/1bdea896-5213-481a-95c2-7fc115a5dbf4/LBiUESn3sp.json" background="##FFFFFF" speed="1" style="width: 300px; height: 300px" loop autoplay direction="1" mode="normal"></lottie-player>
                </div>
                <?php

            endif;
            ?>

        </main><!-- #main -->
    </div>
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );?>../public/css/style.min.css">
    <?php
    return ob_get_clean();
}