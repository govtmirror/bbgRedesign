<?php
/**
 * This is the template that displays the BBG entity pages.
 * VOA, RFE/RL, OCB, RFA and MBN
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Entity
 */

$pageContent = "";
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$pageContent = get_the_content();
		$pageContent = apply_filters('the_content', $pageContent);
		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
		$ogDescription = get_the_excerpt(); //get_the_excerpt()
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

$id = $post->ID;

$fullName = get_post_meta( $id, 'entity_full_name', true );
$abbreviation = get_post_meta( $id, 'entity_abbreviation', true );
$description = get_post_meta( $id, 'entity_description', true );
$siteUrl = get_post_meta( $id, 'entity_site_url', true );
$rssFeed = get_post_meta( $id, 'entity_rss_feed', true );
$entityLogoID = get_post_meta( $id, 'entity_logo',true );
$websiteName = get_post_meta( $id, 'entity_website_name', true );
$entityLogo = "";
if ($entityLogoID) {
	$entityLogoObj = wp_get_attachment_image_src( $entityLogoID , 'Full');
	$entityLogo = $entityLogoObj[0];
}

$awardSlug = get_post_meta( $id, 'entity_award_recipient_taxonomy_slug', true );
$entityApiID = get_post_meta( $id, 'entity_api_id', true );
$entityCategorySlug = get_post_meta( $id, 'entity_category_slug', true );
$entityCategoryObj = get_category_by_slug($entityCategorySlug);
$entityAwardsPageLink = get_permalink( get_page_by_path( 'awards' ) );
$entityAwardsLinkFiltered = add_query_arg('entity', $awardSlug, $entityAwardsPageLink);

$entityMission = get_post_meta( $id, 'entity_mission', true );
$subgroups = getEntityLinks($entityApiID);

$siteSelect = "<h3 class='bbg__article-sidebar__list-label'>Explore the $abbreviation websites</h3>";
if (count($subgroups) < 4) {
	$siteSelect .= "<ul class='bbg__rss__list'>";
	foreach ($subgroups as $s) {
		if ($s->website_url != "") { // EX: mbn digital
			$siteSelect .= "<li class='bbg__rss__list-link'><a target='_blank' href='" . $s->website_url . "'>" . $s->name . "</a></li>";
		}
	}
	$siteSelect .= "</ul>";
} else {
	$siteSelect .= "<select name='entity_sites' id='entity_sites'>";
	$siteSelect .= "<option>Select a service</option>";
	foreach ( $subgroups as $s ) {
		if ( $s->website_url != "" ) { // EX: mbn digital
			$siteSelect .= "<option value='" . $s->website_url . "'>" . $s->name . "</option>";
		}
	}
	$siteSelect .= "</select><button class='usa-button' id='entityUrlGo'>Go</button>";
}

//Entity fast facts / by-the-numbers
$budget = get_post_meta( $id, 'entity_budget', true );
$employees = get_post_meta( $id, 'entity_employees', true );
$languages = get_post_meta( $id, 'entity_languages', true );
$audience = get_post_meta( $id, 'entity_audience', true );
$appLink = get_post_meta( $id, 'entity_mobile_apps_link', true );
$primaryLanguage = get_post_meta( $id, 'entity_primary_language', true );

if ( $budget != "" ) {
	$budget = '<li><span class="bbg__article-sidebar__list-label">Annual budget: </span>' . $budget . '</li>';
}
if ( $employees != "" ) {
	$employees = number_format( floatval( $employees ), 0, '.', ',' );
	$employees = '<li><span class="bbg__article-sidebar__list-label">Employees: </span>' . $employees . '</li>';
}
if ( $languages != "" ) {
	if ($languages == "1"){
		$languages = '<li><span class="bbg__article-sidebar__list-label">Language supported: </span>' . $primaryLanguage . '</li>';
	} else {
		$languages = '<li><span class="bbg__article-sidebar__list-label">Languages supported: </span>' . $languages . '</li>';
	}
}
if ( $audience != "" ) {
	$audience = '<li><span class="bbg__article-sidebar__list-label">Audience estimate: </span>' . $audience . '</li>';
}
if ( $appLink != "" ) {
	$appLink = '<h3><a href="https://www.bbg.gov/apps/" class="bbg__article-sidebar__list-label">Download the apps </a></h3><p style="font-family: sans-serif; margin-top: 0;">' . $appLink . '</p>';
}

//Social + contact links
$twitterProfileHandle = get_post_meta( $id, 'entity_twitter_handle', true );
$facebook = get_post_meta( $id, 'entity_facebook', true );
$instagram = get_post_meta( $id, 'entity_instagram', true );


//Contact information
$email = get_post_meta( $id, 'entity_email', true );
$phone = get_post_meta( $id, 'entity_phone', true );
$street = get_post_meta( $id, 'entity_street', true );
$city = get_post_meta( $id, 'entity_city', true );
$state = get_post_meta( $id, 'entity_state', true );
$zip = get_post_meta( $id, 'entity_zip', true );
$learnMore = get_post_meta( $id, 'entity_learn_more', true );
$address = "";
$map = "";
$mapLink = "";
$includeContactBox = FALSE;

if ( $email != "" ) {
	$email = '<li><span class="bbg__list-label">Email: </span><a href="mailto:' . $email . '" title="Email ' . $abbreviation . '">' . $email . '</a></li>';
}

if ( $phone != "" ) {
	$phone = '<li><span class="bbg__list-label">Tel: </span>' . $phone . '</li>';
}

if ( $learnMore != "" ) {
	$learnMore = '<li><a href="'. $learnMore . '">Learn more</a> about ' . $abbreviation . '</li>';
}


if ( $street != "" && $city!= "" && $state != "" && $zip != "" ) {
	$address = $street . '<br/>' . $city . ', ' . $state . ' ' . $zip;

	//Strip spaces for url-encoding.
	$street = str_replace( " ", "+", $street );
	$city = str_replace( " ", "+", $city );
	$state = str_replace( " ", "+", $state );
	$size = 400;
	$zoom = 14;
	$map = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $street . ',+' . $city . ',+' . $state . '+' . $zip . "&zoom=" . $zoom . "&scale=false&size=" . $size . "x" . $size . "&maptype=roadmap&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:1%7C".$street.',+'.$city.',+'.$state . ');';
	$mapLink = 'https://www.google.com/maps/place/' . $street . ',+' . $city . ',+' . $state . '+' . $zip . '/';
	//$includeMap = "bbg__contact-card--include-map";

	$address = '<p><a href="'. $mapLink . '">' . $address . '</a></p>';
}

/* Add a map */
$includeMap = get_post_meta( get_the_ID(), 'map_include', true );
if ( $includeMap ) {
	$mapLocation = get_post_meta( get_the_ID(), 'map_location', true );
	$mapHeadline = get_post_meta( get_the_ID(), 'map_headline', true );
	$mapDescription = get_post_meta( get_the_ID(), 'map_description', true );
	$mapPin = get_post_meta( get_the_ID(), 'map_pin', true );
	$mapZoom = get_post_meta( get_the_ID(), 'map_zoom', true );

	$key = '<?php echo MAPBOX_API_KEY; ?>';
	$zoom = 8;
	if ( $mapZoom > 0 && $mapZoom < 20 ) {
		$zoom = $mapZoom;
	}

	$lat = $mapLocation['lat'];
	$lng = $mapLocation['lng'];
	$pin = "";

	if ( $mapPin ){
		$pin = "pin-s+990000(" . $lng .",". $lat .")/";
	}
	//Static map like this:
	//$map = "https://api.mapbox.com/v4/mapbox.emerald/" . $pin . $lng . ",". $lat . "," . $zoom . "/170x300.png?access_token=" . $key;
}

if ( $address != "" || $phone != "" || $email != "" ){
	$includeContactBox = TRUE;
}

//Default adds a space above header if there's no image set
$featuredImageClass = " bbg__article--no-featured-image";
$bannerPosition = get_post_meta( $id, 'adjust_the_banner_image', true );
$videoUrl = "";

/**** BEGIN CREATING rssItems array *****/
$entityJson = getFeed($rssFeed,$id);
$rssItems = array();
$itemContainer = false;
$languageDirection = "";

if ( property_exists($entityJson, 'channel') && property_exists($entityJson->channel, 'item') ) {
	$itemContainer = $entityJson->channel;
} else {
	$itemContainer = $entityJson;
}
if ($itemContainer) {
	if (property_exists($itemContainer, 'language')) {
		if ($itemContainer -> language == "ar"){
			$languageDirection = " rtl";
		}
	}
	foreach ($itemContainer -> item as $e) {
		$title = $e -> title;
		$url = $e -> link;
		$description = $e -> description;
		$enclosureUrl = "";
		if (property_exists($e, 'enclosure') && property_exists($e -> enclosure, '@attributes') && property_exists($e -> enclosure -> {'@attributes'}, 'url') ) {
			$enclosureUrl = ( $e -> enclosure -> {'@attributes'} -> url );
		}
		$rssItems[] = array( 'title' => $title, 'url' => $url, 'description' => $description, 'image' => $enclosureUrl );
	}
}
/**** DONE CREATING rssItems array *****/

/**** START FETCH related press releases ****/
$prCategorySlug = get_post_meta( $id, 'entity_pr_category', true );
$pressReleases = array();
if ( $prCategorySlug != "" ) {
	$prCategoryObj = get_category_by_slug( $prCategorySlug );
	if ( is_object($prCategoryObj) ) {
		$prCategoryID = $prCategoryObj -> term_id;
		$qParams = array(
			'post_type' => array( 'post' ),
			'posts_per_page' => 5,
			'category__and' => array( $prCategoryID ),
			'orderby', 'date',
			'order', 'DESC',
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => 'post-format-quote',
					'operator' => 'NOT IN'

				)
			),
			'category__not_in' => get_cat_id( 'Award' )
		);
		$custom_query = new WP_Query( $qParams );
		if ( $custom_query -> have_posts() ) {
			while ( $custom_query -> have_posts() )  {
				$custom_query -> the_post();
				$id = get_the_ID();
				$pressReleases[] = array( 'url' => get_permalink($id), 'title' => get_the_title($id), 'excerpt' => get_the_excerpt());
			}
		}
		wp_reset_postdata();
	}
}
$s = "";
if ( count($pressReleases) ) {
	//$s.= '<h2>Recent '. $abbreviation .' press releases</h2>';
	foreach ( $pressReleases as $pr ) {
		$url = $pr['url'];
		$title = $pr['title'];
		$s .= '<div class="bbg__post-excerpt">';
		$s .= '<h3><a href="' . $url . '">' . $title . '</a></h3>';
		$s .= '<p>' . $pr['excerpt'] . '</p>';
		$s .= '</div>';
	}
	$s .= '<div class="usa-grid-full u--space-below-mobile--large">';
	$entityCategoryLink = get_category_link( $entityCategoryObj -> term_id );
	$s .= "<a href='$entityCategoryLink'>View all " . $abbreviation . " highlights »</a>";
	$s .= "</div>";
}
$pageContent = str_replace( "[press releases]", $s, $pageContent );
/**** END FETCH related press releases ****/

/**** START FETCH AWARDS ****/
$awards = array();
$qParams = array(
	'posts_per_page' => 5,
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_key' => 'standardpost_award_recipient',
	'meta_value' => $awardSlug
);
$custom_query = new WP_Query($qParams);
if ($custom_query -> have_posts()) {
	while ( $custom_query -> have_posts() )  {
		$custom_query -> the_post();
		$id = get_the_ID();

		$awardYears  = get_post_meta( $id, 'standardpost_award_year' );
		$awardTitle = get_post_meta( $id, 'standardpost_award_title', true );
		$orgTerms = get_field( 'standardpost_award_organization', $id );
	    $organizations = array();
	    $organizations[] = $orgTerms -> name;


		$recipients = get_post_meta( $id, 'standardpost_award_recipient' );
		$awards[] = array(
			'id'=>$id,
			'url'=>get_permalink($id),
			'title'=> get_the_title($id),
			'excerpt'=> get_the_excerpt(),
			'awardYears'=> $awardYears,
			'awardTitle'=> $awardTitle,
			'organizations'=> $organizations,
			'recipients'=> $recipients
		);
	}
}
wp_reset_postdata();
$s = "";
if ( count($awards) ) {
	//$s.= '<h2>Recent '. $abbreviation .' Awards</h2>';
	foreach ( $awards as $a ) {
		$id = $a['id'];
		$url = $a['url'];
		$title = $a['title'];
		$awardYears = $a['awardYears'];
		$awardTitle = $a['awardTitle'];
		$organizations = $a['organizations'];
		$recipients = $a['recipients'];

		$s .= '<div class="bbg__post-excerpt bbg__award__excerpt">';
		$s .= '<h3 class="bbg__award-excerpt__title"><a href="' . $url . '">' . $title . '</a></h3>';
		$s .= '<p class="bbg__award-excerpt__source">';
		$s .= '<span class="bbg__award-excerpt__org">' . $awardTitle . ', ' . join($organizations) . '</span> (';
		$s .= join( $awardYears );
		$s .= ')</p>';
		$s .= '<p>' . $a['excerpt'] . '</p>';
		$s .= '</div>';
	}
	$s .= '<div class="usa-grid-full u--space-below-mobile--large">';
	$s .= "<a href='$entityAwardsLinkFiltered'>View all " . $abbreviation . " awards »</a>";
	$s .= "</div>";
}
$pageContent = str_replace( "[awards]", $s, $pageContent );
/**** END FETCH AWARDS ****/

/**** START FETCH threats to press ****/
$threats = array();
$threatsCategoryObj = get_category_by_slug( "threats-to-press" );
$threatsCategoryID=$threatsCategoryObj->term_id;
if ($entity_category_slug != "") {
	if (is_object($entityCategoryObj)) {
		$entityCategoryID=$entityCategoryObj->term_id;
		$qParams=array(
			'post_type' => array('post'),
			'posts_per_page' => 3,
			'category__and' => array(
									$entityCategoryID,
									$threatsCategoryID
							  ),
			'orderby', 'date',
			'order', 'DESC',
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => 'post-format-quote',
					'operator' => 'NOT IN'

				)
			)
		);
		$custom_query = new WP_Query($qParams);
		if ($custom_query -> have_posts()) {
			while ( $custom_query -> have_posts() )  {
				$custom_query->the_post();
				$id=get_the_ID();
				$threats[]=array('url'=>get_permalink($id), 'title'=> get_the_title($id), 'excerpt'=>get_the_excerpt(), 'thumb'=>get_the_post_thumbnail( $id, 'small-thumb' ));
			}
		}
		wp_reset_postdata();
	}
}
/**** END FETCH threats to press ****/

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div class="usa-grid-full">

				<?php while ( have_posts() ) : the_post();
					//$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class("bbg__article"); ?>>
						<div class="usa-grid">
						<?php if($post->post_parent) {
							//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
							$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
							$parent_link = get_permalink($post->post_parent);
							?>
							<h5 class="bbg__label--mobile large"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>
						<?php } ?>
						</div>

						<?php
							$hideFeaturedImage = FALSE;
							if ($videoUrl != "") {
								echo featured_video($videoUrl);
								$hideFeaturedImage = TRUE;
							} elseif ( has_post_thumbnail() && ( $hideFeaturedImage != 1 ) ) {
								echo '<div class="usa-grid-full">';
								$featuredImageClass = "";
								$featuredImageCutline = "";
								$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
								if ($thumbnail_image && isset($thumbnail_image[0])) {
									$featuredImageCutline = $thumbnail_image[0]->post_excerpt;
								}

								$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 700,450 ), false, '' );

								echo '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner--profile" style="background-image: url('.$src[0].'); background-position: '.$bannerPosition.'">';
								echo '</div>';
								echo '</div> <!-- usa-grid-full -->';
							}
						?><!-- .bbg__article-header__thumbnail -->





						<div class="usa-grid">

							<?php echo '<header class="entry-header bbg__article-header'.$featuredImageClass.'">'; ?>

								<div class="bbg__profile-photo">
									<img src="<?php echo $entityLogo; ?>" class="bbg__profile-photo__image"/>
								</div>
								<div class="bbg__profile-title">

									<?php echo '<h1 class="entry-title bbg__article-header__title">' . $fullName . '</h1>'; ?>

									<h5 class="entry-category bbg__profile-tagline">
										<?php echo $websiteName; ?>
									</h5><!-- .bbg__label -->

								</div>

							</header><!-- .entry-header ------------------------------------------------- -->



							<div class="bbg__article-sidebar--left">

								<h3 class="bbg__sidebar-label bbg__contact-label">Share </h3>
								<ul class="bbg__article-share">
									<li class="bbg__article-share__link facebook">
										<a href="<?php echo $fbUrl; ?>">
											<span class="bbg__article-share__icon facebook"></span>
										</a>
									</li>
									<li class="bbg__article-share__link twitter">
										<a href="<?php echo $twitterURL; ?>">
											<span class="bbg__article-share__icon twitter"></span>
										</a>
									</li>
								</ul>

							</div>




							<div class="entry-content bbg__article-content largeXXX <?php echo $featuredImageClass; ?>">
								<div class="bbg__profile__content">
								<?php /*echo $headlineStr;*/ ?>

								<?php /*the_content();*/ ?>
								<?php echo $pageContent; ?>

								</div>

							</div><!-- .entry-content -->

							<div class="bbg__article-sidebar large">
									<?php if ($entityMission!=""){ ?>
									<aside class="bbg__article-sidebar__aside">
										<!--<h3 class="bbg__sidebar-label"><?php echo $abbreviation; ?> Mission</h3>-->
										<p><?php echo $entityMission; ?></p>
									</aside>
									<?php } ?>

									<aside class="bbg__article-sidebar__aside">
										<?php
										if ($budget != "" || $employees != "" || $languages != "" || $audience != "" || $appLink != "") {
											echo '<h3 class="bbg__sidebar-label">Fast facts</h3>';
										} ?>

										<ul class="bbg__article-sidebar__list--labeled">
											<?php
												echo $budget;
												echo $employees;
												echo $languages;
												echo $audience;
											?>
										</ul>
									</aside>

									<?php
									if ($facebook!="" || $twitterProfileHandle!="" || $instagram!=""){
									?>
										<aside class="bbg__article-sidebar__aside">
											<h3 class="bbg__sidebar-label bbg__contact-label"><?php echo $abbreviation; ?> social media </h3>
											<ul class="bbg__article-share">
												<?php
													if ($facebook!=""){
														echo '<li class="bbg__article-share__link facebook"><a href="'.$facebook.'" title="Like '.get_the_title().' on Facebook"><span class="bbg__article-share__icon facebook"></span><span class="bbg__article-share__text">Facebook</span></a></li>';
													}
													if ($twitterProfileHandle!=""){
														echo '<li class="bbg__article-share__link twitter"><a href="https://twitter.com/'.$twitterProfileHandle.'" title="Follow '.get_the_title().' on Twitter"><span class="bbg__article-share__icon twitter"></span><span class="bbg__article-share__text">@'.$twitterProfileHandle.'</span></a></li>';
													}
													if ($instagram!=""){
														echo '<li class="bbg__article-share__link instagram"><a href="https://instagram.com/'.$instagram.'" title="Follow '.get_the_title().' on Instagram"><span class="bbg__article-share__icon instagram"></span><span class="bbg__article-share__text">Instagram</span></a></li>';
													}
												?>
											</ul>
										</aside>
									<?php } ?>

									<aside class="bbg__article-sidebar__aside">
										<?php
											echo $appLink;
										?>
									</aside>

									<?php
										if (count($rssItems)) {
											echo '<aside class="bbg__article-sidebar__aside">';
											echo '<h3 class="bbg__sidebar-label">Recent stories from ' . $websiteName . '</h3>';
											echo '<ul class="bbg__rss__list'. $languageDirection .'">';
											$maxRelatedStories=3;
											for ( $i = 0; $i < min( $maxRelatedStories, count($rssItems) ); $i++) {
												$o = $rssItems[$i];
												echo '<li class="bbg__rss__list-link">';
												echo '<a href="' . $o['url'] . '">';
												if ($o['image'] != "") {
													echo "<img src='". $o['image'] . "'/>";
												}
												echo $o['title'] . '</a>';
												echo '</li>';
											}
											echo '</ul><!-- rss feed -->';
											echo '</aside>';

											if (count($threats)) {
												$maxThreatsStories=3;
												echo '<aside class="bbg__article-sidebar__aside">';
												echo '<h6 class="bbg__label small"><a href="/threats-to-press/">Threats to Press</a></h6>';
												echo '<ul class="bbg__rss__list">';
												for ( $i = 0; $i <min( $maxRelatedStories, count($threats) ); $i++) {
													$o = $threats[$i];
													echo '<li class="bbg__rss__list-link">';
													echo '<a href="' . $o['url'] . '">';
													/*
													if ($o['thumb'] != "") {
														echo $o['thumb'];
													//	echo "<img src='". $o['image'] . "'/>";
													}
													*/
													echo $o['title'] . '</a>';
													echo '</li>';
												}
												echo '</ul></aside>';
											}
										}
										echo '<aside class="bbg__article-sidebar__aside">';
										echo $siteSelect;
										echo '</aside>';
									?>

								<?php if ($includeContactBox){ ?>
									<aside class="bbg__article-sidebar__aside">
										<div class="bbg__contact-card <?php if ($includeMap){echo 'bbg__contact-card--include-map';} ?>">
											<?php if ($includeMap){ ?>
												<div id='map' class='bbg__contact-card__map'></div>
											<?php } ?>

											<div class="bbg__contact-card__text">
											<h3>Contact information</h3>
											<?php
											echo $address;
											echo '<ul class="usa-unstyled-list">';
											echo $phone;
											echo $email;
											echo $learnMore;
											echo '</ul>';
											?>
											</div>
										</div>
									</aside>
								<?php } ?>

							</div><!-- .bbg__article-sidebar -->

						</div>

						<div class="usa-grid">
							<footer class="entry-footer bbg-post-footer 1234">
								<?php
									edit_post_link(
										sprintf(
											/* translators: %s: Name of current post */
											esc_html__( 'Edit %s', 'bbginnovate' ),
											the_title( '<span class="screen-reader-text">"', '"</span>', false )
										),
										'<span class="edit-link">',
										'</span>'
									);
								?>
							</footer><!-- .entry-footer -->

							<?php
								$q=getRandomQuote($entityCategorySlug, array());
								if ($q) {
									echo '<div class="bbg__entity__pullquote">';
									outputQuote($q);
									echo '</div>';
								}
							?>

						</div><!-- .usa-grid -->


					</article><!-- #post-## -->


					<div class="bbg-post-footer">
					</div>

				<?php endwhile; // End of the loop. ?>
			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
/* if the map is set, then load the necessary JS and CSS files */
if ( $includeMap ){
?>
	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.2.0/mapbox.css' rel='stylesheet' />

	<script type="text/javascript">
	L.mapbox.accessToken = '<?php echo MAPBOX_API_KEY; ?>';
	var map = L.mapbox.map('map', 'mapbox.streets')
		//.setView([38.91338, -77.03236], 16);
		<?php echo '.setView(['. $lat . ', ' . $lng . '], ' . $zoom . ');'; ?>

	map.scrollWheelZoom.disable();

	L.mapbox.featureLayer({
		// this feature is in the GeoJSON format: see geojson.org
		// for the full specification
		type: 'Feature',
		geometry: {
			type: 'Point',
			// coordinates here are in longitude, latitude order because
			// x, y is the standard for GeoJSON and many formats
			coordinates: [
				//-77.03221142292,
				//38.913371603574
				<?php echo $lng . ', ' . $lat; ?>
			]
		},
		properties: {
			title: '<?php echo $mapHeadline; ?>',
			description: '<?php echo $mapDescription; ?>',
			// one can customize markers by adding simplestyle properties
			// https://www.mapbox.com/guides/an-open-platform/#simplestyle
			'marker-size': 'large',
			'marker-color': '#981b1e',
			'marker-symbol': ''
		}
	}).addTo(map);

	</script>
<?php } ?>


<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
