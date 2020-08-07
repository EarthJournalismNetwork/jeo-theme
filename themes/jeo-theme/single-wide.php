<?php
/**
 * Template Name: One column wide
 * Template Post Type: post, page
 *
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Newspack
 */

if(is_single()) {
	get_header('single');
} else {
	get_header();
}

$parent_type_category = get_category_by_slug('type')->cat_ID;
$post_categories = get_the_category();
$post_child_category = null;
foreach ($post_categories as $post_cat) {
	if ($parent_type_category == $post_cat->parent) {
		$post_child_category = $post_cat;
		break;
	}
}
if(isset($post_child_category->slug) && in_array ( $post_child_category->slug, ['opinion', 'audio', 'video'])):
	if ($post_child_category->slug === 'opinion') : ?>
		<?php get_template_part('template-parts/singles/single', 'opinion');
	elseif ($post_child_category->slug === 'audio') : ?>
		<?php get_template_part('template-parts/singles/single', 'audio'); 
	elseif ($post_child_category->slug === 'video') : ?>
		<?php get_template_part('template-parts/singles/single', 'video'); ?>
	<?php endif;
else: ?>
	<section id="primary" class="content-area <?php echo esc_attr( newspack_get_category_tag_classes( get_the_ID() ) ); ?>">
		<main id="main" class="site-main">

			<?php

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				$isImageBehind = false;

				if ( in_array( newspack_featured_image_position(), array( 'behind') )) {
					$isImageBehind = true;
				}
			

				// Template part for large featured images.
				if ( in_array( newspack_featured_image_position(), array( 'large', 'behind', 'beside' ) ) ) :
					get_template_part( 'template-parts/post/large-featured-image' );
				else :
				?>
					<header class="entry-header">
						<?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
					</header>
				<?php endif; ?>

				<div class="main-content">
					<?php if($isImageBehind) : ?>
						<div class="entry-subhead">
							<div class="entry-meta"> 
								<?php if (get_post_meta(get_the_ID(), 'author-bio-display', true)) : ?>
									<?php newspack_posted_by(); ?>
								<?php endif; ?>
								<div></div>
								<?php newspack_posted_on(); ?>
							</div><!-- .meta-info -->
							<?php
							// Display Jetpack Share icons, if enabled
							if (function_exists('sharing_display')) {
								sharing_display('', true);
							}
							?>
						</div>
					<?php endif; ?>

					<?php
					if ( is_active_sidebar( 'article-1' ) && is_single() ) {
						dynamic_sidebar( 'article-1' );
					}

					// Place smaller featured images inside of 'content' area.
					if ( 'small' === newspack_featured_image_position() ) {
						newspack_post_thumbnail();
					}

					if ( is_page() ) {
						get_template_part( 'template-parts/content/content', 'page' );
					} else {
						get_template_part( 'template-parts/content/content', 'single' );
					}
					?>

					<?php if(is_single()) : ?>
						<div class="after-post-content-widget-area">
							<?php dynamic_sidebar('after_post_widget_area'); ?>
						</div>
					<?php endif; ?>

					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						newspack_comments_template();
					}
					?>
				</div>
				<?php
					$posts = guaraci\related_posts::get_posts(get_the_id(), 3)->posts;
					$posts_ids = [];
					foreach($posts as $key=>$value) {
						array_push($posts_ids, $value->ID);
					}

					
					$posts_query_args['post__in'] = $posts_ids;

					

					$related_posts = new \WP_Query($posts_query_args); 
				?>
				<?php if(sizeof($related_posts->posts) >= 3): ?>
					<div class="related-posts">
						<p class="title-section"><?= __('Related Posts') ?></p>

						<div class="posts">
							<?php foreach($related_posts->posts as $key=>$value): ?>
								<div class="post">
										<?php if(get_the_post_thumbnail($value->ID)) : ?>
											<div class="thumbnail">
												<a class="thumbnail-inner" href="<?php echo get_permalink($value->ID) ?>" target="blank">
													<?php echo get_the_post_thumbnail($value->ID) ?>
												</a>
											</div>
										<?php endif ?>
										<div class="entry-container"> 
											<p class="title">
												<a href="<?php echo get_permalink($value->ID) ?>" target="blank">
													<?php echo $value->post_title ?>
												</a>
											</p>
											<p class="date"><?php  echo get_the_time('F j, Y', $value->ID); ?></p>
											<p class="excerpt"><?php echo get_the_excerpt($value->ID) ?></p>
										</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif ?>
			<?php endwhile; ?>

		</main><!-- #main -->
	</section><!-- #primary -->
<?php endif; ?>
<?php
get_footer();
