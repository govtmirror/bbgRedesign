<?php
/**
 * The custom home page for the Broadcasting Board of Governors.
 * It includes the mission, a portfolio of recent projects, recent blog posts and staff.
 *
 * @package bbgRedesign
  template name: About
 */

$templateName = "about";

get_header();

?>

<div id="main" class="site-main">

	<div id="primary" class="content-area">
		<main id="bbg-home" class="site-content bbg-home-main" role="main">
			<?php
				$data = get_theme_mod('header_image_data');
				$attachment_id = is_object($data) && isset($data->attachment_id) ? $data->attachment_id : false;
				if($attachment_id) {
					$tempSources= bbgredesign_get_image_size_links($attachment_id);
					//sources aren't automatically in numeric order.  ksort does the trick.
					ksort($tempSources);
					$counter=0;
					$prevWidth=0;
					// Let's prevent any images with width > 1200px from being an output as part of responsive post cover
					foreach( $tempSources as $key => $tempSource ) {
						if ($key > 1900) {
							unset($tempSources[$key]);
						}
					}
					echo "<style>";
					foreach( $tempSources as $key => $tempSourceObj ) {
						$counter++;
						$tempSource=$tempSourceObj['src'];
						if ($counter == 1) {
							echo "\t.bbg-banner { background-image: url($tempSource) !important; }\n";
						} elseif ($counter < count($tempSources)) {
							echo "\t@media (min-width: " . ($prevWidth+1) . "px) and (max-width: " . $key . "px) {\n";
							echo "\t\t.bbg-banner { background-image: url($tempSource) !important; }\n";
							echo "\t}\n";
						} else {
							echo "\t@media (min-width: " . ($prevWidth+1) . "px) {\n";
							echo "\t\t.bbg-banner { background-image: url($tempSource) !important; }\n";
							echo "\t}\n";
						}
						$prevWidth=$key;
					}
					echo "</style>";
				}
				//style="background-image:url(<?php echo get_header_image();
			?>
			<section class="bbg-banner">
				<div class="usa-grid bbg-banner__container">
					<a href="<?php echo site_url(); ?>">
						<img class="bbg-banner__site-logo" src="<?php echo get_template_directory_uri() ?>/img/logo-agency-square.png" alt="BBG logo">
					</a>
					<div class="bbg-banner-box">
						<h1 class="bbg-banner-site-title"><?php echo bbginnovate_site_name_html(); ?></h1>
						<?php
						/*
						$description = get_bloginfo( 'description', 'display' );
						if ( $description || is_customize_preview() ) : ?>
							<h3 class="bbg-banner-site-description usa-heading-site-description"><?php echo $description; ?></h3>
						<?php endif;
						*/
						?>

					</div>

					<div class="bbg-social__container">
						<div class="bbg-social">
						</div>
					</div>
				</div>
			</section>


			<!-- Page header -->
			<div class="usa-grid">

				<?php if($post->post_parent) {
					//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
					$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
					$parent_link = get_permalink($post->post_parent);
				?>
					<h5 class="entry-category bbg-label"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>

				<?php } ?>

				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->
			</div>

			<!-- Page introduction -->
			<?php
				$page = get_page_by_title('BBG overview');
				$qParams=array(
					'post_type' => array('page'),
					'posts_per_page' => 1,
					'page_id' => $page->ID
				);
				query_posts($qParams);

				$pageIntro = "";
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						$pageContent = get_the_content();
						$pageContent = apply_filters('the_content', $pageContent);
						$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
					endwhile;
				endif;
				wp_reset_query();
			?>

			<section class="usa-section usa-grid">
				<div class="bbg-grid__container">
					<?php
						echo $pageContent;
					?>
				</div>
			</section>
			<!-- /Page introduction -->


			<!-- Portfolio -->
			<section id="projects" class="usa-section bbg-portfolio">
				<div class="usa-grid">
					<h6 class="bbg-label"><a href="/blog/category/press-release/">Around the BBG</a></h6>

					<div class="usa-grid-full">
					<?php
						$qParams=array(
							'post_type' => array('post'),
							'posts_per_page' => 3,
							'orderby' => 'post_date',
							'order' => 'desc',
							'cat' => get_cat_id('Press Release')
						);
						query_posts($qParams);

						if ( have_posts() ) :
							while ( have_posts() ) : the_post();
								$gridClass = "bbg-grid--1-3-3";
								get_template_part( 'template-parts/content-portfolio', get_post_format() );
							endwhile;
						endif;
						wp_reset_query();

					?>
					</div><!-- .usa-grid-full -->

					<a href="/blog/category/press-release/">View all press releases »</a>

				</div><!-- .usa-grid -->
			</section><!-- .bbg-portfolio -->


			<!-- Recent posts -->
			<section id="recent-posts" class="usa-section">
				<div class="usa-grid">
					<h6 class="bbg-label"><a href="<?php echo get_permalink( get_page_by_path( 'blog' ) ) ?>">Recent posts</a></h6>
				</div>

				<div class="usa-grid-full">

				<?php
					/* NOTE: if there is a sticky post, we may wind up with an extra item.
					So we hardcode the display code to ignore anything after the 3rd item */
					$maxPostsToShow=3;
					$qParams=array(
						'post_type' => array('post'),
						'posts_per_page' => $maxPostsToShow,
						'orderby' => 'post_date',
						'order' => 'desc',
						'category__not_in' => (array(get_cat_id('Site Introduction'),
													get_cat_id("John's take"),
													get_cat_id('Contact')
											))
					);
					query_posts($qParams);

					if ( have_posts() ) :
						$counter=0;
						while ( have_posts() ) : the_post();
							$counter++;
							if ($counter == 1) {
								get_template_part( 'template-parts/content-excerpt-featured', get_post_format() );
								echo '<div class="usa-grid">';
							}
							else if ($counter <= $maxPostsToShow) {
								$gridClass = "bbg-grid--1-2-2";
								$includeImage = FALSE;
								get_template_part( 'template-parts/content-excerpt', get_post_format() );
							}
						endwhile;
						echo '</div><!-- .usa-grid-full -->';
					endif;
					wp_reset_query();
				?>
				</div>
			</section><!-- Recent posts -->


			<section id="teams" class="usa-section bbg-staff">
				<div class="usa-grid">
					<h6 class="bbg-label"><a href="<?php echo get_permalink( get_page_by_path( 'broadcasters' ) ); ?>" title="A list of the BBG broadcasters.">Our broadcasters</a></h6>

					<div class="usa-intro bbg__broadcasters__intro">
						<h3 class="usa-font-lead">Every week, more than 226 million listeners, viewers and Internet users around the world turn on, tune in and log onto U.S. international broadcasting programs. The day-to-day broadcasting activities are carried out by the individual BBG international broadcasters</h3>
					</div>

					<div class="usa-grid-full">

							<?php
								$entityParentPage = get_page_by_path('broadcasters');
								$qParams=array(
									'post_type' => array('page'),
									'posts_per_page' => -1,
									'post_parent' => $entityParentPage->ID,
									'orderby' => 'meta_value_num',
									'meta_key' => 'entity_year_established',
									'order' => 'ASC'
								);
								$custom_query = new WP_Query($qParams);
								if ($custom_query -> have_posts()) {
									while ( $custom_query -> have_posts() )  {
										$custom_query->the_post();
										$id=get_the_ID();
										$fullName=get_post_meta( $id, 'entity_full_name', true );
										if ($fullName != "") {
											$abbreviation=strtolower(get_post_meta( $id, 'entity_abbreviation', true ));
											$abbreviation=str_replace("/", "",$abbreviation);
											$description=get_post_meta( $id, 'entity_description', true );
											$link=get_permalink( get_page_by_path( "/broadcasters/$abbreviation/" ) );
											$imgSrc=get_template_directory_uri().'/img/logo_'.$abbreviation.'--circle-200.png'; //need to fix this

											echo '<article class="bbg__entity bbg-grid--1-1-1-2">';
											echo '<div class="bbg-avatar__container bbg__entity__icon">';
											echo '<a href="'.$link.'" tabindex="-1">';
											echo '<div class="bbg-avatar bbg__entity__icon__image" style="background-image: url('.$imgSrc.');"></div>';
											echo '</a></div>';
											echo '<div class="bbg__entity__text">';
											echo '<h2 class="bbg__entity__name"><a href="'.$link.'">'.$fullName.'</a></h2>';
											echo '<p class="bbg__entity__text-description">'.$description.'</p>';
											echo '</div>';
											echo '</article>';
										}
										}

								}
								wp_reset_postdata();
							?>
					</div>
					<a href="<?php echo get_permalink( get_page_by_path( 'about-the-agency/history/' ) ); ?>">Learn more about the history of USIM »</a>
				</div>
			</section>


		</main>
	</div><!-- #primary .content-area -->
	<div id="secondary" class="widget-area" role="complementary">
	</div><!-- #secondary .widget-area -->
</div><!-- #main .site-main -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>