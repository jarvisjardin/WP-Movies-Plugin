<?php
/**
* Plugin Name: Movies Custom post type
* Plugin URI: http://github.com/jjardin
* Description: A plugin to create a custom post type for Movies
* Version:  1.0
* Author: Jarvis Jardin
* Author URI: http://github.com/jjardin
* License:  GPL2
*/

// CREATING THE CUSTOM POST TYPE
add_action( 'init', 'movie_init' );

function movie_init() {

	// LABELS for Plugin
	$labels = array(	
		'name'					=> __( 'Movies' ),
		'singular_name'			=> __( 'Movie' ),
		'menu_name'				=> __( 'Movies'),		
		'name_admin_bar'		=> __( 'Movie' ),
		'add_new'				=> __( 'Add New' ),
		'add_new_item'			=> __( 'Add New Movie' ),
		'new_item'				=> __( 'New Movie' ),
		'edit_item'				=> __( 'Edit Movie' ),
		'view_item'				=> __( 'View Movie' ),
		'all_items'				=> __( 'All Movies' ),
		'search_items'			=> __( 'Search Movies' ),
		'parent_item_colon'		=> __( 'Parent Movies:' ),
		'not_found'				=> __( 'No movies found.' ),
		'not_found_in_trash'	=> __( 'No movies found in Trash.' )
	);
	
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'query_var'				=> true,
		'rewrite'				=> array( 'slug' => 'movie' ),
		'capability_type'		=> 'post',
		'has_archive'			=> true,
		'Hierarchical'			=> false,
		'menu_position'			=> null,
		'supports'				=> array( 'title', 'editor', 'author', 'thumbnail', 'comments')
	);
	
	register_post_type( 'movie', $args );

}

// CUSTOM META BOX for Movie Post Type

add_action( 'admin_init', 'my_admin' );

function my_admin() {
    add_meta_box( 'movie_meta_box',
        'Movie',
        'display_movie_meta_box',
        'movie', 'normal', 'high'
    );
}


// DISPLAYING the META BOX in Add/Update view for Movie CPT
function display_movie_meta_box( $movie ) {
	$movie_rating = intval( get_post_meta( $movie->ID, 'movie_rating', true ) );
	$movie_year = esc_html( get_post_meta( $movie->ID, 'movie_year', true ) );
	?>
	<table>
		<tr>
			<td style="width: 100%">Movie Year </td>
			<td><input type="text" size="80" name="movie_year" value="<?php echo $movie_year; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 150px">Movie Rating</td>
			<td>
				<select style="width: 100px" name="movie_rating">
				<?php
					for ( $rating = 5; $rating >= 1; $rating -- ) {
				?>
					<option value="<?php echo $rating; ?>" <?php echo selected( $rating, $movie_rating ); ?>>
				<?php echo $rating; ?> stars <?php } ?>
				</select>
			</td>
			</tr>
	</table>
<?php
	} //end display_movie_meta_box

// SAVING THE DATA FROM THE META BOX
add_action( 'save_post', 'add_movie_fields', 10, 2 );

function add_movie_fields( $movie_id, $movie) {
	if ( $movie->post_type == 'movie' ) {
		if ( isset( $_POST['movie_year'] ) && $_POST['movie_year'] != '' ) {
			update_post_meta( $movie_id, 'movie_year', $_POST['movie_year'] );
		}
		if ( isset( $_POST['movie_rating'] ) && $_POST['movie_rating'] != '' ) {
			update_post_meta( $movie_id, 'movie_rating', $_POST['movie_rating'] );
		}
	}
}

//Template to display custom single post for Movie CPT
add_filter( 'template_include', 'include_template_function', 1 );

function include_template_function( $template_path ) {
	if ( get_post_type() == 'movie' ) {
		if ( is_single() ) {
			if ( $theme_file = locate_template( array ( '/views/single-movie.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/views/single-movie.php';
			}
		}else{
			// redirect to home page. Can remove if usage of Movie Archive is needed.
			wp_redirect( home_url() ); 
		}
	}
	return $template_path;
}


//SHORT CODE CREATION TO DISPLAY MOVIES ON FRONT PAGE. 
/*	
	1. Create New Page. 
	2. Include '[movie]' in the Edit Area
	3. Under Apperance > Customize > Static Front Page, select 'A static page' and choose the new page created in the 'Front Page' dropdown.
	4. Once saved, you may need to reactivate your current then, and then the Movie CPT will be display on the front page.
*/
add_shortcode('movie', 'movie_shortcode_query');
function movie_shortcode_query($content){
	
	global $post;
	
	$posts = new WP_Query(array( 'post_type' => 'movie' ));
	$output = '';
	
	if ($posts->have_posts())
		while ($posts->have_posts()):
			$posts->the_post();
?>
		<div id="primary">
			<div id="content" role="main">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<div style="float: right; margin: 10px">
							<?php the_post_thumbnail( array( 150, 150 ) ); ?>
						</div>
						<strong>Title: </strong>
							<a href="<?php echo get_permalink(); ?>" title="<?php get_the_title(); ?>">
							<?php the_title(); ?>
							</a>
						<br />
						<strong>Year: </strong>
						<?php echo esc_html( get_post_meta( get_the_ID(), 'movie_year', true ) ); ?>
						<br />
						<strong>Rating:</strong>
						<?php
						$nb_stars = intval( get_post_meta( get_the_ID(), 'movie_rating', true ) );
						for ( $star_counter = 1; $star_counter <= 5; $star_counter++ ) {
							if ( $star_counter <= $nb_stars ) {
								echo '<img src="' . plugins_url( 'movies-custom-post-type/images/icon.gif' ).'" height="32px" width="32px" />';
							}else {
								echo '<img src="' . plugins_url( 'movies-custom-post-type/images/grey.png' ).'"height="32px" width="32px" />';
							}
						}
						?>
					</header>

					<div class="entry-content">
						<strong>Description:</strong></br>
						<?php echo read_more(get_the_content());?>
					</div>
				</div>
			</div>
		</div>
<?php
	
		endwhile;
	else
		echo "Sorry, there are ZERO movies. =(";
		return; // no posts found
	
	wp_reset_query();
}

// If description length is longer that 200, then shorten lenght 200 and add 'Read More' link that directs to Single Post

function read_more($string){
	$string = strip_tags($string);

	if (strlen($string) > 200) {
	    $stringCut = substr($string, 0, 200);
		$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... 
		<a href="'.get_permalink().'" title="'.get_the_title().'">
		Read More
		</a>'; 
	}
	return $string;
}

?>