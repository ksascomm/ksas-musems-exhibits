<?php
/*
Plugin Name: KSAS Museums Exhibitions & Programs
Plugin URI:  http://krieger.jhu.edu/documentation/plugins/job-market/
Description: Custom Content Type for Museum's & Society Program. Based on Flagship Content Types
Version: 2.0
Author: Timmy Gelles
Author URI: mailto:tgelles@jhu.edu
License: GPL2
*/
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_ksasexhibits_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_ksasexhibits_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'               => _x( 'Exhibition Types', 'taxonomy general name' ),
		'singular_name'      => _x( 'Exhibition Type', 'taxonomy singular name' ),
		'add_new'            => _x( 'Add New Exhibition Type', 'Exhibition Type' ),
		'add_new_item'       => __( 'Add New Exhibition Type' ),
		'edit_item'          => __( 'Edit Exhibition Type' ),
		'new_item'           => __( 'New Exhibition Type' ),
		'view_item'          => __( 'View Exhibition Type' ),
		'search_items'       => __( 'Search Exhibition Types' ),
		'not_found'          => __( 'No Exhibition Type found' ),
		'not_found_in_trash' => __( 'No Exhibition Type found in Trash' ),
	);

		$args = array(
			'labels'            => $labels,
			'singular_label'    => __( 'Exhibition Type' ),
			'public'            => true,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_tagcloud'     => false,
			'show_in_nav_menus' => false,
			'show_in_rest'       => true,
			'rewrite'           => array(
				'slug'       => 'exhibitions',
				'with_front' => false,
			),
		);
		register_taxonomy( 'exhibition_type', 'ksasexhibits', $args );
}

function register_ksasexhibits_posttype() {
	$labels = array(
		'name'               => _x( 'Exhibits & Programs', 'post type general name' ),
		'singular_name'      => _x( 'Exhibit', 'post type singular name' ),
		'add_new'            => __( 'Add New Exhibit & Program' ),
		'add_new_item'       => __( 'Add New Exhibit & Program' ),
		'edit_item'          => __( 'Edit Exhibit & Program' ),
		'new_item'           => __( 'New Exhibit & Program' ),
		'view_item'          => __( 'View Exhibit & Program' ),
		'search_items'       => __( 'Search Exhibit & Program' ),
		'not_found'          => __( 'No Exhibit & Program found' ),
		'not_found_in_trash' => __( 'No Exhibit & Program found in Trash' ),
		'parent_item_colon'  => __( '' ),
		'menu_name'          => __( 'Exhibits & Programs' ),
	);

		// $taxonomies = array( 'exhibition_type' );

		$supports = array( 'title', 'revisions', 'thumbnail', 'editor' );

		$post_type_args = array(
			'labels'             => $labels,
			'singular_label'     => __( 'Exhibit & Program' ),
			'public'             => true,
			'show_ui'            => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'rewrite'            => array(
				'slug'       => 'exhibit',
				'with_front' => false,
			),
			'supports'           => $supports,
			'menu_position'      => 5,
			'show_in_rest'       => true,
			// 'taxonomies'      => $taxonomies,
			'show_in_nav_menus'  => true,
		);
		register_post_type( 'ksasexhibits', $post_type_args );
}
add_action( 'init', 'register_ksasexhibits_posttype' );

$exhibitinformation_5_metabox = array(
	'id'       => 'exhibitinformation',
	'title'    => 'Exhibit Information',
	'page'     => array( 'ksasexhibits' ),
	'context'  => 'normal',
	'priority' => 'default',
	'fields'   => array(

		array(
			'name'        => 'Location',
			'desc'        => '',
			'id'          => 'ecpt_location',
			'class'       => 'ecpt_location',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
			'std'         => '',
		),

		array(
			'name'        => 'Date',
			'desc'        => '',
			'id'          => 'ecpt_dates',
			'class'       => 'ecpt_dates',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
			'std'         => '',
		)
	),
);

add_action( 'admin_menu', 'ecpt_add_exhibitinformation_5_meta_box' );

function ecpt_add_exhibitinformation_5_meta_box() {

	global $exhibitinformation_5_metabox;

	foreach ( $exhibitinformation_5_metabox['page'] as $page ) {
		add_meta_box( $exhibitinformation_5_metabox['id'], $exhibitinformation_5_metabox['title'], 'ecpt_show_exhibitinformation_5_box', $page, 'normal', 'default', $exhibitinformation_5_metabox );
	}
}

// function to show meta boxes
function ecpt_show_exhibitinformation_5_box() {
	global $post;
	global $exhibitinformation_5_metabox;
	global $ecpt_prefix;
	global $wp_version;

	// Use nonce for verification
	echo '<input type="hidden" name="ecpt_exhibitinformation_5_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

	echo '<table class="form-table">';

	foreach ( $exhibitinformation_5_metabox['fields'] as $field ) {
		// get current post meta data

		$meta = get_post_meta( $post->ID, $field['id'], true );

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', stripslashes( $field['name'] ), '</label></th>',
				'<td class="ecpt_field_type_' . str_replace( ' ', '_', $field['type'] ) . '">';
		switch ( $field['type'] ) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" /><br/>', '', stripslashes( $field['desc'] );
				break;
			case 'textarea':
				if ( $field['rich_editor'] == 1 ) {
						echo wp_editor(
							$meta,
							$field['id'],
							array(
								'textarea_name' => $field['id'],
								'wpautop'       => false,
							)
						); } else {
					echo '<div style="width: 100%;"><textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea></div>', '', stripslashes( $field['desc'] );
						}

				break;
		}
		echo '<td>',
			'</tr>';
	}

	echo '</table>';
}

// Save data from meta box
add_action( 'save_post', 'ecpt_exhibitinformation_5_save' );
function ecpt_exhibitinformation_5_save( $post_id ) {
	global $post;
	global $exhibitinformation_5_metabox;

	// verify nonce
	if ( ! isset( $_POST['ecpt_exhibitinformation_5_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ecpt_exhibitinformation_5_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	foreach ( $exhibitinformation_5_metabox['fields'] as $field ) {

		$old = get_post_meta( $post_id, $field['id'], true );
		$new = $_POST[ $field['id'] ];

		if ( $new && $new != $old ) {
			if ( $field['type'] == 'date' ) {
				$new = ecpt_format_date( $new );
				update_post_meta( $post_id, $field['id'], $new );
			} else {
				if ( is_string( $new ) ) {
					$new = $new;
				}
				update_post_meta( $post_id, $field['id'], $new );

			}
		} elseif ( '' == $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	}
}

function define_exhibition_type_terms() {
	$terms = array(
		'0' => array(
			'name' => 'Campus Partnerships',
			'slug' => 'campus',
		),
		'1' => array(
			'name' => 'Community Partnerships',
			'slug' => 'community',
		),
		'2' => array(
			'name' => 'Independent Study',
			'slug' => 'independent',
		),
		'3' => array(
			'name' => 'Digital Work',
			'slug' => 'digital',
		),
		'4' => array(
			'name' => 'Mellon Foundation',
			'slug' => 'mellon',
		),
	);
	return $terms;
}

function check_exhibition_type_terms() {

	// see if we already have populated any terms
	$terms = get_terms( 'exhibition_type', array( 'hide_empty' => false ) );

	// if no terms then lets add our terms
	if ( empty( $terms ) ) {
		$terms = array(
			'0' => array(
				'name' => 'Campus Partnerships',
				'slug' => 'campus',
			),
			'1' => array(
				'name' => 'Community Partnerships',
				'slug' => 'community',
			),
			'2' => array(
				'name' => 'Independent Study',
				'slug' => 'independent',
			),
			'3' => array(
				'name' => 'Digital Work',
				'slug' => 'digital',
			),
			'4' => array(
				'name' => 'Mellon Foundation',
				'slug' => 'mellon',
			),
		);
		foreach ( $terms as $term ) {
			if ( ! term_exists( $term['name'], 'exhibition_type' ) ) {
				wp_insert_term( $term['name'], 'exhibition_type', array( 'slug' => $term['slug'] ) );
			}
		}
	}

}

add_action( 'init', 'check_exhibition_type_terms' );



add_filter( 'manage_edit-ksasexhibits_columns', 'my_ksasexhibits_columns' );

function my_ksasexhibits_columns( $columns ) {

	$columns = array(
		'cb'          => '<input type="checkbox" />',
		'title'       => __( 'Name' ),
		'exhibitions' => __( 'Exhibition Type' ),
		'date'        => __( 'Date' ),
	);

	return $columns;
}

add_action( 'manage_ksasexhibits_posts_custom_column', 'my_manage_ksasexhibits_columns', 10, 2 );

function my_manage_ksasexhibits_columns( $column, $post_id ) {
	global $post;

	switch ( $column ) {

		/* If displaying the 'program_type' column. */

		case 'exhibitions':
			/* Get the program_types for the post. */
			$terms = get_the_terms( $post_id, 'exhibition_type' );

			/* If terms were found. */
			if ( ! empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							add_query_arg(
								array(
									'post_type'       => $post->post_type,
									'exhibition_type' => $term->slug,
								),
								'edit.php'
							)
						),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'exhibition_type', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'No Exhibitions Assigned' );
			}

			break;
		/* Just break out of the switch statement for everything else. */
		default:
			break;
	}
}


