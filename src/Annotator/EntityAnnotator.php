<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;

class EntityAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if ($name === 'Entity') {
			return false;
		}

		$content = file_get_contents($path);

		$helper = new DocBlockHelper(new View());
		/* @var \Cake\Database\Schema\TableSchema $tableSchema */
		$tableSchema = $this->getConfig('schema');
		$columns = $tableSchema->columns();
		$schema = [];
		foreach ($columns as $column) {
			$row = $tableSchema->column($column);
			$row['kind'] = 'column';
			$schema[$column] = $row;
		}

		$propertyHintMap = $helper->buildEntityPropertyHintTypeMap($schema);
		$propertyHintMap = array_filter($propertyHintMap);

		$annotations = $helper->propertyHints($propertyHintMap);
		$associationHintMap = $helper->buildEntityAssociationHintTypeMap($schema);

		if ($associationHintMap) {
			$annotations[] = '';
			$annotations = array_merge($annotations, $helper->propertyHints($associationHintMap));
		}

		foreach ($annotations as $key => $annotation) {
			$regexAnnotation = str_replace('\$', '[\$]?', preg_quote($annotation));
			if (preg_match('/' . $regexAnnotation . '/', $content)) {
				unset($annotations[$key]);
				continue;
			}

			// Make replacable via parsed object
			$annotation = AnnotationFactory::createFromString($annotation);
			if (!$annotation) {
				continue;
			}

			$annotations[$key] = $annotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

}
