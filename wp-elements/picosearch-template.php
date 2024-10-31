<?php
/**
 * 
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !isset ( $elements ) OR ! is_array ( $elements ) ) {
	return;
}

$sections = array_unique ( $this->element_pluck('section') );
$current_section = empty( $_GET['tab'] ) ? $sections[0] : sanitize_title( $_GET['tab'] );

?>
<div class="wrap wp-elements-wrapper">
	<h1>PicoSearch</h1>
	<nav class="nav-tab-wrapper picosearch-nav-tab-wrapper">
			<?php
				foreach ( $sections as $section ) {
					
					$section_clean = sanitize_title($section);
					$section_url = admin_url( 'admin.php?page=picosearch&tab=' . $section_clean );
					$id = 'wp-section-wrapper-' . sanitize_html_class( $section );
					$class = sanitize_title($current_section) == $section_clean ? 'nav-tab-active' : '';
					
					echo "<a href='$section_url' id='$id' class='wp-section-wrapper nav-tab $class'>$section</a>";
				
				}
				do_action( 'picosearch_settings_tabs' );
			?>
	</nav>
	
	<form action="" method="post">
		<table class="form-table"  style="max-width: 750px;">
		<tbody>
			<?php
				$cbs = array (
					'wrapper_open_cb' => 'picosearch_render_wrapper_open',
					'wrapper_end_cb' => 'picosearch_render_wrapper_end',
				);
				
				foreach ( $elements as $element ) {
					$this->render_element( $element, true, $cbs );			
				}
				//Keep this line in your templates else options wont be saved
				wp_nonce_field( 'wp-elements' );
			?>
		</tbody>
		</table>
	</form>

</div>
<script>
	( function( $ ) {
		//Elements in a section have a class that is similar to its id
		var active = $('.wp-section-wrapper.nav-tab-active').attr('id');

		/* Hide inactive elements; We didnt do this via css so as to support non-js browsers
		 * We also didnt target the .form-group class so as to enable rendering 
		 * non-sectioned elements. This way they will render on all pages
		 */
		$('[class*="wp-section-wrapper-"]:not(.' + active + ')').addClass('d-none');
		
		$('.wp-section-wrapper').on('click', function( e ){
			e.preventDefault(); 
			$('#' + active ).removeClass('nav-tab-active');
			$( this ).addClass('nav-tab-active');
			active = $( this ).attr('id');
			$('[class*="wp-section-wrapper-"]:not(.' + active + ')').addClass('d-none');
			$('.' + active ).removeClass('d-none');
		
		});		
	})( jQuery );
</script>
<?php
