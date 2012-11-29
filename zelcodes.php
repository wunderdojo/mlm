<?php

/*
Plugin Name: zelcodes
Version: 1.0
Description: Custom plugin for displaying video posts via shortcode & widget
Author: James Currie
Author URI: http://www.zelcreative.com
License: GPL version 3
*/
/*************************************************/ 
 


class zelcodes {
    
    function __construct(){
    $plugin_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    require_once('custom-widgets.php');
    add_action('widgets_init', create_function('', 'return register_widget("videoWidget");'));
    add_shortcode('zelposts', array(&$this, 'processShortCodes'));

    }
    
    public function get_video_thumbnail($post, $size) {
        $size = ($size=='max')?'maxresdefault':'mqdefault';
    if (has_post_thumbnail($post->ID)) {
        $image = get_the_post_thumbnail($post->ID, 'home-link-thumb');
    } else {
        $pattern = get_shortcode_regex();
            if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )){
            /* we've got a video, now see if it's Youtube or Vimeo */
            if(in_array( 'youtube', $matches[2] )){
            // it has a Youtube video
            $video_id = explode("=",$matches[5][0]);
            $image = "http://img.youtube.com/vi/".$video_id[1]."/".$size.".jpg";
            }
            elseif(in_array('vimeo', $matches[2])){
            /* it has a vimeo video */
            $video_id = end(explode("/",$matches[5][0]));
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$video_id.".php"));
            $image = $hash[0]["thumbnail_medium"];
            }
            }
        }
        return $image;
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
                            /* Here need to filter out any video shortcodes from within the post body
                             * and replace them with thumbnails or video screenshots
                             */
                            $image = $this->get_video_thumbnail($mypost, 'max');
                            $image = sprintf('<a href="'.get_permalink($post->ID).'"><img src="%s" /></a>',$image);
                            $pattern = get_shortcode_regex();
                            if (   preg_match_all( '/'. $pattern .'/s', $mypost->post_content, $matches )
            && array_key_exists( 2, $matches ) && (in_array( 'youtube', $matches[2] ) || in_array('vimeo', $matches[2]))){
                            /* do the replacement here */
                            $shortcode = $matches[2][0];
                            $newcontent = preg_replace('~^\['.$shortcode.'.*\]~',$image, $mypost->post_content);
                                }
                            $output.= "<li>".apply_filters('the_content',$newcontent)."</li>";
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
