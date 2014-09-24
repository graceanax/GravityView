<?php
/**
 * Display the search LINK input field
 *
 * @see class-search-widget.php
 */

global $gravityview_view;
$view_id = $gravityview_view->view_id;
$search_field = $gravityview_view->search_field;

$links_label = apply_filters( 'gravityview/extension/search/links_label', __( 'Show only:', 'gravity-view' ) );
$links_sep = apply_filters( 'gravityview/extension/search/links_sep', '&nbsp;|&nbsp;' );
?>

<div class="gv-search-box">

	<p class="gv-search-box-links">
		<?php echo esc_html( $links_label ); ?>

		<?php foreach( $search_field['choices'] as $k => $choice ) :

			if( $k != 0 ) { echo esc_html( $links_sep ); }?>

			<a href="<?php echo esc_url( add_query_arg( array( $search_field['name'] => $choice['value'] ) ) ); ?>">
				<?php echo esc_html( $choice['text'] ); ?>
			</a>

		<?php endforeach; ?>
	</p>
</div>