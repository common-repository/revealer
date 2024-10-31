<?php  
/* 
Plugin Name: Revealer 
Plugin URI: http://omniwp.com/plugins/revealer-a-wordpress-plugin/ 
Description: A powerful tool to reveal additional content once it becomes relevant to the reader.
Version: 2.0
Author: Nimrod Tsabari / omniWP
Author URI: http://omniwp.com
*/  
/*  Copyright 2012  Nimrod Tsabari / omniWP  (email : yo@omniwp.com)

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

define('REVEALER_VER', '2.0');
define('REVEALER_DIR', plugin_dir_url( __FILE__ ));

/* Revealer : Init */
/* --------------- */

function init_revealer() {

	wp_register_style('revealer-style', REVEALER_DIR . 'css/revealer.css');
	wp_enqueue_style('revealer-style');
	wp_register_script('revealer-script', REVEALER_DIR . 'js/revealer.min.js', array('jquery'));
	wp_enqueue_script('revealer-script');
}

add_action('wp_enqueue_scripts', 'init_revealer');

/* Revealer : Activation */
/* -------------------- */

define('REVEALER_NAME', 'Revealer');
define('REVEALER_SLUG', 'revealer_registration');

register_activation_hook(__file__,'omni_revealer_admin_activate');
add_action('admin_notices', 'omni_revealer_admin_notices');	

function omni_revealer_admin_activate() {
	$reason = get_option('omni_plugin_reason');
	if ($reason == 'nothanks') { 
		update_option('omni_plugin_on_list',0);
	} else {		
		add_option('omni_plugin_on_list',0);
		add_option('omni_plugin_reason','');
	}
}

function omni_revealer_admin_notices() {
	if ( get_option('omni_plugin_on_list') < 2 ){		
		echo "<div class='updated'><p>" . sprintf(__('<a href="%s">' . REVEALER_NAME . '</a> needs your attention.'), "options-general.php?page=" . REVEALER_SLUG). "</p></div>";
	}
} 

/*  Revealer : Admin Part  */
/* --------------------- */
/* Inspired by Purwedi Kurniawan's SEO Searchterms Tagging 2 Pluging */

function revealer_admin() {
	if (omni_revealer_list_status()) omni_revealer_thank_you(); 
}            

function revealer_admin_init() {
	$onlist = get_option('omni_plugin_on_list');
	if ($onlist < '2') add_options_page("Revealer| Registration", "Revealer| Registration", 1, "revealer_registration", "revealer_admin");
}

add_action('admin_menu', 'revealer_admin_init');

function omni_revealer_thank_you() {
	wp_redirect(admin_url());
}

function omni_revealer_list_status() {
	$onlist = get_option('omni_plugin_on_list');
	$reason = get_option('omni_plugin_reason');
	if ( trim($_GET['onlist']) == 1 || $_GET['no'] == 1 ) {
		$onlist = 2;
		if ($_GET['onlist'] == 1) update_option('omni_plugin_reason','onlist');
		if ($_GET['no'] == 1) {
			 if ($reason != 'onlist') update_option('omni_plugin_reason','nothanks');
		}
		update_option('omni_plugin_on_list', $onlist);
	} 
	if ( ((trim($_GET['activate']) != '' && trim($_GET['from']) != '') || trim($_GET['activate_again']) != '') && $onlist != 2 ) { 
		update_option('omni_plugin_list_name', $_GET['name']);
		update_option('omni_plugin_list_email', $_GET['from']);
		$onlist = 1;
		update_option('omni_plugin_on_list', $onlist);
	}
	if ($onlist == '0') {
		if (isset($_GET['noheader'])) require_once(ABSPATH . 'wp-admin/admin-header.php');
		omni_revealer_register_form_1('revealer_registration');
	} elseif ($onlist == '1') {
		if (isset($_GET['noheader'])) require_once(ABSPATH . 'wp-admin/admin-header.php');
		$name = get_option('omni_plugin_list_name');
		$email = get_option('omni_plugin_list_email');
		omni_revealer_do_list_form_2('revealer_confirm',$name,$email);
	} elseif ($onlist == '2') {
		return true;
	}
}

function omni_revealer_register_form_1($fname) {
	global $current_user;
	get_currentuserinfo();
	$name = $current_user->user_firstname;
	$email = $current_user->user_email;
?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px solid #BBB; padding: 20px;outline-offset: 2px;outline: 1px dashed #eee;box-shadow: 0 0 10px 2px #bbb;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">
			Please register the plugin...
		</p>
		<p>Registration is <strong style="font-size: 1.1em;">Free</strong> and only has to be done <strong style="font-size: 1.1em;">once</strong>. If you've register before or don't want to register, just click the "No Thank You!" button and you'll be redirected back to the Dashboard.</p>
		<p>In addition, you'll receive a a detailed tutorial on how to use the plugin and a complimentary subscription to our Email Newsletter which will give you a wealth of tips and advice on Blogging and Wordpress. Of course, you can unsubscribe anytime you want.</p>
		<p><?php omni_revealer_registration_form($fname,$name,$email);?></p>
		<p style="background: #F8F8F8; border: 1px dotted #ddd; padding: 10px; border-radius: 5px; margin-top: 20px;"><strong>Disclaimer:</strong> Your contact information will be handled with the strictest of confidence and will never be sold or shared with anyone.</p>
	</div>	
<?php
}

function omni_revealer_registration_form($fname,$uname,$uemail,$btn='Register',$hide=0, $activate_again='') {
	$wp_url = get_bloginfo('wpurl');
	$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
	$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;noheader=true';
	$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;onlist=1'.'&amp;noheader=true';
	$nothankyou_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;no=1'.'&amp;noheader=true';
	?>
	
	<?php if ( $activate_again != 1 ) { ?>
	<script><!--
	function trim(str){ return str.replace(/(^\s+|\s+$)/g, ''); }
	function imo_validate_form() {
		var name = document.<?php echo $fname;?>.name;
		var email = document.<?php echo $fname;?>.from;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var err = ''
		if ( trim(name.value) == '' )
			err += '- Name Required\n';
		if ( reg.test(email.value) == false )
			err += '- Valid Email Required\n';
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php } ?>
	<form name="<?php echo $fname;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($activate_again!=1){;?>onsubmit="return imo_validate_form();"<?php }?> style="text-align:center;" >
		<input type="hidden" name="meta_web_form_id" value="1222167085" />
		<input type="hidden" name="listname" value="omniwp_plugins" />  
		<input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
		<input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
		<input type="hidden" name="meta_adtracking" value="omniwp_plugins_adtracking" />
		<input type="hidden" name="meta_message" value="1">
		<input type="hidden" name="meta_required" value="from,name">
		<input type="hidden" name="meta_forward_vars" value="1">	
		 <?php if ( $activate_again == 1 ) { ?> 	
			 <input type="hidden" name="activate_again" value="1">
		 <?php } ?>		 
		<?php if ( $hide == 1 ) { ?> 
			<input type="hidden" name="name" value="<?php echo $uname;?>">
			<input type="hidden" name="from" value="<?php echo $uemail;?>">
		<?php } else { ?>
			<p>Name: </td><td><input type="text" name="name" value="<?php echo $uname;?>" size="25" maxlength="150" />
			<br />Email: </td><td><input type="text" name="from" value="<?php echo $uemail;?>" size="25" maxlength="150" /></p>
		<?php } ?>
		<input class="button-primary" type="submit" name="activate" value="<?php echo $btn; ?>" style="font-size: 14px !important; padding: 5px 20px;" />
	</form>
    <form name="nothankyou" method="post" action="<?php echo $nothankyou_url;?>" style="text-align:center;">
	    <input class="button" type="submit" name="nothankyou" value="No Thank You!" />
    </form>
	<?php
}

function omni_revealer_do_list_form_2($fname,$uname,$uemail) {
	$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to register the plugin.';
	if ( trim($_GET['activate_again']) != '' && $msg != '' ) {
		echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
	}
	?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px dotted #bbb; padding: 20px;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">Thank you...</p>
		<p>A confirmation email has just been sent to your email @ "<?php echo $uemail;?>". In order to register the plugin, check your email and click on the link in that email.</p>
		<p>Click on the button below to Verify and Activate the plugin.</p>
		<p><?php omni_revealer_registration_form($fname.'_0',$uname,$uemail,'Verify and Activate',$hide=1,$activate_again=1);?></p>
		<p>Disclaimer: Your contact information will be handled with the strictest confidence and will never be sold or shared with third parties.</p>
	</div>	
	<?php
}

/* Adding TinyMCE Button */
/* --------------------- */

add_filter('mce_external_plugins', "revealer_register");
add_filter('mce_buttons', 'revealer_add_button', 0);
function revealer_add_button($buttons){
	array_push($buttons, "|", "revealerplugin");
	return $buttons;
}
function revealer_register($plugin_array){
	$url = REVEALER_DIR . "btn/revealer.button.js";
	$plugin_array['revealerplugin'] = $url;
	return $plugin_array;
}
function revealer_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

add_filter( 'tiny_mce_version', 'revealer_refresh_mce');


//* Revealer : Shortcode adction */
/* --------------------------- */
/* @author Nimrod Tsabari
 * @since 0.1b
 */
function set_revealer($atts,$content=null) {
  extract(shortcode_atts(array(
      'role'			=> '', 			// switch | highlight
      'connector'   	=> 'default',	// a string, no whitespace
      'position'		=> 'center',	// left | right| fixed | center
      'style'			=> 'none',		// unerline | highlight
      'top'				=> '',			// number
      'left'			=> '',			// number
      'arrow'			=> '',			// plus } icon name
      'arrow_color'		=> 'gray',		// black | white | gray
      'border_color'	=> 'gray',		// black | white | gray
      'border_width'	=> '2',			// number
      'fold'			=> '30',		// number
      'fold_top'		=> '',			// number
      'fold_bottom'		=> '',			// number
      'width'			=> 'auto',		// number
      'height'			=> 'auto',		// number
      'radius'			=> '10',		// number
      'background_color'=> 'black',		// color / hex / rgba
      'padding'			=> '5',			// number
      'fixed_top'		=> '',			// number
      'fixed_right'		=> '',			// number
      'fixed_bottom'	=> '',			// number
      'fixed_left'		=> '',			// number
      'fixed_pos'		=> 'none',		// none | top-left | top-right | bottom-left | bottom-right
      'effect'			=> 'fade'		// fade | toggle | slide-left | slide-right
    ), $atts));

  /* Variables */
  $html = '';
  $role	= strtolower($role);
  $connector_class = 'revealer-connector-' . trim($connector);
  $y = trim($top);
  $x = trim($left);
  $arrow = trim($arrow);
  $fold = trim($fold);
  $fold_top = trim($fold_top);
  $fold_bottom = trim($fold_bottom);
  $radius = trim($radius);
  $position = trim(strtolower($position));
  $effect = trim(strtolower($effect));
  
  if (!in_array($position,array('left','right','fixed'))) $position = 'center';
  
  $styles = "";
  
  // Highlight Styling
  $styles .= 'width: ' . $width . 'px; ';
  $styles .= 'height: ' . $height . 'px; ';
  $styles .= 'border-radius: ' . $radius . 'px; ';
  $styles .= 'background-color: ' . $background_color . '; ';
  $styles .= 'border-color:' . $border_color . '; '; 
  $styles .= 'border-width:' . $border_width . 'px; '; 
  $styles .= 'padding:' . $padding . 'px; ';


  // Highlight Positioning
  switch ($position) {
      case 'left':
		  $pos_class = ' revealer-pos-xxleftxx'.$x.'xx'.$y;
          break;
      case 'right':
		  $pos_class = ' revealer-pos-xxrightxx'.$x.'xx'.$y;
          break;
	  case 'fixed':
		  $styles .= 'position: fixed;';
		  $pos_class = ' revealer-pos-xxfixed';
	  	  switch ($fixed_pos) {
		  	case 'top-left' : 
				$styles .= 'top:20px;left:20px;';
				break;
		  	case 'top-right' : 
				$styles .= 'top:20px;right:20px;';
				break;
		  	case 'bottom-left' : 
				$styles .= 'bottom:20px;left:20px;';
				break;
		  	case 'bottom-right' : 
				$styles .= 'bottom:20px;right:20px;';
				break;
			default: 
				if ($fixed_top != '') $styles .= 'top:' . $fixed_top . 'px;';
				if ($fixed_right != '') $styles .= 'right:' . $fixed_right . 'px;';
				if ($fixed_bottom != '') $styles .= 'bottom:' . $fixed_bottom . 'px;';
				if ($fixed_left != '') $styles .= 'left:' . $fixed_left . 'px;';
				break;
	  	  }
		  break;
      default:
		  $pos_class = ' revealer-pos-xxcenterxx'. $x . 'xx' . $y;
		  $styles .= 'margin-left: ' . $x . 'px; ';
          break;
  }
  
  $arrow_class = ' revealer-arrow-' . $arrow . '-' . $arrow_color;
  
  if (($fold_top != '') && ($fold_bottom != '')) {
  	 $fold_class = ' revealer-fold-' . $fold_top . 'xx' . $fold_bottom;
  } else {
  	 $fold_top = 50 - intval($fold)/2;
  	 $fold_bottom = 50 + intval($fold)/2;
  	 $fold_class = ' revealer-fold-' . $fold_top . 'xx' . $fold_bottom;
  }

  if (in_array($effect, array('fade','toggle','slide-left','slide-right','slide-top','slide-bottom'))) {
	  if (($effect == 'slide-left') || ($effect == 'slide-right')) {
	  	 $effect_class = ' revealer-effect-' . $x . '-'. $effect;
	  } else {
  	  	$effect_class = ' revealer-effect-' . $effect;
	  }
  }

  if (in_array($role, array('switch','highlight'))) {
	  switch ($role) {
	      case 'switch':
			  $styles_class = ' revealer-switch-styles-' . $style;
	          $html .= '<span class="revealer-switch ' . $connector_class . $styles_class . $fold_class . $effect_class . '">' . $content . '</span>';
	          break;
	      case 'highlight':
			  $html .= '<div class="revealer-box '. $connector_class . $pos_class . $arrow_class . $box_styles_class . '" style="' . $styles . '">' . do_shortcode($content) . '</div>';
	         break;
	      default:
	          break;
	  }
  }	  

  return $html;
 
}

add_shortcode( 'revealer', 'set_revealer' );
?>