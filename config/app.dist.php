<?php

return [
	 // Copy the following over to your project one in ROOT/config/
	'IdeHelper' => [
		// Additional plugins that are not loaded, but should be included, use `-` prefix to exclude
		'plugins' => [
		],
		// Controller prefixes to check for
		'prefixes' => [
			'Admin',
		],
		// Template paths to skip
		'skipTemplatePaths' => [
			'/templates/Bake/',
		],
		'templateExtensions' => [
			'php',
		],
		'arrayAsGenerics' => false, // Enable to have modern generics syntax (recommended) in doc blocks
		// Set to false to disable
		'objectAsGenerics' => false, // Enable to have modern generics syntax (recommended) in doc blocks
		// Set to false to disable, or string if you have a custom FQCN to be used
		'templateCollectionObject' => true,
		// Set to false to disable, defaults to mixed if enabled, you can also pass callable for logic
		'autoCollect' => true,
		// Can be strings or `/regex/` (e.g. `'/^\_.+$/i'` for underscore prefixed variables)
		'autoCollectBlacklist' => [
		],
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
		// For Migrations plugin DatabaseTableTask generator task
		'skipDatabaseTables' => null,
		// For Illuminator tasks
		'illuminatorTasks' => [
		],
		'illuminatorIndentation' => "\t",
		// For code completion file generator
		'codeCompletionTasks' => [
		],
		// If a custom directory should be used, defaults to TMP otherwise
		'codeCompletionPath' => null,
	],
];
