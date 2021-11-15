<?php
namespace TestApp\Foo;

use TestApp\Form\DocForm;

class FormAnnotation {

	public function test() {
		$docForm = new DocForm();
		$docForm->execute();
	}
}
