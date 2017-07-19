<?php
return [
	 // Copy the following over to your project one in ROOT/config/
	'IdeHelper' => [
		// Controller prefixes to check for
		'prefixes' => [
			'Admin',
		],
		// Template paths to skip
		'skipTemplatePaths' => [
			'/src/Template/Bake/',
		]
	],
];
