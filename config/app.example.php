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
		// How behaviors are annotated into tables: ['mixin', 'extends', true, false, null]
		'tableBehaviors' => null, // null auto-detects based CakePHP version (>= 5.2.2 for extends) and always adds mixins.
		'arrayAsGenerics' => false, // Enable to have modern generics syntax (recommended) in doc blocks
		'objectAsGenerics' => false, // Enable to have modern generics syntax (recommended) in doc blocks
		'assocsAsGenerics' => false, // Enable to have modern generics syntax (NOT recommended yet) in doc blocks
		'genericsInParam' => false, // Enable to have generics in generated method param doc blocks (NOT recommended yet)
		'concreteEntitiesInParam' => false, // Enable to have specific entities in generated method param doc blocks (NOT recommended)
		// Set to false to disable, or string if you have a custom FQCN to be used
		'templateCollectionObject' => true,
		// Set to false to disable, defaults to mixed if enabled, you can also pass callable for logic
		'autoCollect' => true,
		// Can be strings or `/regex/` (e.g. `'/^\_.+$/i'` for underscore prefixed variables)
		'autoCollectBlacklist' => [
		],
		'preferLinkOverUsesInTests' => false, // Prefer `@link` annotations over `@uses` in test files, prevents PHPUnit to replace with annotations
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
		// Annotator task customization
		'annotators' => [
		],
		// For meta file generator
		'generatorTasks' => [
		],
		// A regex pattern - for Migrations plugin DatabaseTableTask generator task
		'ignoreDatabaseTables' => null,
		// A list of tables - for Migrations plugin DatabaseTableTask generator task
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
		'codeCompletionReturnType' => null, // Auto-detect based on controller/component, set to true/false to force one mode.
	],
];
