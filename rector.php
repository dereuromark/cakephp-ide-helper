<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src',
		__DIR__ . '/tests',
	])
	->withSkip([
		// Hand-crafted annotator inputs / generated code — must not be rewritten.
		__DIR__ . '/tests/test_app',
		__DIR__ . '/tests/test_files',

		// Conflicts with house style: plugins deliberately omit declare(strict_types=1).
		SafeDeclareStrictTypesRector::class,
		// Conflicts with house style: we prefer `if ($var)` over explicit `!== null` / `!== 0`.
		ExplicitBoolCompareRector::class,
		DisallowedEmptyRuleFixerRector::class,

		// Opinionated, large churn — revisit deliberately, not in a sweep.
		ClassPropertyAssignToConstructorPromotionRector::class,
		ClosureToArrowFunctionRector::class,
		// Adds untyped `public $Foo;` properties (CS then wants a @var docblock); cake core skips it.
		CompleteDynamicPropertiesRector::class,

		// Docblock cleanup is a separate, deliberate pass — keep redundant tags for now.
		RemoveUselessParamTagRector::class,
		RemoveUselessReturnTagRector::class,
		RemoveUselessVarTagRector::class,
		RemoveNonExistingVarAnnotationRector::class,

		// Broken / unsafe rules (see upstream issues):
		// rectorphp/rector#9767 — converts a string callable to a namespace-relative
		// first-class callable, turning working code into a fatal class-not-found.
		FunctionFirstClassCallableRector::class,
		// rectorphp/rector#9768 — rewrites empty() on a possibly-undefined variable to
		// `=== []`, introducing an undefined-variable warning and flipping behavior.
		SimplifyEmptyCheckOnEmptyArrayRector::class,
		// Drops the leading backslash from class-name strings used as data (e.g. docblock
		// fragments); the deprecated should_keep_pre_slash knob no longer prevents it.
		StringClassNameToClassConstantRector::class,
		// get_class() -> ::class invalidates existing phpstan ignore patterns; opt in per plugin.
		ClassOnObjectRector::class,
	])
	->withPhpSets(php82: true)
	->withPreparedSets(
		deadCode: true,
		codeQuality: true,
	);
