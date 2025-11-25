<?php

namespace IdeHelper\Illuminator\Task;

use Cake\Core\App;
use Cake\Utility\Inflector;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionException;

/**
 * Replaces string field names with entity FIELD_* constants in Table class queries.
 *
 * This task targets Table classes and replaces string literals in query builder
 * method calls (select, where, orderBy, etc.) with the corresponding entity
 * constants where they exist.
 *
 * Example:
 * Before: $this->find()->where(['name' => 'foo']);
 * After: $this->find()->where([Wheel::FIELD_NAME => 'foo']);
 */
class FieldConstantUsageTask extends AbstractTask {

	/**
	 * Methods grouped by class that accept field names as arguments.
	 *
	 * Structure: [ClassName => [methodName => [argPositions]]]
	 *
	 * @var array<string, array<string, array<int>>>
	 */
	protected const CLASS_METHODS = [
		// Query builder methods (SelectQuery, UpdateQuery, DeleteQuery)
		'Cake\ORM\Query\SelectQuery' => [
			'select' => [0],
			'where' => [0],
			'andWhere' => [0],
			'orWhere' => [0],
			'orderBy' => [0],
			'orderByAsc' => [0],
			'orderByDesc' => [0],
			'groupBy' => [0],
			'distinct' => [0],
			'contain' => [0],
		],
		// Validator methods
		'Cake\Validation\Validator' => [
			'add' => [0],
			'addNested' => [0],
			'addNestedMany' => [0],
			'remove' => [0],
			'hasField' => [0],
			'requirePresence' => [0],
			'allowEmptyFor' => [0],
			'allowEmptyString' => [0],
			'notEmptyString' => [0],
			'allowEmptyArray' => [0],
			'notEmptyArray' => [0],
			'allowEmptyFile' => [0],
			'notEmptyFile' => [0],
			'allowEmptyDate' => [0],
			'notEmptyDate' => [0],
			'allowEmptyTime' => [0],
			'notEmptyTime' => [0],
			'allowEmptyDateTime' => [0],
			'notEmptyDateTime' => [0],
			'notBlank' => [0],
			'alphaNumeric' => [0],
			'notAlphaNumeric' => [0],
			'asciiAlphaNumeric' => [0],
			'notAsciiAlphaNumeric' => [0],
			'lengthBetween' => [0],
			'creditCard' => [0],
			'greaterThan' => [0],
			'greaterThanOrEqual' => [0],
			'lessThan' => [0],
			'lessThanOrEqual' => [0],
			'equals' => [0],
			'notEquals' => [0],
			'sameAs' => [0],
			'notSameAs' => [0],
			'equalToField' => [0],
			'notEqualToField' => [0],
			'greaterThanField' => [0],
			'greaterThanOrEqualToField' => [0],
			'lessThanField' => [0],
			'lessThanOrEqualToField' => [0],
			'date' => [0],
			'dateTime' => [0],
			'time' => [0],
			'localizedTime' => [0],
			'boolean' => [0],
			'decimal' => [0],
			'email' => [0],
			'enum' => [0],
			'ip' => [0],
			'ipv4' => [0],
			'ipv6' => [0],
			'minLength' => [0],
			'minLengthBytes' => [0],
			'maxLength' => [0],
			'maxLengthBytes' => [0],
			'numeric' => [0],
			'naturalNumber' => [0],
			'nonNegativeInteger' => [0],
			'range' => [0],
			'url' => [0],
			'urlWithProtocol' => [0],
			'inList' => [0],
			'uuid' => [0],
			'uploadedFile' => [0],
			'latLong' => [0],
			'latitude' => [0],
			'longitude' => [0],
			'ascii' => [0],
			'utf8' => [0],
			'utf8Extended' => [0],
			'integer' => [0],
			'array' => [0],
			'scalar' => [0],
			'hexColor' => [0],
			'multipleOptions' => [0],
			'hasAtLeast' => [0],
			'hasAtMost' => [0],
		],
		// Rules checker methods
		'Cake\ORM\RulesChecker' => [
			'existsIn' => [0],
			'isUnique' => [0],
		],
	];

	/**
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'methods' => null,
	];

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);

		return $className !== 'Table' && str_ends_with($className, 'Table');
	}

	/**
	 * @param string $content
	 * @param string $path Path to file.
	 * @return string
	 */
	public function run(string $content, string $path): string {
		$entityInfo = $this->resolveEntityInfo($content, $path);
		if (!$entityInfo) {
			return $content;
		}

		$entityClass = $entityInfo['class'];
		$entityShortName = $entityInfo['shortName'];
		$fieldConstants = $entityInfo['constants'];

		if (!$fieldConstants) {
			return $content;
		}

		$replacements = $this->findReplacements($content, $fieldConstants, $entityShortName);
		if (!$replacements) {
			return $content;
		}

		return $this->applyReplacements($content, $replacements, $entityClass, $entityShortName);
	}

	/**
	 * Resolve the entity class and its FIELD_* constants from a Table class.
	 *
	 * @param string $content
	 * @param string $path
	 * @return array{class: string, shortName: string, constants: array<string, string>}|null
	 */
	protected function resolveEntityInfo(string $content, string $path): ?array {
		$namespace = $this->extractNamespace($content);
		if (!$namespace) {
			return null;
		}

		$className = pathinfo($path, PATHINFO_FILENAME);
		$tableClass = $namespace . '\\' . $className;

		// Derive entity class from table class
		// App\Model\Table\WheelsTable -> App\Model\Entity\Wheel
		$entityNamespace = str_replace('\\Table\\', '\\Entity\\', $namespace);
		$entityName = Inflector::singularize(substr($className, 0, -5)); // Remove 'Table' suffix
		$entityClass = $entityNamespace . '\\' . $entityName;

		// Try to load entity class and get its constants
		$fieldConstants = $this->getEntityFieldConstants($entityClass);
		if (!$fieldConstants) {
			// Try App-level entity path for plugin tables
			$appEntityClass = App::className($entityName, 'Model/Entity');
			if ($appEntityClass) {
				$fieldConstants = $this->getEntityFieldConstants($appEntityClass);
				$entityClass = $appEntityClass;
			}
		}

		if (!$fieldConstants) {
			return null;
		}

		$parts = explode('\\', $entityClass);

		return [
			'class' => $entityClass,
			'shortName' => end($parts),
			'constants' => $fieldConstants,
		];
	}

	/**
	 * Extract namespace from PHP content.
	 *
	 * @param string $content
	 * @return string|null
	 */
	protected function extractNamespace(string $content): ?string {
		if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
			return trim($matches[1]);
		}

		return null;
	}

	/**
	 * Get FIELD_* constants from an entity class.
	 *
	 * @param string $entityClass
	 * @return array<string, string> Map of field name => constant name
	 */
	protected function getEntityFieldConstants(string $entityClass): array {
		try {
			if (!class_exists($entityClass)) {
				return [];
			}

			$reflection = new ReflectionClass($entityClass);
			$constants = $reflection->getConstants();

			$fieldConstants = [];
			foreach ($constants as $name => $value) {
				if (str_starts_with($name, EntityFieldTask::PREFIX) && is_string($value)) {
					$fieldConstants[$value] = $name;
				}
			}

			return $fieldConstants;
		} catch (ReflectionException $e) {
			return [];
		}
	}

	/**
	 * Build a flat method lookup from the nested CLASS_METHODS structure.
	 *
	 * @return array<string, array{class: string, positions: array<int>}>
	 */
	protected function buildMethodLookup(): array {
		$classMethods = $this->getConfig('classMethods') ?? static::CLASS_METHODS;
		$lookup = [];

		foreach ($classMethods as $className => $methods) {
			foreach ($methods as $methodName => $positions) {
				// If method exists in multiple classes, prefer the first one
				// This is fine since we also do context-based matching
				if (!isset($lookup[$methodName])) {
					$lookup[$methodName] = [
						'class' => $className,
						'positions' => $positions,
					];
				}
			}
		}

		return $lookup;
	}

	/**
	 * Find all string literals that should be replaced with constants.
	 *
	 * @param string $content
	 * @param array<string, string> $fieldConstants
	 * @param string $entityShortName
	 * @return array<int, array{line: int, startPos: int, endPos: int, field: string, constant: string, position: string}>
	 */
	protected function findReplacements(string $content, array $fieldConstants, string $entityShortName): array {
		$parser = (new ParserFactory())->createForNewestSupportedVersion();

		try {
			$ast = $parser->parse($content);
		} catch (\Throwable $e) {
			return [];
		}

		if ($ast === null) {
			return [];
		}

		$methodLookup = $this->buildMethodLookup();

		$visitor = new class ($fieldConstants, $methodLookup) extends NodeVisitorAbstract {
			/**
			 * @var array<string, string>
			 */
			private array $fieldConstants;

			/**
			 * @var array<string, array{class: string, positions: array<int>}>
			 */
			private array $methodLookup;

			/**
			 * @var array<int, array{line: int, startPos: int, endPos: int, field: string, constant: string, position: string}>
			 */
			public array $replacements = [];

			/**
			 * Track variable types from method parameters.
			 *
			 * @var array<string, string>
			 */
			private array $variableTypes = [];

			/**
			 * @param array<string, string> $fieldConstants
			 * @param array<string, array{class: string, positions: array<int>}> $methodLookup
			 */
			public function __construct(array $fieldConstants, array $methodLookup) {
				$this->fieldConstants = $fieldConstants;
				$this->methodLookup = $methodLookup;
			}

			/**
			 * @param \PhpParser\Node $node
			 * @return int|null
			 */
			public function enterNode(Node $node): ?int {
				// Track method parameters with type hints
				if ($node instanceof Node\Stmt\ClassMethod) {
					$this->variableTypes = [];
					foreach ($node->params as $param) {
						if ($param->type instanceof Node\Name && $param->var instanceof Node\Expr\Variable) {
							$varName = $param->var->name;
							if (is_string($varName)) {
								$typeName = $param->type->toString();
								// Store full class name for matching
								$this->variableTypes[$varName] = $typeName;
							}
						}
					}

					return null;
				}

				if (!$node instanceof Node\Expr\MethodCall) {
					return null;
				}

				if (!$node->name instanceof Node\Identifier) {
					return null;
				}

				$methodName = $node->name->name;
				if (!isset($this->methodLookup[$methodName])) {
					return null;
				}

				// Check if we can determine the class context
				$expectedClass = $this->methodLookup[$methodName]['class'];
				if (!$this->isValidContext($node, $expectedClass, $methodName)) {
					return null;
				}

				$argPositions = $this->methodLookup[$methodName]['positions'];
				foreach ($argPositions as $position) {
					if (!isset($node->args[$position])) {
						continue;
					}

					$arg = $node->args[$position];
					if (!$arg instanceof Node\Arg) {
						continue;
					}

					$this->processArgument($arg->value);
				}

				return null;
			}

			/**
			 * Check if the method call is in a valid context for the expected class.
			 *
			 * @param \PhpParser\Node\Expr\MethodCall $node
			 * @param string $expectedClass
			 * @param string $methodName
			 * @return bool
			 */
			protected function isValidContext(Node\Expr\MethodCall $node, string $expectedClass, string $methodName): bool {
				$var = $node->var;

				// Check if it's a variable we know the type of
				if ($var instanceof Node\Expr\Variable && is_string($var->name)) {
					$varType = $this->variableTypes[$var->name] ?? null;
					if ($varType !== null) {
						// Check if the variable type matches the expected class
						$shortExpected = substr($expectedClass, (int)strrpos($expectedClass, '\\') + 1);
						$shortVar = substr($varType, (int)strrpos($varType, '\\') + 1);

						return $shortVar === $shortExpected || str_ends_with($varType, $shortExpected);
					}
				}

				// For chained method calls (like $this->find()->where()), allow query methods
				if ($var instanceof Node\Expr\MethodCall) {
					// If the expected class is SelectQuery and we're in a chain, allow it
					if (str_contains($expectedClass, 'Query')) {
						return true;
					}
				}

				// For $this-> calls in Table classes, allow query methods
				if ($var instanceof Node\Expr\Variable && $var->name === 'this') {
					if (str_contains($expectedClass, 'Query')) {
						return true;
					}
				}

				// If we can't determine context and the method is in our lookup, allow it
				// This is more permissive but avoids false negatives
				return true;
			}

			/**
			 * @param \PhpParser\Node\Expr $expr
			 * @return void
			 */
			protected function processArgument(Node\Expr $expr): void {
				// Handle single string: ->select('field')
				if ($expr instanceof Node\Scalar\String_) {
					$this->checkAndAddReplacement($expr);

					return;
				}

				// Handle array: ->select(['field1', 'field2']) or ->where(['field' => 'value'])
				if ($expr instanceof Node\Expr\Array_) {
					foreach ($expr->items as $item) {
						if (!$item instanceof Node\Expr\ArrayItem) {
							continue;
						}

						// For array keys (associative arrays)
						if ($item->key instanceof Node\Scalar\String_) {
							$this->checkAndAddReplacement($item->key, 'key');
						}

						// For array values that are field names (indexed arrays)
						if ($item->key === null && $item->value instanceof Node\Scalar\String_) {
							$this->checkAndAddReplacement($item->value, 'value');
						}
					}
				}
			}

			/**
			 * @param \PhpParser\Node\Scalar\String_ $stringNode
			 * @param string $position
			 * @return void
			 */
			protected function checkAndAddReplacement(Node\Scalar\String_ $stringNode, string $position = 'single'): void {
				$fieldName = $stringNode->value;

				// Skip if it contains a dot (table.field notation)
				if (str_contains($fieldName, '.')) {
					return;
				}

				// Skip if not a known field constant
				if (!isset($this->fieldConstants[$fieldName])) {
					return;
				}

				$this->replacements[] = [
					'line' => $stringNode->getStartLine(),
					'startPos' => $stringNode->getStartFilePos(),
					'endPos' => $stringNode->getEndFilePos(),
					'field' => $fieldName,
					'constant' => $this->fieldConstants[$fieldName],
					'position' => $position,
				];
			}
		};

		$traverser = new NodeTraverser();
		$traverser->addVisitor($visitor);
		$traverser->traverse($ast);

		return $visitor->replacements;
	}

	/**
	 * Apply replacements to the content.
	 *
	 * @param string $content
	 * @param array<array{line: int, startPos: int, endPos: int, field: string, constant: string, position: string}> $replacements
	 * @param string $entityClass
	 * @param string $entityShortName
	 * @return string
	 */
	protected function applyReplacements(string $content, array $replacements, string $entityClass, string $entityShortName): string {
		// Sort by position descending to avoid offset issues
		usort($replacements, fn ($a, $b) => $b['startPos'] <=> $a['startPos']);

		foreach ($replacements as $replacement) {
			$constantRef = $entityShortName . '::' . $replacement['constant'];
			$start = $replacement['startPos'];
			$end = $replacement['endPos'];

			$content = substr($content, 0, $start) . $constantRef . substr($content, $end + 1);
		}

		// Add use statement if not present
		$content = $this->ensureUseStatement($content, $entityClass, $entityShortName);

		return $content;
	}

	/**
	 * Ensure the entity class is imported with a use statement.
	 *
	 * @param string $content
	 * @param string $entityClass
	 * @param string $entityShortName
	 * @return string
	 */
	protected function ensureUseStatement(string $content, string $entityClass, string $entityShortName): string {
		// Check if use statement already exists
		$usePattern = '/use\s+' . preg_quote($entityClass, '/') . '\s*;/';
		if (preg_match($usePattern, $content)) {
			return $content;
		}

		// Check if short name is already used (might be imported differently)
		$shortNamePattern = '/use\s+[^;]+\\\\' . preg_quote($entityShortName, '/') . '\s*;/';
		if (preg_match($shortNamePattern, $content)) {
			return $content;
		}

		// Find the last use statement and add after it
		if (preg_match_all('/^use\s+[^;]+;$/m', $content, $matches, PREG_OFFSET_CAPTURE) && $matches[0]) {
			$lastUse = end($matches[0]);
			if ($lastUse !== false) {
				$insertPosition = (int)$lastUse[1] + strlen((string)$lastUse[0]);
				$useStatement = "\nuse " . $entityClass . ';';
				$content = substr($content, 0, $insertPosition) . $useStatement . substr($content, $insertPosition);
			}
		} else {
			// No use statements, add after namespace
			if (preg_match('/^namespace\s+[^;]+;$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
				$insertPosition = $matches[0][1] + strlen($matches[0][0]);
				$useStatement = "\n\nuse " . $entityClass . ';';
				$content = substr($content, 0, $insertPosition) . $useStatement . substr($content, $insertPosition);
			}
		}

		return $content;
	}

}
