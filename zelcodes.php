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
    add_shortcode('zelposts', array(&$this, 'processShortCodes'));
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
    
    function processShortCodes($atts, $content, $code){
            if($code=='zelposts'){
               extract($atts);
               $categories=($categories)?explode(",", $categories):'';
               if($categories){
                    foreach($categories as $category){
                        $catid[] = get_category_by_slug( $category )->term_id;
                    }
               }
               $myposts = get_posts(array(
                        'category__in'=>$catid,
                        'numberposts'=>$number
                        ));
                    if($myposts):
                        $output.="<ul class='videos'>";
                        foreach($myposts as $mypost): setup_postdata($mypost);
                            $output.="<li><h2 class='headline'><a href='".get_permalink()."'>".$mypost->post_title."</a></h2></li>";
                            $output.= "<li>".apply_filters('the_content',get_the_content( $more_link_text, $stripteaser, $more_file ))."</li>";
                        /* show number of comments */
                        endforeach;
                        $output.="</ul>";
                        else:
                        $output='no posts found';
                    endif;
                   return $output;
                }
           }//end processShortCodes

}//end of class
$ZELCODE = new zelcodes();
?>
