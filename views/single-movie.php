<?php
 /*
 	Template Name: Custom Movie Post Type
 */
 
get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

		<?php
		while ( have_posts() ) : the_post();
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
				<header class="entry-header">
	 				<div style="padding-top:10px;"> 
	 					<?php the_post_thumbnail(); ?> 
	 				</div>
					<strong>Title: </strong>
					<?php the_title(); ?><br />
					<strong>Year: </strong>
					<?php echo esc_html( get_post_meta( get_the_ID(), 'movie_year', true ) ); ?>
					<br />
					<strong>Rating:</strong>
					<?php
					$nb_stars = intval( get_post_meta( get_the_ID(), 'movie_rating', true ) );
					for ( $star_counter = 1; $star_counter <= 5; $star_counter++ ) {
						if ( $star_counter <= $nb_stars ) {
							echo '<img src="' . plugins_url( 'movies-custom-post-type/images/icon.gif' ).'" height="30px" width="30px" />';
						} else {
							echo '<img src="' . plugins_url( 'movies-custom-post-type/images/grey.png' ).'"height="30px" width="30px" />';
						}
					}
					?>
				</header>
				<div class="entry-content">
					<strong>Description:</strong>
					<?php the_content(); ?>
				</div>
			</article>
<?php	endwhile; ?>

		</div>
	</div>

<?php get_footer(); ?>