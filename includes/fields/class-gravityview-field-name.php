<?php

class GravityView_Field_Name extends GravityView_Field {

	var $name = 'name';

	var $_gf_field_class_name = 'GF_Field_Name';

	var $label = 'Name';

	var $group = 'advanced';

	public function __construct() {
		$this->label = esc_attr__( 'Name', 'gravityview' );
		parent::__construct();
	}
}

new GravityView_Field_Name;
