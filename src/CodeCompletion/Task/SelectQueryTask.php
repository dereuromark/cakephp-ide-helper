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
use Psr\SimpleCache\CacheInterface;

if (false) {
	/**
	 * @template TSubject
	 */
	class SelectQuery {
		/**
		 * @return static<TSubject>
		 */
		public function find(string $finder, mixed ...$args) {}

		/**
		 * @return static<TSubject>
		 */
		public function where(
			ExpressionInterface|Closure|array|string|null $conditions = null,
			array $types = [],
			bool $overwrite = false,
		) {}

		/**
		 * @return static<TSubject>
		 */
		public function andWhere($conditions, array $types = []) {}

		/**
		 * @return static<TSubject>
		 */
		public function matching(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function leftJoinWith(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function innerJoinWith(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function notMatching(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function contain(mixed $associations, Closure|bool $override = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function clearContain() {}

		/**
		 * @return static<TSubject>
		 */
		public function cache(Closure|string|false $key, CacheInterface|string $config = 'default') {}

		/**
		 * @return static<TSubject>
		 */
		public function groupBy(ExpressionInterface|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function orderBy(ExpressionInterface|Closure|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function enableAutoFields(bool $value = true) {}

		/**
		 * @return static<TSubject>
		 */
		public function disableAutoFields() {}

		/**
		 * @return static<array<string,mixed>>
		 */
		public function disableHydration() {}

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
