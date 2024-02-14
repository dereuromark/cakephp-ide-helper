<?php
/**
 * @var \TestApp\View\AppView $this
 * @var array $x
 * @var array<int> $ints
 * @var array{a: int, b: string}|null $shaped
 */
	foreach ($x as $y) {
		echo $y;
	}
	foreach ($foo as $int) {
		echo $int;
	}
?>
<div>
	<?php foreach ($ints as $int) {
		echo $int;
	} ?>
	<?php foreach ($shaped as $x) {
		echo h($x);
	} ?>
</div>
