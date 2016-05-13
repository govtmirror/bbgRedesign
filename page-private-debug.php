<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Debugger
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="usa-grid-full">
				<?php 
					$templates =  wp_get_theme()->get_page_templates();
					foreach ( $templates as $template_filename => $template_name ) {
						echo "<h3>$template_name ($template_filename)</h3>";
						$args = array(
					        'post_type' => 'page',
					        'posts_per_page' => 9999,
					        'meta_query' => array(
					            array(
					                'key' => '_wp_page_template',
					                'value' => $template_filename, // template name as stored in the dB
					            )
					        ),
					    );
						$custom_query = new WP_Query($args);
						if ($custom_query -> have_posts()) {
							echo "<ul style='margin-left:20px'>";
							while ( $custom_query -> have_posts() )  {
								$custom_query -> the_post();
								echo "<li><a target='_blank' href='" . get_permalink(get_the_id()) . "'>".get_the_title()."</a></li>";
							}
							echo "</ul>";
						}
						wp_reset_postdata();
					}
				 ?>
			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
