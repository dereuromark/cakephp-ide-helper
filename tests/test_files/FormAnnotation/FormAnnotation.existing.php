<?php
namespace TestApp\Foo;

use TestApp\Form\DocForm;

class FormAnnotation {

	public function test() {
		$docForm = new DocForm();
		/** @uses \TestApp\Form\DocForm::_execute() */
		$docForm->execute();
	}
}
