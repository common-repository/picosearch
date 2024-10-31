<?php
/**
 * Admin View: Statistics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


?>

	<p> Loving this plugin? <a href="https://wordpress.org/plugins/picosearch">Give us a 5 star rating</a> on Wordpress.org! </p>
<h3> Get the Addons Pack! <?php picosearch_print_buy_button();?></h3>
	<p> The Addons Pack is a package of addons designed to make PicoSearch even better.</p>
	<p><?php picosearch_print_buy_button();?></p>
	<ol>
		<li>Search results are <strong>cached</strong> hence <strong>speeding up searches</strong> by up to 200%.</li>
		<li>An improved ranking algorithm  that takes into account the age of your content and its popularity. This uses <strong>artificial intelligence</strong> to provide even better results to your users.</li>
		<li>Display <strong>sponsored results</strong> above normal search results and watch your income shoot through the roof. Works best for <strong>affiliate marketers</strong> and online shops.</li>
		<li>Google Autocompletes. Fetches query suggestions using the Google Api hence less load on your server. Best for News sites and bloggers.</li>
		<li>Wikipedia Autocompletes. Fetches suggestions using the Wikipedia Api. Best for niche sites. </li>
		<li>Amazon Autocompletes. Fetches suggestions using the Amazon Api. Best for review sites.</li>
		<li>YouTube Autocompletes. Fetches suggestions using the YouTube Api. Best for Media related sites.</li>
	</ol>
	<p><?php picosearch_print_buy_button();?></p>
	<p><strong>We provide a 30 day money back guarantee!!</strong></p>
	<p> <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'picosearch-settings' ), 'admin.php' ) ) ); ?>">Go to Plugin Settings</a></p>

<script src="https://sellfy.com/js/api_buttons.js"></script>

