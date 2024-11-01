<?php
/*
Author: Neelam Samariya
Author URI: https://neelamsamariya.wordpress.com/
Author Email: nitsy85@gmail.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WpPostsShowcaseShortcodeDecode {
    public static function initialize() {
        return WpPostsShowcaseGenerator::generate();
    }
}
add_shortcode("wp_posts_showcase", array('WpPostsShowcaseShortcodeDecode', "initialize"));
?>