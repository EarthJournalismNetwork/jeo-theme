<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Newspack
 */

?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="post-container-author-page">
        <?php newspack_post_thumbnail(); ?>
    
        <div class="entry-container">
            <?php newspack_categories(); ?>
            <header class="entry-header">
                <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            </header><!-- .entry-header -->
    
            <?php if ( 'page' !== get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php newspack_posted_on(); ?>
                </div><!-- .meta-info -->
            <?php endif; ?>
    
            <div class="entry-content">
                <?php the_excerpt(); ?>
            </div><!-- .entry-content -->
        </div><!-- .entry-container -->
    </div>
</article><!-- #post-${ID} -->

