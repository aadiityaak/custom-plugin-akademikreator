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

$magic_elementor_lite_blog_layout = get_theme_mod('magic_elementor_blog_layout', 'rightside');
$magic_elementor_lite_blog_style = get_theme_mod('magic_elementor_lite_blog_style', 'grid');
global $post;
?>

<div class="wss-container mg-main-blog wss-pt-2 wss-mb-2">
    <main id="primary" class="site-main">

        <?php if (have_posts()) : ?>
            <div class="wss-d-flex wsss-flex-wrap">
                <?php
                /* Start the Loop */
                while (have_posts()) :
                    the_post();
                    ?>
                    
                    <div class="wss-w-50 wss-w-md-25 wss-position-relative wss-p-1">
                        <a class="wss-d-block wss-card" href="<?php echo get_the_permalink(); ?>">
                            <div class="wss-ratio wss-ratio-3x4">
                                <img src="
                                <?php 
                                $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array('220','220'), true );
                                $thumbnail_url = $thumbnail_url[0];
                                echo $thumbnail_url;
                                ?>" alt="<?php echo get_the_title(); ?>"/>
                            </div>

                            <span class="wss-floating-badge">
                                <?php 
                                $first_term_name = get_the_terms( $post->ID, 'category_questionnaire' )[0]->name;
                                echo $first_term_name ;
                                ?>
                            </span>
                            <h3 class="wss-pt-1">
                                <?php echo get_the_title(); ?>
                            </h3>
                        </a>
                    </div>
                    
                    <?php

                endwhile;

                ?>
            </div>
        <?php
            the_posts_pagination();

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>

    </main><!-- #main -->
</div>

<?php
