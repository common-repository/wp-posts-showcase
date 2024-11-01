<?php
/*
Author: Neelam Samariya
Author URI: https://neelamsamariya.wordpress.com/
Author Email: nitsy85@gmail.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<div class="postbox">
    <div class="inside hndle">
        <p class="inner"><?php _e('Version', 'wp-posts-showcase') ?>: <?php echo self::VERSION ?></p>
    </div>
    <h3 class="hndle" style="border-top: 1px solid #eee;">
        <span><?php _e('Need support?', 'wp-posts-showcase') ?></span>
    </h3>
    <div class="inside">
        <p class="inner">
            <?php _e('If you are having problems with this plugin, please contact by', $this->plugin_slug) ?> <a href="mailto:nitsy85@gmail.com" target="_blank" title="nitsy85@gmail.com">nitsy85@gmail.com</a><br />
            <?php _e('For more information about this plugin, please visit', 'wp-posts-showcase') ?> <a href="https://neelamsamariya.wordpress.com/" target="_blank" title="https://neelamsamariya.wordpress.com/"><?php _e('plugin page', 'wp-posts-showcase') ?></a><br />
        </p>
    </div>

    <h3 class="hndle" style="color:#A6CF38;border-top: 1px solid #eee;">
        <span><?php _e('Need custom modification, plugin or theme?', 'wp-posts-showcase') ?></span>
    </h3>
    <div class="inside">
        <p class="inner">
            <?php _e('If you like this plugin, but need something a bit more custom or completely new, you can hire me to work for you! Email me at <a href="mailto:nitsy85@gmail.com" title="Hire me">nitsy85@gmail.com</a> for more information!', 'wp-posts-showcase') ?><br />
        </p>
    </div>
    <div class="inside" style="border-top: 1px solid #eee;">
        <p>
            <a href="https://neelamsamariya.wordpress.com/" target="_blank" title="Design and web development - https://neelamsamariya.wordpress.com/">Neelam Samariya</a>
        </p>
    </div>
</div>