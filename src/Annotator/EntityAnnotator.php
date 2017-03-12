<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\View\View;

class EntityAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return null|string
	 */
	public function annotate($path) {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if ($name === 'Entity') {
			return null;
		}

		$content = file_get_contents($path);

		$helper = new DocBlockHelper(new View());
		$schema = $this->getConfig('schema');
		$propertyHintMap = $helper->buildEntityPropertyHintTypeMap($schema);

		$annotations = $helper->propertyHints($propertyHintMap);
		$associationHintMap = $helper->buildEntityAssociationHintTypeMap($schema);

		if ($associationHintMap) {
			$annotations[] = '';
			$annotations = array_merge($annotations, $helper->propertyHints($associationHintMap));
		}

		foreach ($annotations as $key => $annotation) {
			if (preg_match('/' . preg_quote($annotation) . '/', $content)) {
				unset($annotations[$key]);
			}
		}

		return $this->_annotate($path, $content, $annotations);
	}

}
