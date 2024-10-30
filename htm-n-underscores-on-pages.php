<?php
/*
Plugin Name: .htm and underscores on PAGES
Plugin URI: http://www.devcreations.com/plugins 
Description: Adds _ & .htm to pages. To remove underscores from url after detective you should republish the page/post to get the permalinks default.
Note that any posts you've created before this change, and rely on the %postname% permalink structure tag, will be broken.
In that case you'll need to go back and republish these posts, so the dashes are swapped out for the underscores.  
Author: DEBASIS PATTNAYAK
Version: 1.1
Author URI: http://www.devcreations.com/
*/

add_action('init', 'html_page_permalink', -1);
register_activation_hook(__FILE__, 'active');
register_deactivation_hook(__FILE__, 'deactive');


function html_page_permalink() {
	global $wp_rewrite;
 if ( !strpos($wp_rewrite->get_page_permastruct(), '.htm')){
		$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';
 }
}
add_filter('user_trailingslashit', 'no_page_slash',66,2);
function no_page_slash($string, $type){
   global $wp_rewrite;
	if ($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
		return untrailingslashit($string);
  }else{
   return $string;
  }
}

function my_sanitize_title($title, $raw_title, $context) {
   
$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '_', $title);

	if ( 'save' == $context ) {
		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '_', $title );

		// Strip these characters entirely
		$title = str_replace( array(
			// iexcl and iquest
			'%c2%a1', '%c2%bf',
			// angle quotes
			'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
			// curly quotes
			'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
			'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
			// copy, reg, deg, hellip and trade
			'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
			// acute accents
			'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
			// grave accent, macron, caron
			'%cc%80', '%cc%84', '%cc%8c',
		), '', $title );

		// Convert times to x
		$title = str_replace( '%c3%97', 'x', $title );
	}

	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '_', $title);
	$title = preg_replace('|-+|', '_', $title);
	$title = trim($title, '_');

	return $title;
}
add_filter('sanitize_title', 'my_sanitize_title', 10, 3);

function active() {
	global $wp_rewrite;
	if ( !strpos($wp_rewrite->get_page_permastruct(), '.htm')){
		$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';
 }
  $wp_rewrite->flush_rules();
}	
	function deactive() {
		global $wp_rewrite;
		$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
		$wp_rewrite->flush_rules();
	}
?>