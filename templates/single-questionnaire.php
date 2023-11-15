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
                    $i = 1;
                    echo '<form class="questionnaire-frame wss-mb-2" data-id="'.$post_id.'">';
                    foreach($qnas as $qna){
                        $j = $i++;
                        ?>
                        <div class="wss-card wss-question-frame wss-mb-2">
                            <h3 class="question-title"><?php echo $qna['_cmb2_qa_group_question']; ?></h3>
                            <div class="wsss-d-flex wsss-flex-wrap">
                                <?php
                                foreach($options as $option){
                                    ?>
                                    <div class="wss-w-50 d-flex wss-p-1">
                                        <input class="wss-d-none" id="<?php echo $option.$post_id.$j; ?>" type="radio" name="<?php echo 'qna_'.$post_id.$j; ?>" value="<?php echo $option; ?>" aria-label="Checkbox for following text input">
                                        <label class="wss-pl-2 wss-d-block wss-cursor-pointer" for="<?php echo $option.$post_id.$j; ?>">
                                            <span><?php echo $qna['_cmb2_qa_group_answer_'.$option]; ?></span>
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
                 </form>
 
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
 