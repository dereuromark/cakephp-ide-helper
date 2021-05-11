<?php
/**
 * @var \TestApp\View\AppView $this
 * @var object $allCars
 * @var object $date
 * @var object $obj
 * @var \TestApp\Model\Entity\Foo[]|\Cake\Collection\CollectionInterface $foos
 * @var \TestApp\Model\Entity\Wheel $wheel
 */
	if ($obj) {
		echo $obj->foo();
	}
	echo $this->Generator->generate(['obj' => $obj]);
?>
<div>
	<?php foreach ($allCars as $car) {
		$finalCarTime = $this->Helper->out($car->created);
		echo $finalCarTime;
	} ?>
	<?php echo h($wheel->id); ?>
	<p>
		<?= $date->format(); ?>
	</p>
	<?php foreach ($allCars->engines as $i => $engine) {
		echo h($engine);
	} ?>
	<?php foreach ($foos as $foo) {
		echo h($foo->prop);
	} ?>
</div>
