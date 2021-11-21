<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\UsesAnnotation;

/**
 * Mailer classes should automatically have `@uses` annotated for method invocation.
 */
class MailerClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (strpos($path, DS . 'src' . DS) === false) {
			return false;
		}

		preg_match('#\buse (\w+)\\\\Mailer\\\\(\w+)Mailer\b#', $content, $useMatches);
		preg_match('#\$\w+\s*=\s*\$this-\>getMailer\(\'([\w\.]+)\'\)#', $content, $callMatches);
		if (!$useMatches && !$callMatches) {
			return false;
		}

		if ($useMatches) {
			$varName = lcfirst($useMatches[2]) . 'Mailer';
		} else {
			[$plugin, $name] = pluginSplit($callMatches[1]);
			$varName = lcfirst($name) . 'Mailer';
		}

		if (!preg_match('#\$' . $varName . '->send\(\'\w+\'#', $content)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		preg_match('#\buse (\w+)\\\\Mailer\\\\(\w+)Mailer\b#', $this->content, $useMatches);
		if (!$useMatches) {
			preg_match('#\$\w+\s*=\s*\$this->getMailer\(\'([\w\.]+)\'\)#', $this->content, $callMatches);
			if (!$callMatches) {
				return false;
			}
		}

		if ($useMatches) {
			$appNamespace = $useMatches[1];
			$name = $useMatches[2] . 'Mailer';
		} else {
			[$plugin, $name] = pluginSplit($callMatches[1]);
			$appNamespace = $plugin ?: (Configure::read('App.namespace') ?: 'App');
			$name = $name . 'Mailer';
		}

		$varName = lcfirst($name);
		$rows = explode(PHP_EOL, $this->content);
		$rowToAnnotate = null;
		$rowMatches = null;
		foreach ($rows as $i => $row) {
			if (!preg_match('#\$' . $varName . '->send\(\'(\w+)\'#', $row, $rowMatches)) {
				continue;
			}
			$rowToAnnotate = $i + 1;

			break;
		}

		if (!$rowToAnnotate) {
			return false;
		}

		$action = $rowMatches[1];
		$method = $appNamespace . '\\Mailer\\' . $name . '::' . $action . '()';
		$annotations = $this->buildUsesAnnotations([$method]);

		return $this->annotateInlineContent($path, $this->content, $annotations, $rowToAnnotate);
	}

	/**
	 * @param array<string> $classes
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildUsesAnnotations(array $classes): array {
		$annotations = [];

		foreach ($classes as $className) {
			$annotations[] = AnnotationFactory::createOrFail(UsesAnnotation::TAG, '\\' . $className);
		}

		return $annotations;
	}

}
