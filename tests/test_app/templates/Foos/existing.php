<?php
/**
 * @license MIT
 */

/**
 * @var \TestApp\View\AppView $this
 * @var array<int, int> $things
 */
?>
<div>
	<?php foreach ($cars as $car) {} ?>
	<?php echo h($wheel->id); ?>

	<?php foreach ($things as $keyInt => $valueInt) {
		echo h($keyInt + $valueInt);
	} ?>
</div>
