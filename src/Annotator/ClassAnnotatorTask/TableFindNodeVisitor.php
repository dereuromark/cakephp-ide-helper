<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Utility\Inflector;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\NodeVisitorAbstract;

/**
 * Node visitor that finds table finder calls ending with first() or firstOrFail().
 */
class TableFindNodeVisitor extends NodeVisitorAbstract {

	private string $appNamespace;

	/**
	 * @var array<array{line: int, varName: string, tableName: string, entityClass: string, nullable: bool}>
	 */
	private array $findings = [];

	/**
	 * @param string $appNamespace
	 */
	public function __construct(string $appNamespace) {
		$this->appNamespace = $appNamespace;
	}

	/**
	 * @return array<array{line: int, varName: string, tableName: string, entityClass: string, nullable: bool}>
	 */
	public function getFindings(): array {
		return $this->findings;
	}

	/**
	 * @param \PhpParser\Node $node
	 * @return int|null
	 */
	public function enterNode(Node $node): ?int {
		if (!$node instanceof Assign || !$node->expr instanceof Node\Expr\MethodCall) {
			return null;
		}

		$methodCall = $node->expr;

		// Check for ->first() or ->firstOrFail()
		if (
			!$methodCall->name instanceof Node\Identifier ||
			!in_array($methodCall->name->toString(), ['first', 'firstOrFail'], true)
		) {
			return null;
		}

		$method = $methodCall->name->toString();
		$nullable = $method === 'first';

		// Get the variable name being assigned
		if (!$node->var instanceof Node\Expr\Variable || !is_string($node->var->name)) {
			return null;
		}
		$varName = $node->var->name;

		// Traverse back through the method chain to find $this->TableName
		$tableName = $this->extractTableName($methodCall->var);
		if ($tableName === null) {
			return null;
		}

		$entityName = Inflector::singularize($tableName);
		$entityClass = '\\' . $this->appNamespace . '\\Model\\Entity\\' . $entityName;

		$this->findings[] = [
			'line' => $node->getStartLine(),
			'varName' => $varName,
			'tableName' => $tableName,
			'entityClass' => $entityClass,
			'nullable' => $nullable,
		];

		return null;
	}

	/**
	 * Extract table name from method chain like $this->TableName->find()->...
	 *
	 * @param \PhpParser\Node\Expr $expr
	 * @return string|null
	 */
	private function extractTableName(Node\Expr $expr): ?string {
		// Walk back through the method chain
		while ($expr instanceof Node\Expr\MethodCall) {
			$expr = $expr->var;
		}

		// Should now be at $this->TableName
		if (
			$expr instanceof Node\Expr\PropertyFetch &&
			$expr->var instanceof Node\Expr\Variable &&
			$expr->var->name === 'this' &&
			$expr->name instanceof Node\Identifier
		) {
			return $expr->name->toString();
		}

		return null;
	}

}
