<?php
/**
 * Plugin Name: Pin It Button for Pinterest
 * Plugin URI: http://about.pinterest.com/goodies/
 * Description: Add a hover Pin It button for Pinterest to your images
 * Version: 0.9
 * Author: Pinterest
 * Author URI: http://about.pinterest.com/goodies/
 */
/* 
Copyright 2013 Pinterest, Inc
This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License, version 2, as published by the Free Software
Foundation. This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details. You should hav
received a copy of the GNU General Public License along with this program; if not,
write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, 
Boston, MA 02110-1301 USA 
*/

add_action('admin_menu', 'pin_it_button_menu');
add_action('init', 'pin_it_init');

// add pinit.js for Pin It button functionality
if(!function_exists('pin_it_init')) {
	function pin_it_init() {
		$is_button_enabled = get_option('pin_it_button_enabled');
		if(isset($is_button_enabled) && $is_button_enabled == "Y") {
			wp_enqueue_script('pinit-js', '//assets.pinterest.com/js/pinit.js', false, null, true);
		}
	}
}

// add the Pin It Button options menu
if(!function_exists('pin_it_button_menu')) {
	function pin_it_button_menu() {
		add_options_page('Pin It Button Options', 'Pin It Button', 'manage_options', 'pin_it_button', 'pin_it_button_options');
	}
}

// Add data-pin-hover to the script line for pinit.js
if(!function_exists("pinit_js_config")){
	function pinit_js_config($url){
		if (FALSE === strpos($url, 'pinit') || FALSE === strpos($url, '.js') || FALSE === strpos($url, 'pinterest.com')) {
			// this isn't a Pinterest URL, ignore it
			return $url;
		}
		$hover_op = get_option('pin_it_button_hover');
		// if image hover is enabled, append the data-pin-hover attribute
		if(isset($hover_op) && $hover_op == "Y") {
			return "$url' data-pin-hover='true";
		}
		else {
			return $url;
		}
	}
	
	add_filter('clean_url', 'pinit_js_config');
}

// Options page
if(!function_exists('pin_it_button_options')) {

	function pin_it_button_options()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$enabled_option = 'pin_it_button_enabled';
		$image_hover_option = 'pin_it_button_hover';
		$submitted_option = 'pin_it_button_submitted';
		
		$enabled_val = get_option($enabled_option);
		$image_hover_val = get_option($image_hover_option);
		
		$image_hover_checked = "";		
		
		if( isset($_POST[ $submitted_option] ) && $_POST[ $submitted_option] == 'Y') {
			// check and update variables
			if(isset($_POST[$image_hover_option])) {
				$image_hover_val = "Y";
				$enabled_val = "Y";
			}
			else{
				$image_hover_val = "N";
				$enabled_val = "N";
			}
			
			// update the new values
			update_option($enabled_option, $enabled_val);
			update_option($image_hover_option, $image_hover_val);
			?>
			<div class="updated"><p><strong>Settings saved!</strong></p></div>
			<?php
		}
		
		if(isset($image_hover_val) && $image_hover_val == "Y"){
			$image_hover_checked = "checked";
		}
		echo '<div class="wrap">';
		echo "<h2>Pin It Button Settings</h2>";
	    ?>
	    
	    <form name="form1" method="post" action="">
	    <input type="hidden" name="<?php echo $submitted_option; ?>" value="Y">
	    <p><input type="checkbox" name=<?php echo '"' . $image_hover_option . '" ' . $image_hover_checked; ?> value="Y"> Enable the Pin It hover button over images</p>
		<hr />
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
	
		</form>
		</div>
		<?php
		
	}
}
?>