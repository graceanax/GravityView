<?php
/**
 * Display the search single CHECKBOX input field ( on/off type)
 *
 * @see class-search-widget.php
 */

global $gravityview_view;
$view_id = $gravityview_view->view_id;
$search_field = $gravityview_view->search_field;

?>

<div class="gv-search-box">
	<label for="search-box-<?php echo esc_attr( $search_field['name'] ); ?>" class="gv-check-radio">
		<input type="checkbox" name="<?php echo esc_attr( $search_field['name'] ); ?>" value="1" id="search-box-<?php echo esc_attr( $search_field['name'] ); ?>" <?php checked( '1', $search_field['value'], true ); ?>>
			<?php echo esc_html(  $search_field['label'] ); ?>
	</label>
</div>