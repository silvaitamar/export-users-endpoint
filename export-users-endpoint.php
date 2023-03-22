<?php
/*
 * Plugin Name:       Export Users Endpoint
 * Plugin URI:        #
 * Description:       Exports the list of users and the number of posts created during a date range.
 * Version:           1.0.0
 * Author:            Itamar Silva
 * Author URI:        https://www.linkedin.com/in/silvaitamar
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       export-users-endpoint
 * Domain Path:       /languages
 */

 function get_users_by_range_date($request){
    $request_range_date_start = $request->get_param( 'range_date_start' );
    $request_range_date_end = $request->get_param( 'range_date_end' );

	$args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'date_query'    => array(
            array(
                'after'     => $request_range_date_start,
                'before'    => $request_range_date_end,
                'inclusive' => true,
            ),
        ),
        'ignore_sticky_posts' => true
    );
    
    $query = new WP_Query( $args );

    //echo $query->post_count;

    $authors = wp_list_pluck( $query->posts, 'post_author' );
    //var_dump($authors);

    $authors_posts = array_count_values($authors);
    //print_r($authors_posts);

    $response = new WP_REST_Response($authors_posts);
    $response->set_status(200);

    return $response;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'export-users-endpoint/v1', '/author-posts/', array(
        'methods' => 'GET',
        'callback' => 'get_users_by_range_date',
        'args' => array(
            'range_date_start' => array(
                'required' => false,
				'type'     => 'string',
            ),
            'range_date_end' => array(
                'required' => false,
				'type'     => 'string',
            )
        )
    ) );
  } );