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
		],
		// Custom Entity field type mapping
		'typeMap' => [
		],
		// Always add annotations even if not needed
		'preemptive' => false,
		// For meta file generator
		'generatorTasks' => [
		],
	],
];
