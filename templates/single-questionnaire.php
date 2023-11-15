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

 if ('elementor_library' == get_post_type()) {
     get_template_part('template-parts/elementor', 'template');
     return;
 }
 
 
 $magic_elementor_blog_container = get_theme_mod('magic_elementor_blog_container', 'mg-wrapper');
 $magic_elementor_blog_layout = get_theme_mod('magic_elementor_blog_layout', 'rightside');
 
 if (is_active_sidebar('sidebar-1') && $magic_elementor_blog_layout != 'fullwidth' && 'post' == get_post_type()) {
     $magic_elementor_blog_column = 'mg-grid-9';
 } else {
     $magic_elementor_blog_column = 'mg-grid-12';
 }
 get_header();
 ?>
 
 
 
 <div class="<?php echo esc_attr($magic_elementor_blog_container); ?> mg-main-blog nxsingle-post">
     <div class="mg-flex">
         <?php if (is_active_sidebar('sidebar-1') && $magic_elementor_blog_layout == 'leftside' && 'post' === get_post_type()) : ?>
             <div class="mg-grid-3">
                 <?php get_sidebar(); ?>
             </div>
         <?php endif; ?>
         <div class="<?php echo esc_attr($magic_elementor_blog_column); ?>">
             <main id="primary" class="site-main">
 
                 <?php
                 while (have_posts()) :
                     the_post();
                    $post_id = get_the_ID();
                    $qnas = get_post_meta($post_id, '_cmb2_qa_group_qa_group', true);
                    $options = ['a','b','c','d'];
                    foreach($qnas as $qna){
                        ?>
                        <div class="wss-card">
                            <h3><?php echo $qna['_cmb2_qa_group_question']; ?></h3>
                            <div class="wsss-d-flex wsss-flex-wrap">
                                <?php
                                foreach($options as $option){
                                    ?>
                                    <div class="wss-w-50">
                                        <?php echo $qna['_cmb2_qa_group_answer_'.$option]; ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                     echo '<pre>';
                     print_r($qna);
                     echo '<pre>';
 
                     the_post_navigation(
                         array(
                             'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'magic-elementor') . '</span> <span class="nav-title">%title</span>',
                             'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'magic-elementor') . '</span> <span class="nav-title">%title</span>',
                         )
                     );
 
                     // If comments are open or we have at least one comment, load up the comment template.
                     if (comments_open() || get_comments_number()) :
                         comments_template();
                     endif;
 
                 endwhile; // End of the loop.
                 ?>
 
             </main><!-- #main -->
         </div>
         <?php if (is_active_sidebar('sidebar-1') && $magic_elementor_blog_layout == 'rightside' && 'post' === get_post_type()) : ?>
             <div class="mg-grid-3">
                 <?php get_sidebar(); ?>
             </div>
         <?php endif; ?>
     </div>
 </div>
 
 <?php
 get_footer();
 