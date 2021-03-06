<?php

/** ----------------------------------
 * entending mega menu plugin features
 * ----------------------------------- */


/** ----------------------------------
 * STEP 1 :: create module options [example : portfolio option]
 * ----------------------------------- */
add_action( 'wip_megamenu_admin_module_lists', 'extends_megamenu_module_lists');
function extends_megamenu_module_lists(){

  /** info:
	 * 1. each <a> Must wrapped with li
	 * 2. <a> tag must have class 'mega-menu-button', so javscript can detect it as module option
	 * 3. <a> title = not important, just work as info when people hovering the button
	 * 4. <a> data-name = will shown as box title
	 * 5. <a> data-type = no need to translate, must unique, will use this for detect the content type, no space only alphabhet and hyphens allowed
	 * 6. <i class="*"></i> - the class name use font-awesome icons.
	 *
	 * use add_action( 'wip_megamenu_admin_module_lists', function_name );
	 */
	echo '<li><a class="mega-menu-button" href="#" title="' . __('Add Portfolio module', 'wip') . '" data-name="' .  __('Portfolio', 'wip') . '" data-type="portfolio"><i class="icon-briefcase"></i></a></li>';
}



/** ----------------------------------
 * STEP 2 :: Plugin call the modules name and title dynamically - register yours!
 * ----------------------------------- */

/**
 * @title = returning title, if the module is custom, this var will remain empty
 * @type = module type name
 *
 * call filter wip_megamenu_content_type_name_'your module name'
 */
add_filter('wip_megamenu_content_type_name_portfolio', 'extends_megamenu_option_name', 10, 2);
function extends_megamenu_option_name( $title, $type ){

	// check your <a> data-type value above
	if( $type == 'portfolio' ) {

		// return your <a> data-name.
		$title = __('Portfolio', 'wip');
	}

	return $title;
}



/** ----------------------------------
 * STEP 3 :: Build the form settings for your module
 * ----------------------------------- */

/**
 * @content_id = the content id, if it's empty will generated by js dynamically, default must set to {content_id}
 * @args = array of form value, default to empty array()
 */
function extends_megamenu_my_portfolio_settings_form( $content_id = "{content_id}", $args = array() ){
	// Define default form value for your module
	// you can extends this value based on your form settings
	// next, you just need type the var name inside the default args to call the value , e.g $title, $cat_id, $number
	extract( wp_parse_args(
		$args, array(
			'title' 	=> '',
			'cat_id' 	=> 0,
			'number' 	=> 8
		) 
	), EXTR_SKIP);

	// ob_start
	ob_start();
	?>

	<?php
		// watch carefully how {{ $content_id }} is called in each form element's name and id
		// <input type="text" class="widefat" name="your-input-name[$content_id]" id="your-input-name-$content_id" value="" />
	?>

		<?php // refer to $title above ?>
		<p class="mega-form">
			<label for="content-title-<?php echo $content_id; ?>"><?php print __('Title / Heading (Optional)', 'wip'); ?></label>
			<input type="text" class="widefat" name="content-title[<?php echo $content_id; ?>]" id="content-title-<?php echo $content_id; ?>" value="<?php echo stripslashes( esc_attr($title) ); ?>" />
		</p>

		<p class="mega-form">
			<label for="mega-portfolio-cat-<?php echo $content_id; ?>"><?php print __('Selecy a Portfolio category? (optional)', 'wip'); ?></label>

				<?php 
				// check plugin file wip-mega-menu-functions.php line 14
				// if you can have custom taxonomy you can simply use this function to call in your form
				// change 'category' with your taxonomy name
				echo wip_theme_get_tax_lists('category', 'mega-portfolio-cat['.$content_id.']', 'mega-portfolio-cat-'.$content_id, 'widefat', __('All Porfolio Categories', 'wip'), $cat_id );
				?>
			
		</p>

		<?php // refer to $number above ?>
		<p class="mega-form">
			<label for="mega-portfolio-number-<?php echo $content_id; ?>"><?php print __('Number of posts to show','wip'); ?></label>
			<input type="text" class="widefat" name="mega-portfolio-number[<?php echo $content_id; ?>]" id="mega-portfolio-number-<?php echo $content_id; ?>" value="<?php echo stripslashes( esc_attr($number) ); ?>" />
		</p>

	<?php

	return ob_get_clean();
}




/** ----------------------------------
 * STEP 4 :: Inject the localize scripts value from plugin
 * ----------------------------------- */

add_filter( 'wip_megamenu_admin_localize_script', 'extends_megamenu_localize_scripts' );
function extends_megamenu_localize_scripts( $localize ) {

	// $localize[ 'your_module_name' + '_form'] >>>> check your <a> data-type value
	// so if you have another module with name 'gallery' - you should register your module settings form with $localize['gallery_form']
	$localize['portfolio_form'] = extends_megamenu_my_portfolio_settings_form();

	return $localize;
}



/** ----------------------------------
 * STEP 5 :: Tell the plugin the right form when outputing the saved data
 * ----------------------------------- */

/**
 * $form = html module form settings, if module is custom, this var will remain empty
 * $type = module type - check your <a> data-type value
 * $contentID = unique content id, dynamically generated by plugin
 * $args = the form value in array
 *
 * call filter wip_megamenu_settings_form_'your module name'
 */
add_filter( 'wip_megamenu_settings_form_portfolio', 'extends_megamenu_saved_form', 10, 4 );
function extends_megamenu_saved_form( $form, $type, $contentID, $args ) {

	// check your <a> data-type value above
	if( $type == 'portfolio' ) {
		//call your module form function 
		$form = extends_megamenu_my_portfolio_settings_form( $contentID, $args );
	}

	return $form;

}




/** ----------------------------------
 * STEP 6 :: Tell the plugin the accepted arguments for your form settings, this is will used when people save the megamenu content and layout
 * ----------------------------------- */

/**
 * check function extends_megamenu_my_portfolio_settings_form()
 * the arguments name MUST same with args that you've define in the function above
 *
 * $settings = contain array of arguments name and value; if module is custom, this var will remain empty
 * $type = module type - check your <a> data-type value
 * $contentID = unique content id, dynamically generated by plugin
 * 
 * call filter wip_megamenu_admin_content_settings_saved_'your module name'
 */
add_filter( 'wip_megamenu_admin_content_settings_saved_portfolio', 'extends_megamenu_get_settings_saved', 10, 3 );
function extends_megamenu_get_settings_saved( $settings, $type, $contentID ){
	// check your <a> data-type value
	if( $type == 'portfolio' ) {

		// the name of form elements MUST match with your module settings form
		$settings = array (
			'title' 	=> ( isset( $_POST['content-title'][$contentID] ) ? stripslashes($_POST['content-title'][$contentID]) : ''  ),
			'cat_id' 	=> ( isset( $_POST['mega-portfolio-cat'][$contentID] ) ? stripslashes($_POST['mega-portfolio-cat'][$contentID]) : ''  ),
			'number'	=> ( isset( $_POST['mega-portfolio-number'][$contentID] ) ? $_POST['mega-portfolio-number'][$contentID] : 8  )
			);
	}

	return $settings;
}


/** 
 * STEP 7 :: 	last, create a folder under the theme dir, named 'megamenu-template',
 * 				create a file 'module-name'.php --> refer to your <a> data-type value as module name
 * 				place it under 'megamenu-template' folder. e.g 'portfolio.php'
 * 
 * at this template file you can simply call the argument name to get the value
 * e.g $title, $cat_id, $number - and $content_id for module unique id, incase you create something that need unique id for your module
 * or simply check the default template that comes with plugin to get the idea :)
 */
