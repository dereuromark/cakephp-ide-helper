<?php

namespace IdeHelper\CodeCompletion\Task;

class SelectQueryTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\ORM\Query';

	/**
	 * @return string
	 */
	public function type(): string {
		return static::TYPE_NAMESPACE;
	}

	/**
	 * @return string
	 */
	public function create(): string {
		return <<<'CODE'
use Cake\Database\ExpressionInterface;
use Cake\Datasource\ResultSetInterface;
use Closure;

if (false) {
	/**
	 * @template TSubject
	 */
	class SelectQuery {
		/**
		 * @return static
		 */
		public function find(string $finder, mixed ...$args) {}

		/**
		 * @return static
		 */
		public function where(
			ExpressionInterface|Closure|array|string|null $conditions = null,
			array $types = [],
			bool $overwrite = false,
		) {}

		/**
		 * @return static
		 */
		public function andWhere($conditions, array $types = []) {}

		/**
		 * @return static
		 */
		public function contain(mixed $associations, Closure|bool $override = false) {}

		/**
		 * @return static
		 */
		public function groupBy(ExpressionInterface|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return static
		 */
		public function orderBy(ExpressionInterface|Closure|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return ResultSetInterface<array-key, TSubject>
		 */
		public function all() {}

		/**
		 * @return TSubject|null
		 */
		public function first() {}

		/**
		 * @return TSubject
		 */
		public function firstOrFail() {}

		/**
		 * @return array<TSubject>
		 */
		public function toArray() {}
	}
}

CODE;
	}

}
