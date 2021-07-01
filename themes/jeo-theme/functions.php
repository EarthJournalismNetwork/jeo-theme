<?php
require __DIR__ . '/inc/generic-css-injection.php';
require __DIR__ . '/inc/template-tags.php';
require __DIR__ . '/inc/api.php';
require __DIR__ . '/inc/post-types.php';
require __DIR__ . '/inc/custom-taxonomies.php';
require __DIR__ . '/inc/newspack-functions-overwrites.php';
require __DIR__ . '/inc/widgets.php';
require __DIR__ . '/inc/metaboxes.php';
require __DIR__ . '/inc/gutenberg-blocks.php';
require __DIR__ . '/inc/menus.php';
require __DIR__ . '/classes/ajax-pv.php';
require __DIR__ . '/classes/library_related-posts.php';



add_filter('post_link', 'custom_get_permalink', 10, 3);

function custom_get_permalink($url, $post, $leavename = false) {
	$external_source_link = get_post_meta($post->ID, 'external-source-link', true);
	if ($external_source_link) {
		return $external_source_link;
	}

	return $url;
}

add_action( 'after_setup_theme', 'jeo_setup' );

function jeo_setup() {
	load_theme_textdomain( 'jeo', get_stylesheet_directory() . '/lang' );
}

add_filter('pre_get_posts', '_search_pre_get_posts', 1);

function _search_pre_get_posts($query) {
	global $wp_query;

	if (is_admin() || !$query->is_main_query()) {
		return $query;
	}

	if (isset($query->query['p']) && strpos($query->query['p'], ':redirect') > 0) {
		$query->query['p'] = intval($query->query['p']);
		$query->is_404 = false;
		$query->is_page = true;
		$query->is_home = false;
	}


	if ($query->is_search() && $query->is_main_query()) {
		//$query->set('post_type', [$query->query['post_type']]);
		// Date filter
		if (isset($_GET['daterange'])) {
			$date_range = explode(' - ', $_GET['daterange'], 2);
			if (sizeof($date_range) == 2) {
				$from_date = date_parse($date_range[0]);
				$to_date   = date_parse($date_range[1]);
				$after  = null;
				$before = null;

				if ($from_date && checkdate($from_date['month'], $from_date['day'], $from_date['year'])) {
					$after = array(
						'year'  => $from_date['year'],
						'month' => $from_date['month'],
						'day'   => $from_date['day'],
					);
				}
				// Same for the "to" date.
				if ($to_date && checkdate($to_date['month'], $to_date['day'], $to_date['year'])) {
					$before = array(
						'year'  => $to_date['year'],
						'month' => $to_date['month'],
						'day'   => $to_date['day'],
					);
				}


				$date_query = array();
				if ($after) {
					$date_query['after'] = $after;
				}
				if ($before) {
					$date_query['before'] = $before;
				}
				if ($after || $before) {
					$date_query['inclusive'] = true;
				}

				if (!empty($date_query)) {
					$query->set('date_query', $date_query);
				}
			}
		}

		if (isset($_GET['order'])) {
			$order_option = $_GET['order'];
			$query->set('orderby', 'date');

			if ($order_option == 'ASC' || $order_option == 'DESC') {
				$query->set('order', $_GET['order']);
			}
			//var_dump($query);
		} else {
			$query->set('orderby', 'date');
			$query->set('order', 'DESC');
		}

		$categories = "";


		if(!empty($categories)) {
			$categories .= ",";
		}

		if(isset($_GET['region']) && !empty($_GET['region'])) {
			$categories .= implode(",", $_GET['region']);
		}

		if (isset($_GET['topic']) && !empty($_GET['topic'])) {
			$tags = implode(",", $_GET['topic']);
		}
		// echo $categories;

		if(!empty($categories)) {
			$query->set('category_name', $categories);
		}

		if(!empty($tags)) {
			$query->set('tag', $tags);
		}

	}

	return $query;
}

add_filter('pre_get_posts', '_author_pre_get_posts', 1);

function _author_pre_get_posts($query) {

	if ($query->is_author() && !is_admin()) {
		// die("IS AUTHOR.. DYING");

		$meta_query = $query->get('meta_query');
		if (!is_array($meta_query)) {
			$meta_query = [];
		}

		$meta_query[] = [
			'relation' => 'OR',
			['key' => 'author-bio-display', 'value' => 1],
			['key' => 'authors-listing', 'value' => 1],
		];

		$query->set('meta_query', $meta_query);
	}

	return $query;
}

add_filter('pre_get_posts', 'feed_rss_filter', 2);
function feed_rss_filter($query) {
	if ($query->is_feed) {
		$query->set('meta_query', array(
			'relation' => 'OR',
			array(
				'key'     => 'external-source-link',
				'compare' => 'NOT EXISTS',
			),

			array(
				'key'     => 'external-source-link',
				'value'   => '',
				'compare' => '=',
			),
		));
	}

	return $query;
}



function ns_filter_avatar($avatar, $id_or_email, $size, $default, $alt, $args) {
	$headers = @get_headers($args['url']);
	if (!preg_match("|200|", $headers[0])) {
		return;
	}
	return $avatar;
}
add_filter('get_avatar', 'ns_filter_avatar', 10, 6);

if (!function_exists('jeo_comment_form')) {
	function jeo_comment_form() {
		comment_form([
			'logged_in_as' => null,
			'title_reply' => null,
		]);
	}
}

function remove_website_field($fields) {
	unset($fields['url']);
	return $fields;
}
add_filter('comment_form_default_fields', 'remove_website_field');


// its suppose to fix (https://github.com/WordPress/gutenberg/issues/18098)
global $wp_embed;
add_filter('the_content', array($wp_embed, 'autoembed'), 9);

add_filter('comment_form_fields', 'move_comment_field');
function move_comment_field($fields) {
	$comment_field = $fields['comment'];
	unset($fields['comment']);
	$fields['comment'] = $comment_field;
	return $fields;
}


function wpseo_no_show_article_author_facebook( $facebook ) {
    if ( is_single() ) {
        return false;
    }
    return $facebook;
}
add_filter( 'wpseo_opengraph_author_facebook', 'wpseo_no_show_article_author_facebook', 10, 1 );

function get_term_for_default_lang( $term, $taxonomy ) {
	global $icl_adjust_id_url_filter_off;

	$term_id = is_int( $term ) ? $term : $term->term_id;

	$default_term_id = (int) icl_object_id( $term_id, $taxonomy, true, 'en' );

	$orig_flag_value = $icl_adjust_id_url_filter_off;

	$icl_adjust_id_url_filter_off = true;
	$term = get_term( $default_term_id, $taxonomy );
	$icl_adjust_id_url_filter_off = $orig_flag_value;

	return $term;
}


function show_publishers($id){
	if(taxonomy_exists('partner')){
		$partners = get_the_terms( get_the_id(), 'partner');
		if ($partners && count($partners) > 0){
			$partner_link = get_post_meta($id, 'partner-link', true);
			if (class_exists('WPSEO_Primary_Term')) {
				$wpseo_primary_term = new WPSEO_Primary_Term( 'partner', get_the_id() );
				$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
				$term = get_term( $wpseo_primary_term );

				if ($term || count($partners) == 1 ) {

					$partner_name = '';
					if($term && isset($term->term_id)) {
						$partner_name = $term->name;
					} else if (count($partners) == 1) {
						$partner_name = $partners[0]->name;
					}

					?>
					<div class="publishers">
						<span class="publisher-name">
								<?php
									if ($partner_link) {?>
										<a href="<?= $partner_link ?>" >
											<i class="fas fa-sync-alt publisher-icon"></i>
											<?php echo $partner_name; ?>
										</a>
									<?php } else { ?>
										<i class="fas fa-sync-alt publisher-icon"></i>
										<?php echo $partner_name;
									}?>
								</span>
					</div>
					<?php
				}
			}
		}
	}
}

function get_publisher($id){
	$partner_name = '';
	if(taxonomy_exists('partner')){
		$partners = get_the_terms( $id, 'partner');
		if ($partners && count($partners) > 0){
			if (class_exists('WPSEO_Primary_Term')) {
				$wpseo_primary_term = new WPSEO_Primary_Term( 'partner', $id );
				$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
				$term = get_term( $wpseo_primary_term );

				if ($term || count($partners) == 1 ) {
					if($term && isset($term->term_id)) {
						$partner_name = $term->name;
					} else if (count($partners) == 1) {
						$partner_name = $partners[0]->name;
					}
				}
			}
		}
	}
	return $partner_name;
}

/* Remove 'this is a story from media partners */
add_action( 'init', 'the_content_remove_plugin_filter' );
function the_content_remove_plugin_filter() {
    remove_filter( 'the_content', 'Newspack_Media_Partners::add_content_partner_logo', 10 );
}

function video_embed_url($url) {	
	if (empty($url)) {	
		return $url;	
	}	

	$youtube_matches = [];	
	$is_youtube = preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $youtube_matches);	
	if ($is_youtube) {	
		return 'https://www.youtube.com/embed/' . $youtube_matches[1];	
	}	

	$vimeo_matches = [];	
	$is_vimeo = preg_match("/\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/", $url, $vimeo_matches);	
	if ($is_vimeo) {	
		return 'https://player.vimeo.com/video/' . $vimeo_matches[1];	
	}	

	return $url;	
}

add_filter( 'single_template', 'override_storymap_template');

function override_storymap_template($template) {
	global $post;

	if (is_singular('storymap')) {
		return __DIR__ . '/jeo/templates/single-story-map.php';
	}

	return $template;
}

function get_language_name($code=''){
	global $sitepress;
	$details = $sitepress->get_language_details($code);
	$language_name = $details['english_name'];
	return $language_name;
}

function f_the_author( $display_name ) {

    // $display_name === string $authordata->display_name

    if ( is_feed() ) {
		$author_bio_display = get_post_meta(get_the_ID(), 'author-bio-display', true);
		$authors_listing = get_post_meta(get_the_ID(), 'authors-listing', true);

		if($author_bio_display == '1' || $authors_listing == '1') {
			return $display_name;
		} else {
			return "";
		}
    }

    return $display_name;
}

add_filter( 'the_author', 'f_the_author', 99 );

add_filter( 'wpseo_schema_graph_pieces', 'remove_author_from_schema', 11, 2 );

/**
 * Removes the breadcrumb graph pieces from the schema collector.
 *
 * @param array  $pieces  The current graph pieces.
 * @param string $context The current context.
 *
 * @return array The remaining graph pieces.
 */
function remove_author_from_schema( $pieces, $context ) {
    return \array_filter( $pieces, function( $piece ) {
		$author_bio_display = get_post_meta(get_the_ID(), 'author-bio-display', true);
		$authors_listing = get_post_meta(get_the_ID(), 'authors-listing', true);


		if(!($author_bio_display == '1' || $authors_listing == '1') && $piece instanceof \Yoast\WP\SEO\Generators\Schema\Person ) {
			return false;
		} else {
			return true;
		}
    } );
}