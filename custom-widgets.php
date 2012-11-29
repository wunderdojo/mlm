<?php

class videoWidget extends WP_Widget {

    /** constructor */
    function __construct() {
		
        /* Widget settings. */
	$widget_ops = array( 'classname' => 'video-posts', 'description' => 'Sidebar widget for displaying video posts' );
   
	/* Widget control settings. */
	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'video-posts-widget' );

	/* Create the widget. */
        parent::__construct(false, $name = 'Video Posts Widget', $widget_ops);	
	}

	/*  Controls how the widget is displayed on screen */
	function widget( $args, $instance ) {
		global $ZELCODE;
                global $post;
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
                
                /* get all of the posts from the category video, excluding the one currently being
                 * displayed if on a single post page. If on any other type of page then no exclusion is needed.
                 */
                $exclude = (is_single()) ? $post->ID:'';
                $posts = get_posts(array(
                        'exclude'=>$exclude,
                        'category_name'=>'episode'      
                ));
                if($posts):?>
                    <ul class='videos'>
                    <?php foreach($posts as $post): ?>
                        <li><a href='<?php the_permalink();?>'><?php the_title();?></a></li>
                        <?php /* now get the video or post thumbnail and display as a link */
                        $image = $ZELCODE->get_video_thumbnail($post, 'small');
                        printf('<a href="'.get_permalink($post->ID).'"><img src="%s" /></a>',$image); 
                        ?>
                    <?php endforeach; ?>
                  </ul>
                <?php else:
                $output='no posts found';
                echo $output;
                endif;
    
	/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* Update the widget settings.  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
                $instance['category']= $new_instance['category'];
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Recent Posts' );
		$instance = wp_parse_args( (array) $instance, $defaults );?>
		<!-- Widget Title: Text Input -->
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
                <p><label for="cat">Select a category to display</label><?php wp_dropdown_categories( array('selected'=>$instance['category'], 'name'=>$this->get_field_name('category')) );?>
		</p>

	<?php
	}
}

?>
