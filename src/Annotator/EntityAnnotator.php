<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\View\View;
use PHP_CodeSniffer_Tokens;

/**
 */
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
		if (preg_match('/\* @property .+ \$/', $content)) {
			return null;
		}

		$helper = new DocBlockHelper(new View());
		$schema = $this->config('schema');
		$propertyHintMap = $helper->buildEntityPropertyHintTypeMap($schema);

		$annotations = $helper->propertyHints($propertyHintMap);
		$associationHintMap = $helper->buildEntityAssociationHintTypeMap($schema);

		if ($associationHintMap) {
			$annotations[] = '';
			$annotations = array_merge($annotations, $helper->propertyHints($associationHintMap));
		}

		$annotations = $helper->classDescription($name, 'Entity', $annotations);

		$file = $this->_getFile($path);
		$file->start($content);

		$tokens = $file->getTokens();

		$classIndex = $file->findNext(T_CLASS, 0);

		$prevCode = $file->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $classIndex, null, true);

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classIndex, $prevCode);
		if ($closeTagIndex) {
			return null;
		}

		$fixer = $this->_getFixer();
		$fixer->startFile($file);

		$docBlock = $annotations . PHP_EOL;
		$fixer->replaceToken($classIndex, $docBlock . $tokens[$classIndex]['content']);

		$contents = $fixer->getContents();

		$this->_storeFile($path, $contents);

		$this->_io->out($name);

		return true;
	}

}
