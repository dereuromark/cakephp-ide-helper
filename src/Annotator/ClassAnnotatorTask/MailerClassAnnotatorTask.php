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
		if (!str_contains($path, DS . 'src' . DS)) {
			return false;
		}

		preg_match('#\buse (\w+)\\\\Mailer\\\\(\w+)Mailer\b#', $content, $useMatches);
		preg_match('#\$\w+\s*=\s*\$this-\>getMailer\(\'([\w\.]+)\'\)#', $content, $callMatches);
		$singleLine = false;
		if (!$callMatches) {
			$singleLine = true;
			preg_match('#\$this->getMailer\(\s*\'([\w\.]+)\'\s*\)\s*->\s*send\(\s*\'([\w\.]+)\'#msu', $content, $callMatches);
		}
		if (!$useMatches && !$callMatches) {
			return false;
		}

		if ($useMatches) {
			$varName = lcfirst($useMatches[2]) . 'Mailer';
		} else {
			$class = $callMatches[1];
			[$plugin, $name] = pluginSplit($class);
			$varName = lcfirst($name) . 'Mailer';
		}

		if ($singleLine && !empty($callMatches)) {
			return true;
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

		$singleCall = false;
		if (!$useMatches) {
			preg_match('#\$\w+\s*=\s*\$this->getMailer\(\'([\w.]+)\'\)#', $this->content, $callMatches);
			if (!$callMatches) {
				preg_match('#\$this->getMailer\(\s*\'([\w\.]+)\'\s*\)\s*->\s*send\(\s*\'([\w\.]+)\'#msu', $this->content, $callMatches);
				if (!$callMatches) {
					return false;
				}

				$singleCall = true;
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

		$action = null;
		if (!$singleCall) {
			$varName = lcfirst($name);
			$rows = explode(PHP_EOL, $this->content);
			$rowToAnnotate = null;
			$rowMatches = null;
			foreach ($rows as $i => $row) {
				if (!preg_match('#\$' . $varName . '->send\(\'(\w+)\'#', $row, $rowMatches)) {
					continue;
				}
				$rowToAnnotate = $i + 1;
				$action = $rowMatches[1];

				break;
			}
		} else {
			assert(!empty($callMatches));
			$rows = explode(PHP_EOL, $this->content);
			$rowToAnnotate = null;
			$rowMatches = null;
			$multiLine = str_contains($callMatches[0], PHP_EOL);
			foreach ($rows as $i => $row) {
				if (
					$multiLine
					&& preg_match('#\$this->getMailer\(\s*\'' . $callMatches[1] . '\'\s*\)#msu', $row, $rowMatches)
					&& !empty($rows[$i + 1])
					&& preg_match('#->\s*send\(\s*\'' . $callMatches[2] . '\'#msu', $rows[$i + 1], $rowMatches)
				) {
					$rowToAnnotate = $i;
					$action = $callMatches[2];

					break;
				}

				if (!preg_match('#\$this->getMailer\(\s*\'' . $callMatches[1] . '\'\s*\)\s*->\s*send\(\s*\'' . $callMatches[2] . '\'#msu', $row, $rowMatches)) {
					continue;
				}
				$rowToAnnotate = $i + 1;
				$action = $callMatches[2];

				break;
			}
		}

		if (!$rowToAnnotate) {
			return false;
		}

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
