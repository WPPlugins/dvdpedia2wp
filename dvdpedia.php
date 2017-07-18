<?php
/*
Plugin Name: DVDPedia to Wordpress
Plugin URI: http://www.neunzehn82.de/2011/08/dvdpedia-export-in-wordpress-integrieren/
Description: DVDPedia in Wordpress integrieren
Author: Christian Birkenbeul
Version: 1.0.0
Author URI: http://www.neunzehn82.de
*/

//Admin Menu 

add_action('admin_menu', 'wp_dvdpedia_menu');
add_option('wp_dvdpedia');

function wp_dvdpedia_register_settings() {
	// Register the settings
	register_setting( 'wp_dvdpedia_menu', 'wp_dvdpedia_options', 'db_shortcode' );
	}

function wp_dvdpedia_menu() {
	add_options_page('DVDPedia to Wordpress', 'DVDPedia2WP', 'manage_options', 'dvdpedia2wp', 'wp_dvdpedia_options');
}

function wp_dvdpedia_options() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo "<div class=\"wrap\">";
	echo "<h1>DVDPedia to Wordpress</h1>";
	echo "<a href=\"".home_url()."/wp-admin/media-new.php\">Upload your txt-file and paste the URL into the field.</a>";
	echo "<form method=\"post\">";
	echo "<input id=\"dp_bib\" name=\"wp_dvdpedia\" type=\"text\" value=\"".get_option('wp_dvdpedia')."\" />";
	echo "<input type=\"submit\" class=\"button-primary\" value=\"Speichern\" />";
	echo "</div>";
	
	
// Insert to database
	$dp_path = str_replace(home_url(), '.', $_POST['wp_dvdpedia']);
	update_option('wp_dvdpedia', $dp_path );

}
//Finds [dvdpedia] as shortcode

function dp_shortcode($attr) {
	$datei = get_option('wp_dvdpedia');
	$dp_array = file($datei);
	echo "<table>";
	echo "<tr><td><h3>Typ</h3></td><td><h3>Titel</h3></td><td><h3>Genre</h3></td><td><h3>Jahr</h3></td><td><h3>IMDb</h3></td><tr>";
	foreach ($dp_array as $dp_element) {
	$collum = explode(";",$dp_element);
	
	// If there an IMDb ID link to the site
	if ($collum[2] > 0) {
	echo "<tr><td>".$collum[0]."</td><td><a href=\"http://www.imdb.com/title/tt".$collum[2]."/\" title=\"".$collum[0]."\">" 
			 . $collum[1] . "</a></td>" . 
			 "<td>" . $collum[3] . "</td>" .
			 "<td>" . $collum[4] . "</td>" .
			 "<td>" . $collum[5] . "</td></tr>";
			}
			
	// If not don't link
	else {
		echo "<tr><td>$collum[0]</td><td>". $collum[1] . "</td>" . 
				 "<td>" . $collum[3] . "</td>" .
				 "<td>" . $collum[4] . "</td>" .
				 "<td>" . $collum[5] . "</td></tr>";
		}
	}
	echo "</table>";
}

// Register the short code to the function dp_shortcode()
add_shortcode( 'dvdpedia', 'dp_shortcode' );
// Register hooks for activation/deactivation. (I'm tidy.)
register_activation_hook( __FILE__, 'dp_activation' );
register_deactivation_hook( __FILE__, 'dp_deactivation' );
?>