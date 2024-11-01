<?php
/*
Author: Neelam Samariya
Author URI: https://neelamsamariya.wordpress.com/
Author Email: nitsy85@gmail.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WP_Posts_Showcase_Utils {
   

    public static function getTaxonomies() {
        return get_post_types(array(
            'public'            => 'true',
            'show_in_nav_menus' => true
        ), 'objects');
    }
	
	public static function getAllPostsofTypes($ptypes) {
		
		$typesPostsArray = new stdClass;
		$sid = 0;
		$args = array( 'post_type' => $ptypes, 'post_status' => 'publish', 'posts_per_page' => -1);
		
		$post_loop = new WP_Query( $args );
		while ( $post_loop->have_posts() ) : $post_loop->the_post();
			$typesPostsArray->$sid->id = get_the_ID();
			$typesPostsArray->$sid->title = get_the_title(get_the_ID());
			$sid++;
		endwhile;
		
		//$postcolumnobject = (object) $typesPostsArray;
		
        return $typesPostsArray;
    }
	public static function getDescriptions() {
        return apply_filters('get_descriptions', array(            
            'excerpt' => __('Excerpt', 'wp-posts-showcase'),
            'content' => __('Full content', 'wp-posts-showcase')
        ));
    }

    public static function getSources() {
        return apply_filters('get_sources', array(
            'thumbnail' => __('Thumbnail', 'wp-posts-showcase'),
            'medium'    => __('Medium', 'wp-posts-showcase'),
            'large'     => __('Large', 'wp-posts-showcase'),
            'full'      => __('Full', 'wp-posts-showcase')
        ));
    }
}