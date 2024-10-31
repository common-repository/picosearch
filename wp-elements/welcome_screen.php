<?php
/**
 * Admin View: Statistics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
list( $display_version ) = explode( '-', picosearch()->version );
$tab = isset($_GET['tab'])? $_GET['tab'] : 'picosearch';
?>


<div class="wrap about-wrap picosearch-about-wrap">
<style>
	.picosearch-badge {
		position: absolute;
		top: 0;
		right: 0;
		padding-top: 100px;
		padding-bottom: 42px;
		height: 50px;
		width: 173px;
		color: #fafafa;
		font-weight: bold;
		font-size: 32px;
		text-align: center;
		margin: 0 -5px;
		background-color: #2196f3;
	}
	
	.badge-br {
		display: block;
		margin-top: 22px;
		font-size: 14px;
	}
	
	.picosearch-about-wrap p,
	.picosearch-about-wrap li{
		font-size: 18px;
		color: #000;
		margin-bottom: 32px;
		max-width: 640px;
	}
	
	.d-none{
		display: none;
	}
	
	.picosearch-about-wrap li{margin-bottom: 20px;}
</style>

	<h1><?php printf( esc_html__( 'Welcome to Picosearch %s', 'picosearch' ),  $display_version ); ?></h1>
	<div class="about-text"><?php printf( esc_html__( 'Picosearch %s Shows your users relevant search results, highlights search terms in the results, suggests search terms and logs search queries among other features to your in-built search engine', 'picosearch' ), $display_version ); ?></div>
	<div class="picosearch-badge"><?php printf( esc_html__( 'Picosearch %s Version %s', 'picosearch' ), '<span class="badge-br">', $display_version . '</span>' ); ?></div>
	
	<h2 class="nav-tab-wrapper">
		<a id="doc-tab" class="nav-tab nav-tab-active" href="#doc">
			<?php esc_html_e( 'Picosearch', 'picosearch' ); ?>
		</a><a id="how-tab" class="nav-tab" href="#how">
			<?php esc_html_e( 'About', 'picosearch' ); ?>
		</a>
	</h2>
	
	<div id="picosearch-doc">
		<?php include_once('documentation.php'); ?>
	</div>
	
	<div id="picosearch-how" class='d-none'>
		<?php include_once('how-it-works.php'); ?>
	</div>

</div>

<script>
	( function( $ ) {

		$('#doc-tab').on('click', function(){
			$( this ).addClass('nav-tab-active');
			$("#picosearch-doc").removeClass('d-none');
			$("#picosearch-how").addClass('d-none');
			$('#how-tab').removeClass('nav-tab-active');
		});
		
		$('#how-tab').on('click', function(){
			$('#doc-tab').removeClass('nav-tab-active');
			$( this ).addClass('nav-tab-active');
			$("#picosearch-doc").addClass('d-none');
			$("#picosearch-how").removeClass('d-none');
						
		});	
	})( jQuery );
</script>
