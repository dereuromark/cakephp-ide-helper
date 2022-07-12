<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\ORM\Association;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;
use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotation\PropertyReadAnnotation;
use IdeHelper\Utility\App;
use IdeHelper\View\Helper\DocBlockHelper;
use PHP_CodeSniffer\Files\File;
use RuntimeException;
use Throwable;

class EntityAnnotator extends AbstractAnnotator {

	/**
	 * @var array<string, string>|null
	 */
	protected static $typeMap;

	/**
	 * @var array<string, string>
	 */
	protected static $typeMapDefaults = [
		'mediumtext' => 'string',
		'longtext' => 'string',
		'array' => 'array',
		'json' => 'array',
		'binaryuuid' => 'string',
	];

	/**
	 * @param string $path Path to file.
	 * @throws \RuntimeException
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if ($name === 'Entity') {
			return false;
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}
		$helper = new DocBlockHelper(new View());
		$propertyHintMap = $this->propertyHintMap($content, $helper);

		$virtualFields = $this->virtualFields($name);
		// For BC reasons we cannot pass it as 3rd param, so we transport it on the helper
		$helper->virtualFields = $virtualFields;
		$annotations = $this->buildAnnotations($propertyHintMap, $helper);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 * @param \IdeHelper\View\Helper\DocBlockHelper $helper
	 * @return array<string>
	 */
	protected function propertyHintMap(string $content, DocBlockHelper $helper): array {
		/** @var \Cake\Database\Schema\TableSchemaInterface $tableSchema */
		$tableSchema = $this->getConfig('schema');
		$columns = $tableSchema->columns();

		$schema = [];
		foreach ($columns as $column) {
			$row = $tableSchema->getColumn($column);
			$row['kind'] = 'column';
			$schema[$column] = $row;
		}

		$schema = $this->hydrateSchemaFromAssoc($schema);

		$propertyHintMap = $helper->buildEntityPropertyHintTypeMap($schema);
		$propertyHintMap = $this->buildExtendedEntityPropertyHintTypeMap($schema, $helper) + $propertyHintMap;
		$propertyHintMap += $this->buildVirtualPropertyHintTypeMap($content);
		$propertyHintMap += $helper->buildEntityAssociationHintTypeMap($schema);

		return array_filter($propertyHintMap);
	}

	/**
	 * From Bake Plugin
	 *
	 * @param array<string, mixed> $schema
	 *
	 * @return array<string, array<string, mixed>>
	 */
	protected function hydrateSchemaFromAssoc(array $schema): array {
		/** @var \Cake\ORM\AssociationCollection<\Cake\ORM\Association> $associations */
		$associations = $this->getConfig('associations');

		foreach ($associations as $association) {
			try {
				$entityClass = '\\' . ltrim($association->getTarget()->getEntityClass(), '\\');

				if ($entityClass === '\\' . Entity::class) {
					$namespace = Configure::read('App.namespace');

					[$plugin] = pluginSplit($association->getTarget()->getRegistryAlias());
					if ($plugin !== null) {
						$namespace = $plugin;
					}
					$namespace = str_replace('/', '\\', trim($namespace, '\\'));

					$entityClass = $this->entityName($association->getTarget()->getAlias());
					$entityClass = '\\' . $namespace . '\Model\Entity\\' . $entityClass;

					if (!class_exists($entityClass)) {
						$entityClass = '\\' . Entity::class;
					}
				}

				$schema[$association->getProperty()] = [
					'kind' => 'association',
					'association' => $association,
					'type' => $entityClass,
					'null' => $this->nullable($association, $schema),
				];
			} catch (Throwable $exception) {
				continue;
			}
		}

		return $schema;
	}

	/**
	 * @param \Cake\ORM\Association $association
	 * @param array<string, mixed> $schema
	 * @return bool
	 */
	protected function nullable(Association $association, array $schema): bool {
		if ($association->type() === Association::ONE_TO_ONE) {
			return true;
		}

		if ($association->type() === Association::MANY_TO_ONE) {
			/** @var array<string>|string $field */
			$field = $association->getForeignKey();
			if (is_array($field)) {
				return false;
			}
			if (!isset($schema[$field]['null'])) {
				return false;
			}

			return $schema[$field]['null'];
		}

		return false;
	}

	/**
	 * Creates the proper entity name (singular) for the specified name
	 *
	 * @param string $name Name
	 * @return string Camelized and plural model name
	 */
	protected function entityName(string $name): string {
		return Inflector::singularize(Inflector::camelize($name));
	}

	/**
	 * @param array<string, array<string, mixed>> $propertySchema
	 * @param \IdeHelper\View\Helper\DocBlockHelper $helper
	 *
	 * @return array<string, string>
	 */
	protected function buildExtendedEntityPropertyHintTypeMap(array $propertySchema, DocBlockHelper $helper): array {
		$propertyHintMap = [];

		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'column') {
				$type = $this->columnTypeToHintType($info['type']);
				if ($type === null) {
					continue;
				}

				$propertyHintMap[$property] = $helper->columnTypeNullable($info, $type);
			}
		}

		return $propertyHintMap;
	}

	/**
	 * Converts a column type to its DocBlock type counterpart.
	 *
	 * @see \Cake\Database\Type
	 *
	 * @param string $type The column type.
	 * @return string|null The DocBlock type, or `null` for unsupported column types.
	 */
	protected function columnTypeToHintType(string $type): ?string {
		if (static::$typeMap === null) {
			static::$typeMap = (array)Configure::read('IdeHelper.typeMap') + static::$typeMapDefaults;
		}

		if (isset(static::$typeMap[$type])) {
			return static::$typeMap[$type];
		}

		return null;
	}

	/**
	 * @param string $content
	 * @return array<string, string>
	 */
	protected function buildVirtualPropertyHintTypeMap(string $content): array {
		if (!preg_match('#\bfunction _get[A-Z][a-zA-Z0-9]+\(\)#', $content)) {
			return [];
		}

		$file = $this->getFile('', $content);

		$classIndex = $file->findNext(T_CLASS, 0);
		if ($classIndex === false) {
			return [];
		}

		$tokens = $file->getTokens();
		if (empty($tokens[$classIndex]['scope_closer'])) {
			return [];
		}

		$classEndIndex = $tokens[$classIndex]['scope_closer'];

		$properties = [];
		$startIndex = $classIndex;
		while ($startIndex < $classEndIndex) {
			$functionIndex = $file->findNext(T_FUNCTION, $startIndex + 1);
			if ($functionIndex === false) {
				break;
			}

			$methodNameIndex = $file->findNext(T_STRING, $functionIndex + 1);
			if ($methodNameIndex === false) {
				break;
			}

			$token = $tokens[$methodNameIndex];
			$methodName = $token['content'];

			$startIndex = $methodNameIndex + 1;

			if (!preg_match('#^_get([A-Z][a-zA-Z0-9]+)$#', $methodName, $matches)) {
				continue;
			}

			$property = Inflector::underscore($matches[1]);

			$properties[$property] = $this->returnType($file, $tokens, $functionIndex);
		}

		return $properties;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<array<string, mixed>> $tokens
	 * @param int $functionIndex
	 * @return string
	 */
	protected function returnType(File $file, array $tokens, int $functionIndex): string {
		$firstTokenInLineIndex = $functionIndex;

		$line = $tokens[$functionIndex]['line'];

		while ($tokens[$firstTokenInLineIndex - 1]['line'] === $line) {
			$firstTokenInLineIndex--;
		}

		$docBlockCloseTagIndex = $this->findDocBlockCloseTagIndex($file, $firstTokenInLineIndex);
		if (!$docBlockCloseTagIndex || empty($tokens[$docBlockCloseTagIndex]['comment_opener'])) {
			return $this->typeHint($file, $tokens, $functionIndex);
		}

		$docBlockOpenTagIndex = $tokens[$docBlockCloseTagIndex]['comment_opener'];

		return $this->extractReturnType($tokens, $docBlockOpenTagIndex, $docBlockCloseTagIndex);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<array<string, mixed>> $tokens
	 * @param int $functionIndex
	 *
	 * @return string
	 */
	protected function typeHint(File $file, array $tokens, int $functionIndex): string {
		$parenthesisCloseTagIndex = $tokens[$functionIndex]['parenthesis_closer'];
		$scopeOpenTagIndex = $tokens[$functionIndex]['scope_opener'];

		$typehintIndex = $file->findNext(T_STRING, $parenthesisCloseTagIndex + 1, $scopeOpenTagIndex);
		if ($typehintIndex === false) {
			return 'mixed';
		}

		$returnType = $tokens[$typehintIndex]['content'];

		$nullableIndex = $file->findNext(T_NULLABLE, $parenthesisCloseTagIndex + 1, $typehintIndex);

		if ($nullableIndex) {
			$returnType .= '|null';
		}

		return $returnType;
	}

	/**
	 * @param array<array<string, mixed>> $tokens
	 * @param int $docBlockOpenTagIndex
	 * @param int $docBlockCloseTagIndex
	 *
	 * @return string
	 */
	protected function extractReturnType(array $tokens, int $docBlockOpenTagIndex, int $docBlockCloseTagIndex): string {
		for ($i = $docBlockOpenTagIndex + 1; $i < $docBlockCloseTagIndex; $i++) {

			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if ($tokens[$i]['content'] !== '@return') {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			$spaceIndex = strpos($content, ' ');
			if ($spaceIndex) {
				$content = substr($content, 0, $spaceIndex);
			}

			return $content;
		}

		return 'mixed';
	}

	/**
	 * @param array<string> $propertyHintMap
	 * @param \IdeHelper\View\Helper\DocBlockHelper $helper
	 *
	 * @throws \RuntimeException
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(array $propertyHintMap, DocBlockHelper $helper): array {
		/** @var array<string> $virtualFields */
		$virtualFields = $helper->virtualFields;

		$real = $virtual = [];
		foreach ($propertyHintMap as $name => $type) {
			$isVirtual = in_array($name, $virtualFields, true);
			$tag = $isVirtual ? PropertyReadAnnotation::TAG : PropertyAnnotation::TAG;
			$annotation = "$tag {$type}\${$name}";

			$annotationObject = AnnotationFactory::create($tag, $type, $name);
			if (!$annotationObject) {
				throw new RuntimeException('Cannot factorize annotation `' . $annotation . '`');
			}

			if ($isVirtual) {
				$virtual[$name] = $annotationObject;
			} else {
				$real[$name] = $annotationObject;
			}
		}

		return $real + $virtual;
	}

	/**
	 * Detect actual virtual fields by them being exposed as such.
	 *
	 * @param string $name
	 *
	 * @return array<string>
	 */
	protected function virtualFields(string $name): array {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'Model/Entity');
		if (!$className) {
			return [];
		}

		try {
			/** @var \Cake\Datasource\EntityInterface $entity */
			$entity = new $className();
		} catch (Throwable $exception) {
			return [];
		}

		return $entity->getVirtual();
	}

}
