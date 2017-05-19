<?php

namespace IdeHelper\Test\TestCase\Annotator;

use SebastianBergmann\Diff\Differ;

trait DiffHelperTrait {

	/**
	 * Outputs some debug info for tests.
	 *
	 * @param string $expected
	 * @param string $actual
	 *
	 * @return void
	 */
	protected function _displayDiff($expected, $actual) {
		$differ = new Differ(null);
		$array = $differ->diffToArray($expected, $actual);

		$begin = null;
		$end = null;
		foreach ($array as $key => $row) {
			if ($row[1] === 0) {
				continue;
			}

			if ($begin === null) {
				$begin = $key;
			}
			$end = $key;
		}
		if ($begin === null) {
			return;
		}
		$firstLineOfOutput = $begin > 0 ? $begin - 1 : 0;
		$lastLineOfOutput = count($array) - 1 > $end ? $end + 1 : $end;

		$out = [];
		for ($i = $firstLineOfOutput; $i <= $lastLineOfOutput; $i++) {
			$row = $array[$i];
			$char = ' ';
			if ($row[1] === 1) {
				$char = '+';
				$out[] = $char . $row[0];
			} elseif ($row[1] === 2) {
				$char = '-';
				$out[] = $char . $row[0];
			} else {
				$out[] = $char . $row[0];
			}
		}

		debug($out);
	}

}
