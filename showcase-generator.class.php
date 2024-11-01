<?php
/*
Author: Neelam Samariya
Author URI: https://neelamsamariya.wordpress.com/
Author Email: nitsy85@gmail.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WpPostsShowcaseGenerator {

  
    public static function getDefaults() {
        return array(
            'include_in_footer' => 'true',        
            'post_types'            => 'post',
            'layout'              => '3',
			'postcolumn'              => '',
            'show_title'            => 'true',			
            'post_titlecolor' => '#809393',            
            'show_description'  => 'excerpt',
            'show_more_button'      => 'true',
            'postcontent_color' => '#737777',   
            'imagebackground_color' => '#fff',
			'active_imageborder' => '#697575',			       
            'show_more_button'      => 'true',            
            'image_source'          => 'thumbnail',
            'image_width'           => 100,
            'image_height'          => 100,
            		
            
        );
    }
	
	public function add_showcase_data()
	{
		$defaults = self::getDefaults();
		return serialize($defaults);
		
	}
	
	public function customize_excerpt($post_id)
	{
		$the_post = get_post($post_id); //Gets post ID
		$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
		$excerpt_length = 55; //Sets excerpt length by word count
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);	
		if(count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '…');
			$the_excerpt = implode(' ', $words);
		endif;	
		$the_excerpt = $the_excerpt;	
		return $the_excerpt;
	}

    public static function generate() {
        global $post;

        /*
         * option parameters
        */
        $params = get_option('wp-posts-showcase_options');
		$layout_format = 0;
        //print_r($params);
		
		/*
         * default parameters
        */
        $defaultparams = self::getDefaults();
		//print_r($defaultparams);
        /*
         * description field for post */
        
        if (array_key_exists('show_description', $params) && in_array($params['show_description'], array('true', 'false'))) {
            $params['show_description'] = $params['show_description'] == 'true' ? 'excerpt' : 'false';
        } 
		
		   if((isset($params['layout'])) && ($params['layout'] > 0))
		   {
				if((isset($params['postcolumn'])) && (count($params['postcolumn']) > 0))
				{
				   $post_types = $params['post_types'];
		   
				   $query_args = array(
					'post_type'      => $post_types,
					'post__in' => $params['postcolumn'],
					'post_status'    => 'publish',           
				);
				
				//Define the loop based on arguments
 
				$loop = new WP_Query( $query_args );
				 
				//Display the posts showcase
				$li_class = "tab-horiz-3cols"; //set to default 3 columns layout class
				$content_class = "tab-content-3cols"; //set to default 3 columns layout class
				if($params['layout'] == 4)
				{
					$li_class = "tab-horiz-4cols";
					$content_class = "tab-content-4cols";
				}
				
				$out = "";
				$out .='<div class="post_showcase">
					<div class="tab '.$li_class.'">
					<ul class="tab-legend">';
				/*
				 * show title
				*/
				if($params['show_title'] == 1)
				{
					$params['show_title'] = 'true';
				}
				else
				{
					$params['show_title'] = 'false';
				}
				
				/*
				 * show read more button
				*/
				
				if($params['show_more_button'] == 1)
				{
					$params['show_more_button'] = 'true';
				}
				else
				{
					$params['show_more_button'] = 'false';
				}	
					
				for($n = 1;$n <= $params['layout']; $n++)
				{
					
					while ( $loop->have_posts() ) : $loop->the_post();
					$title = '';
					$featured_image = '';
					$pshowcase_image = '';
					$post_url = "";
						if($params['postcolumn'][$n] == $post->ID)
						{
							
							$post_url = get_permalink($post->ID);
							
							$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , $defaultparams['image_source']);

							/*
							 * if no featured image for the post
							*/
							if ( $featured_image[0] == '' || $featured_image[0] == '/' ) {
								
								$featured_image[0] = apply_filters('wps_item_featured_image_placeholder', plugin_dir_url(__FILE__) . 'images/placeholder.png');
								
							}                    
                        	$data_src = 'src="' . $featured_image[0] . '"';
							$pshowcase_image = '<img alt="' . $post->post_title . '" style="max-width:' . $defaultparams['image_width'] . '%;max-height:' . $defaultparams['image_height'] . '%" '.$data_src. '>';	
							
								$out .= '<li class="active" data-background="'.$params['imagebackground_color'].'" box-border="'.$params['active_imageborder'].'">'.$pshowcase_image;
								if($params['show_title'] == 'true')
								{
									$out .= '<span class="p_title" style="display:none; color:'.$params['post_titlecolor'].'">'.$post->post_title.'</span>';
								}								
								$out .= '</li>';
								
								
						}//if postcolumn id equals post id ends
					endwhile;
				}
				$out .= '</ul>';	 
				
				
				//post content display starts
				$out .= '<ul class="'.$content_class.'">';
				for($p = 1;$p <= $params['layout']; $p++)
				{
					
					while ( $loop->have_posts() ) : $loop->the_post();
						$description = '';
						$title = '';
						$post_url = "";
						
						
						if($params['postcolumn'][$p] == $post->ID)
						{
							$post_url = get_permalink($post->ID);	
							
							if($params['show_title'] == 'true'){
								$title = '<span class="p_title"><a href="' . $post_url . '" title="' . $post->post_title . '" style="color:'.$params['post_titlecolor'].'">' . $post->post_title . '</a></span>';	
							}
							/*
							 * show excerpt or full content
							*/
							if ( $params['show_description'] === 'excerpt' ) {
								$description = '<div class="inner-content" style="color:'.$params['postcontent_color'].'">' . self::customize_excerpt($post->ID) . '</div>';
							} else if ( $params['show_description'] === 'content' ) {
								$description = '<div class="inner-content">' .get_the_content() . '</div>';
							}											
							
							$out .='<li id="content_'.$p.'">'.$title. $description;
							
							if ( $params['show_more_button'] == 'true' ) {
								
								$out .='<p class="wp-posts-showcase-buttons"><a href="' . $post_url . '" class="post_showcase_button" title="' . __('Read more', 'wp-posts-showcase') .'">' . __('Read More', 'wp-posts-showcase') . '</a></p>';
							}
							$out .='</li>';
							
						}//if postcolumn id equals post id ends
					endwhile;
				}//post content loop ends	
				
				 
		   $out .= '</ul></div>
		   </div>';				   
				}//postcolumn loop ends
		   }//layout loop ends   
		 
      	/*
         * reset wordpress query
        */
        wp_reset_postdata();
        return $out;
    } 
}