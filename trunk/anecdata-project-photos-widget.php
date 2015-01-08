<?php
/*
Plugin Name: Anecdata Recent Project Photos
Description: Recent photos from your project on <a href="http://www.anecdata.org">Anecdata.org!</a>
Version: 1.0.1
Author: Anecdata
Author URI: http://www.anecdata.org
License: GPL2
*/
/**
  Copyright 2015  Duncan Bailey  (email : dbailey@mdibl.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Creating the widget 
	class anecdata_sidebar_photo_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'anecdata_sidebar_photo_widget', 

		// Widget name will appear in UI
		__('Anecdata Project Photos', 'anecdata_sidebar_photo_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Recent photos from your Anecdata project', 'anecdata_sidebar_photo_widget_domain' ), ) 
		);
		
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
	}
	

	public function register_plugin_scripts() {
		wp_enqueue_script('jquery');
	}

	public function register_plugin_styles() {
		wp_register_style( 'anecdata_sidebar_photo_widget', plugins_url( 'anecdata-project-photos-widget/style.css' ) );
		wp_enqueue_style( 'anecdata_sidebar_photo_widget' );		
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		?>
			<div id="anecdata-recent-photos">				
				<div class="anecdata-recent-photos-loader">Loading photos...</div>
			</div>
			<a href="http://www.anecdata.org/projects/view/<?php echo $instance['project_id']; ?>">Visit the project on Anecdata.org</a>
			<script>			
				jQuery(document).ready(function($) {
					$container = $("#anecdata-recent-photos");
					$.ajax({
						type: "GET",
						url: 'http://dataportal.nfshost.com/posts.json?images=1&limit=6&callback&project_id=<?php echo $instance['project_id']; ?>',
						dataType: 'jsonp',
						async: false,
						success: function (data) {
							$(".anecdata-recent-photos-loader").hide();
							var count = 0;
							$.each(data, function(i, post){
								$.each(post.Image, function(i, image){
									count++;
									if((count + 2) % 3 == 0){
										width = 100;
										size = "medium";
									} else {
										width = 50;
										size = "small";
									}
									$container.append("<a href='http://www.anecdata.org/posts/view/" + post.Post.id + "'><img src='http://www.anecdata.org" + image.image.replace(/(\.[\w\d_-]+)$/i, '-' + size + '$1') + "' style='align:right; width:calc(" + width + "% - 5px); padding:2.5px; margin-bottom:-2.5px;'></a>");
								});
							});
						}
					});
				});
			</script>
		<?php
		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'anecdata_sidebar_photo_widget_domain' );
		}
		if ( isset( $instance[ 'project_id' ] ) ) {
			$project_id = $instance[ 'project_id' ];
		}
		else {
			$project_id = __( '1', 'anecdata_sidebar_photo_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'project_id' ); ?>"><?php _e( 'Project ID:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'project_id' ); ?>" name="<?php echo $this->get_field_name( 'project_id' ); ?>" type="text" value="<?php echo esc_attr( $project_id ); ?>" />
		</p>
		<p class="description">E.g. "http://www.anecdata.org/projects/view/<strong><u>59</u></strong>".</p>
		<p class="description">Don't have a project yet? <a href="http://anecdata.org/projects" target = "_blank">Create one now.</a></p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['project_id'] = ( ! empty( $new_instance['project_id'] ) ) ? intval(strip_tags( $new_instance['project_id'] )) : '';
		return $instance;
	}
} // Class anecdata_sidebar_photo_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'anecdata_sidebar_photo_widget' );
}

add_action( 'widgets_init', 'wpb_load_widget' );
/* Stop Adding Functions Below this Line */
?>