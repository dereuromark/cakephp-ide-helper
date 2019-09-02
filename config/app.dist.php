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
		'templateExtensions' => [
			'ctp', 'php',
		],
		// Set to false to disable, set to empty string for no type added if type cannot be detected
		'autoCollect' => 'mixed',
		// Custom Entity field type mapping
		'typeMap' => [
		],
		// Default View class to use
		'viewClass' => null,
		// Plugins to include for View annotations
		'includedPlugins' => [
		],
		// Always add annotations/meta even if not yet needed
		'preemptive' => false,
		// For meta file generator
		'generatorTasks' => [
		],
		// For Illuminator tasks
		'illuminatorTasks' => [
		],
		// For code completion file generator
		'codeCompletionTasks' => [
		],
		// If a custom directory should be used, defaults to TMP otherwise
		'codeCompletionPath' => null,
	],
];
