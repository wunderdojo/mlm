<?php

/*
Plugin Name: zelcodes
Version: 1.0
Description: Custom plugin for displaying video posts via shortcode & widget
Author: James Currie
Author URI: http://www.wunderdojo.com
Copyright (c) 2011, James Currie / wunderojo.
*/
/*************************************************/ 
 


class zelcodes {
    
    function __construct(){
    $plugin_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    require_once('custom-widgets.php');
    add_action('widgets_init', create_function('', 'return register_widget("videoWidget");'));
    }
    
    public function get_video_thumbnail($post) {
    if (has_post_thumbnail($post->ID)) {
        the_post_thumbnail('home-link-thumb');
    } else {
        $url = '';
        $pattern = get_shortcode_regex();
            if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )){
            /* we've got a video, now see if it's Youtube or Vimeo */
            if(in_array( 'youtube', $matches[2] )){
            // it has a Youtube video
            $url = explode("=",$matches[5][0]);
            $image = "http://img.youtube.com/vi/".$url[1]."/0.jpg";
            printf('<a href="'.get_permalink($post->ID).'"><img src="%s" /></a>',$image);
            }
            elseif(in_array('vimeo', $matches[2])){
            /* it has a vimeo video */
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/54183029.php"));
            printf('<a href="'.get_permalink($post->ID).'"><img src="%s" /></a>',$hash[0]["thumbnail_medium"]); 
            }
            }
        }
    }

}//end of class
$ZELCODE = new zelcodes();
?>
