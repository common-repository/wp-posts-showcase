<?php
/**
 * Plugin Name: Wp Posts Showcase
 * Plugin URI: https://neelamsamariya.wordpress.com/
 * Description: Fully Responsive and Mobile Friendly way to showcase your featured posts or custom post types on frontend.
 * Author: Neelam Samariya
 * Version: 1.0
 * Text Domain: wp-posts-showcase 
 * Author URI: https://neelamsamariya.wordpress.com/
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
 * plugin
 */
$WP_Posts_Showcase = new WP_Posts_Showcase();
class WP_Posts_Showcase {
    const VERSION = '1.1';
    private $plugin_name = 'WP Posts Showcase';
    private $plugin_slug = 'wp-posts-showcase';
    private $options = array();

    public function __construct() {
        /*
         * get options
         */
        $showcase_options = get_option($this->plugin_slug . '_options');
		
		if(is_array($showcase_options))
		{
		$this->options = $showcase_options;
		}
		else{
		$this->options = unserialize($showcase_options);	
		}	
      // print_r($this->options);
	   
	    /*
         * include utils
         */
        require_once( "includes/utils.class.php" );
        //include required files based on admin or site
        if ( is_admin() ) {
            /*
             * activate plugin
             */
            add_action( 'init', array($this, 'wp_posts_showcase_button') );
            add_action( 'admin_init', array($this, 'register_settings'));
            add_action( 'admin_menu', array($this, 'admin_menu_options'));
            add_action( 'admin_head',  array($this, 'wp_posts_showcase_wp_head') );    
			add_action( 'admin_head', array($this, 'wp_posts_showcase_button') );        
			
            /*
             * register scripts and stylesheets for admin
             */
            add_action( "admin_enqueue_scripts", array($this, "admin_wp_posts_showcase_register_scripts") );
			
			 /*
             * ajax page for adding post columns dropdown
             */
            add_action( "wp_ajax_wp_posts_showcase_adddropdown", array($this, "WpPostsShowcaseAddDropdown") );
			add_action("wp_ajax_nopriv_wp_posts_showcase_adddropdown", "authenticate_login");
           
            /*
             * clear settings
             */
            register_deactivation_hook(__FILE__,  array($this, 'deactivation') );
        } else {   
			require_once( "shortcode-decode.class.php" );         
			/*
             * register scripts
             */
            add_action( "wp_enqueue_scripts", array($this, "wp_posts_showcase_register_scripts") );
            add_action( "wp_head",  array($this, "wp_posts_showcase_wp_head") );
        }   
		
		require_once( "showcase-generator.class.php" );         
    }
	
	

    /**
     * deactivate the plugin
     */
    public function deactivation() {
        if ( !current_user_can( 'activate_plugins' ) ) {
           return;
        }
        delete_option( $this->plugin_slug . '_options' );
    }

    /**
     * retrieves the plugin options from the database.
     */
    private function get_defaults() {
        return array();
    }
	
	function WpPostsShowcaseAddDropdown()
	{
		
		global $wpdb;
		
		if(!check_ajax_referer( 'wp_post_showcase_nonce', 'nonce', false ))
		{
			echo 'You dont have rights!';
			exit();
		}		
		if ( ! current_user_can( 'manage_options') ) {
			return;
		}		
		$posttyps = ""; 
		
		if(isset($_REQUEST['ptypes']))
		{
			$posttyps = sanitize_text_field($_REQUEST['ptypes']);
		}
		$cntr_post = 0;
		
		if(isset($_REQUEST['countr']))
		{
			$cntr_post = sanitize_text_field(intval($_REQUEST['countr']));
						
		}		
		if(($posttyps != "") && ($cntr_post != 0))
		{			
			$post_array = WP_Posts_Showcase_Utils::getAllPostsofTypes($posttyps);			
			//print_r($post_array);
			if(count((array)$post_array))
			{
				?>
                <tbody>
                <?php				
				for($i = 1; $i <= $cntr_post; $i++ )
				{
				?>
                 <tr valign="top">
                        <th scope="row"><?php _e('Post Column-'.$i, $this->plugin_slug) ?></th>
                        <td>
                            <label for="<?php echo $this->plugin_slug."_postcolumn-".$i; ?>">
                               <select name="<?php echo $this->plugin_slug; ?>_options[postcolumn][<?php echo $i;?>]" id="<?php echo $this->plugin_slug; ?>_postcolumn-<?php echo $i;?>" class="showcase_posts">
                               <?php							  
							   foreach ($post_array as $colvalues) {
										echo "<option value=\"" .$colvalues->id ."\" ". ( ($this->options["postcolumn"][$i]  == $colvalues->id) ? 'selected="selected"' : null) .">". $colvalues->title ."</option>";									
								
								}										
									?>                                        
    							</select>                            
                        </td>
                    </tr>                
                <?php					
				}
				?>
                </tbody>
                <?php
			}
			else{
			return 0;
		}
		
		}
		
		die();
	}
	
	function authenticate_login() {
	   echo "You must log in to change settings";
	   die();
	}
	
    /*
     * adds the plugin url in the head tag
     */
    function wp_posts_showcase_wp_head() {
        echo "<script>var wp_posts_showcase_url=\"".plugin_dir_url(__FILE__)."\";</script>";
    }

    /*
     * registers the scripts and styles for admin
     */
    public function admin_wp_posts_showcase_register_scripts() {
        wp_register_script("wp-posts-showcase-js", plugin_dir_url(__FILE__) . "js/wp-posts-showcase.js", array( 'jquery', 'wp-color-picker' ), '1.0.0', true);
		wp_localize_script( 'wp-posts-showcase-js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajax_nonce' => wp_create_nonce('wp_post_showcase_nonce')));        
		wp_enqueue_script( 'jquery' );
        wp_enqueue_script("wp-posts-showcase-js");
        wp_register_style("wp-posts-showcase-css", plugin_dir_url(__FILE__) . "css/wp-posts-showcase.css");
        wp_enqueue_style("wp-posts-showcase-css");		
		// Css rules for Color Picker		
    	wp_enqueue_style( 'wp-color-picker' );
    }

    /*
     * registers the scripts and styles
     */
    function wp_posts_showcase_register_scripts() {
        $include_in_footer = array_key_exists('include_in_footer', $this->options) ? true  : false;
        wp_register_script("tabModule", plugin_dir_url(__FILE__) . "js/tabModule.js", array('jquery'), '2.0.0', $include_in_footer);        
        wp_register_style("showcase-tabModule.style", plugin_dir_url(__FILE__) . "css/showcase-tabModule.css");        
        wp_enqueue_script("tabModule");
        wp_enqueue_style("showcase-tabModule.style");
        
    } 
	
	  /*
     * add button to editor
     */
    function wp_posts_showcase_button() {
        // check user permissions
        if ( !current_user_can( "edit_posts" ) && !current_user_can( "edit_pages" ) ) {
            return;
        }

        //adds button to the visual editor
        add_filter( "mce_external_plugins", array($this, "add_wp_posts_showcase_plugin") );
        add_filter( "mce_buttons", array($this, "register_add_wp_posts_showcase_button") );
    }
	
	 /*
     * callback function
     */
    function add_wp_posts_showcase_plugin( $plugin_array ) {        
        $version = "pshowcase_shortcode.js";
        $plugin_array["wp_posts_showcase_button"] = plugin_dir_url(__FILE__)."js/".$version;
        return $plugin_array;
    }

    /*
     * callback function
     */
    public function register_add_wp_posts_showcase_button( $buttons ) {
        array_push($buttons, "wp_posts_showcase_button");
        return $buttons;
    }
	
	  
   /**
     * add submenu
     */
    public function admin_menu_options() {
        add_options_page(
            __($this->plugin_name, $this->plugin_slug),
            __($this->plugin_name, $this->plugin_slug),
            'manage_options',
            $this->plugin_slug,
            array( $this, 'settings_page' )
        );
    }

    /**
     * register plugin settings
     */
    public function register_settings() {
	
        register_setting( $this->plugin_slug, $this->plugin_slug . '_options');
		$defaults = WpPostsShowcaseGenerator::add_showcase_data(); 
		add_option($this->plugin_slug . '_options', $defaults);
		
    }	
	
public function settings_page() { 
$instance = "";
$instance = WpPostsShowcaseGenerator::getDefaults();  
//print_r($instance);

//$nonce = wp_create_nonce("wp-postshowcase-nonce");
/**********************************************/

?>
<div class="wrap">
    <h2><?php _e($this->plugin_name, $this->plugin_slug) ?></h2>

    <div id="poststuff" class="metabox-holder has-right-sidebar">
       
        <div class="wp-posts-showcase-form">
            <div id="post-body-content">
                <form method="post" action="options.php">
                    <?php settings_fields( $this->plugin_slug ); ?>
                    <p>
            			<h3 class="heading"><?php _e("Display options", $this->plugin_slug) ?></h3>
					</p>
                    <p>
                    </p>
                    <div class="wp-posts-showcase-notify">
                    <div class="metabox-holder">
                        <div id="post-body">
                            <div id="dashboard-widgets-main-content">
                                <div class="postbox-container" id="main-container" style="width:75%;">
                                    <?php _e('Go through the steps below to add wp posts showcase to posts or pages:', $this->plugin_slug) ?>
                                    <p>							
                                    <b><?php _e('Step 1', $this->plugin_slug) ?></b> - <?php _e('Make settings below to cusomize showcase according to your requirement', $this->plugin_slug) ?> .</li>							
                                    </p>
                                    <p>
                                    <b><?php _e('Step 2', $this->plugin_slug) ?></b> - <?php _e(' To add posts showcase to your post/page you can use the shortcode [wp_posts_showcase] or directly add wp posts showcase from button available in the post/page editor ', $this->plugin_slug) ?> .</li>
                                    </p>
                                    <p>
                                    <b><?php _e('Please Note', $this->plugin_slug) ?> - <?php _e('Add Posts featured images of the same size so as to get the desired result', $this->plugin_slug) ?></b> .</li>
                                    </p>
                                </div>
                                <div class="postbox-container" id="side-container" style="width:24%;">
                                </div>						
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wp-posts-showcase-maindiv">
				 <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row"><?php _e('Scripts include', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_include_in_footer">
                                        <input type="checkbox" name="<?php echo $this->plugin_slug; ?>_options[include_in_footer]" id="<?php echo $this->plugin_slug; ?>_include_in_footer"  value="1" <?php array_key_exists('include_in_footer', $this->options) ? checked( (bool) $this->options["include_in_footer"], true ): null; ?> />
                                        <?php _e('Include plugin\'s scripts in "footer" section', $this->plugin_slug) ?>
                                    </label>
                                    <p class="description"><?php _e('Select if you want to include plugins\'s scripts in "footer" section. (on defaults is include in "head")', $this->plugin_slug) ?></p>
                                </td>
                            </tr>							
							 <tr valign="top">
                                <th scope="row"><?php _e('Post Types', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_post_types">
									<select name="<?php echo $this->plugin_slug; ?>_options[post_types]" id="<?php echo $this->plugin_slug; ?>_[post_types]" class="dropdown_posttypes">
									<?php
										$taxonomies = WP_Posts_Showcase_Utils::getTaxonomies();
										
										
										foreach($taxonomies as $key => $type) {
											
											echo "<option value=\"" .$key ."\" ". ( ($this->options["post_types"]  == $key) ? 'selected="selected"' : null) .">". $type->label ."</option>";
										}
									?>
									</select>
                                    <p class="description"><?php _e('Select which post type you want to display', $this->plugin_slug) ?></p>
                                </td>
                            </tr>
                            
							 <tr valign="top" id="layout_row">
                                <th scope="row"><?php _e('Layout', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_layout"></label>
                                       <select name="<?php echo $this->plugin_slug; ?>_options[layout]" id="<?php echo $this->plugin_slug; ?>_layout" class="layout_blocks">
									   <option value="3" <?php if(array_key_exists('layout', $this->options) && ($this->options["layout"] == 3)){ echo 'selected=selected';}?>>3-column</option>
									   <option value="4" <?php if(array_key_exists('layout', $this->options) && ($this->options["layout"] == 4)){ echo 'selected=selected';}?>>4-column</option>           
            </select>  
                                    <p class="description"><?php _e('Select if you want to display posts in 3 column or 4 column layout', $this->plugin_slug) ?></p>
                                </td>
                            </tr>
                            <tbody>
                            </table>
                            <table class="form-table" id="selection_posts">
                            <tbody>
                            <?php							
							if(array_key_exists('postcolumn', $this->options))
							{
								$num_columns = 0;
								$num_columns = $this->options["layout"];
								for($k = 1; $k <= $num_columns; $k++)
								{
								?>
                                 <tr valign="top">
                                        <th scope="row"><?php _e('Post Column-'.$k, $this->plugin_slug) ?></th>
                                        <td>
                                            <label for="<?php echo $this->plugin_slug."_postcolumn-".$k; ?>">
                                            <select name="<?php echo $this->plugin_slug; ?>_options[postcolumn][<?php echo $k;?>]" id="<?php echo $this->plugin_slug; ?>_postcolumn-<?php echo $k;?>" class="showcase_posts">                                               
										   <?php
                                          // print_r($post_array);
                                           $post_array = WP_Posts_Showcase_Utils::getAllPostsofTypes($this->options["post_types"]);
                                           foreach ($post_array as $colvalues) {
                                                    echo "<option value=\"" .$colvalues->id ."\" ". ( ($this->options["postcolumn"][$k]  == $colvalues->id) ? 'selected="selected"' : null) .">". $colvalues->title ."</option>";                                                    
                                            
                                            }                                                        
                                            ?>                                        
                                             </select>                                            
                                        </td>
                                 </tr>                                
                                <?php                                    
                                }
							}
							?> 
                            <tbody>                           
                           </table>                            
                           <table class="form-table"> 
                           <tbody>
							<tr valign="top">
                                <th scope="row"><?php _e('Show title', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_show_posttitle">
									<input class="checkbox wp-posts-showcase-field" type="checkbox" id="<?php echo $this->plugin_slug; ?>_[show_title]" name="<?php echo $this->plugin_slug; ?>_options[show_title]" <?php array_key_exists('show_title', $this->options) ? checked( (bool) $this->options["show_title"], true ): null; ?> value="1" />
                                    
                                </td>
                            </tr>
							<tr valign="top">
                                <th scope="row"><?php _e('Choose title color', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_post_titlecolor">
									<input class="wp-posts-showcase-field wpps-color-picker" size="10" id="<?php echo $this->plugin_slug; ?>_[post_titlecolor]" name="<?php echo $this->plugin_slug; ?>_options[post_titlecolor]" type="text" value="<?php echo esc_attr($this->options["post_titlecolor"]); ?>" data-default-color="#809393"/>									
                                     <p class="description"><?php _e('Select post title color', $this->plugin_slug) ?></p>
                                </td>
                            </tr>							
							<tr valign="top">
                                <th scope="row"><?php _e('Show Description', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_show_description">
									<select class="select" name="<?php echo $this->plugin_slug; ?>_options[show_description]" id="<?php echo $this->plugin_slug; ?>_[show_description]">
									<?php
										$description_list = WP_Posts_Showcase_Utils::getDescriptions();
										foreach($description_list as $key => $list) {
											echo "<option value=\"". $key ."\" ". (esc_attr($this->options["show_description"]) == $key ? 'selected="selected"' : null) .">". $list ."</option>";
										}
									?>
									</select>
                                    
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Show more button', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_show_more_button">
									<input class="checkbox" type="checkbox" id="<?php echo $this->plugin_slug; ?>_[show_more_button]" name="<?php echo $this->plugin_slug; ?>_options[show_more_button]" <?php array_key_exists('show_more_button', $this->options) ? checked( (bool) $this->options["show_more_button"], true ): null; ?> value="1" />
                                    
                                </td>
                            </tr>
							<tr valign="top">
                                <th scope="row"><?php _e('Choose Description content color', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_postcontent_color">
									<input class="wp-posts-showcase-field wpps-color-picker" size="10" id="<?php echo $this->plugin_slug; ?>_[postcontent_color]" name="<?php echo $this->plugin_slug; ?>_options[postcontent_color]" type="text" value="<?php echo esc_attr($this->options["postcontent_color"]); ?>" data-default-color="#737777"/>									
                                     <p class="description"><?php _e('Select post description text color', $this->plugin_slug) ?></p>
                                </td>
                            </tr>
							<tr valign="top">
                                <th scope="row"><?php _e('Choose active post background color', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_imagebackground_color">
									<input class="wp-posts-showcase-field wpps-color-picker" size="10" id="<?php echo $this->plugin_slug; ?>_[imagebackground_color]" name="<?php echo $this->plugin_slug; ?>_options[imagebackground_color]" type="text" value="<?php echo esc_attr($this->options["imagebackground_color"]); ?>" data-default-color="#fff"/>									
                                     <p class="description"><?php _e('Select background color for the active post', $this->plugin_slug) ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Choose border color for active post', $this->plugin_slug) ?></th>
                                <td>
                                    <label for="<?php echo $this->plugin_slug; ?>_active_imageborder">
									<input class="wp-posts-showcase-field wpps-color-picker" size="10" id="<?php echo $this->plugin_slug; ?>_[active_imageborder]" name="<?php echo $this->plugin_slug; ?>_options[active_imageborder]" type="text" value="<?php echo esc_attr($this->options["active_imageborder"]); ?>" data-default-color="#697575"/>									
                                     <p class="description"><?php _e('Select border color for active post background', $this->plugin_slug) ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                   
                    
                    <hr />	
                    <span class="button-div">				
                   <?php submit_button('', 'primary', 'submit', true); ?>
                   </span>
                </form>
                </div>                             
            </div>
             <div class="inner-sidebar">
					<?php include( 'includes/plugin-info.php' ); ?>
                </div>   
        </div>
    </div>
</div>
<?php }
}