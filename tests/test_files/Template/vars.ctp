<?php
/**
 * @var \App\View\AppView $this
 * @var object $date
 * @var object $obj
 * @var \App\Model\Entity\Car[]|\Cake\Collection\CollectionInterface $cars
 * @var \App\Model\Entity\Wheel $wheel
 */
	if ($obj) {
		echo $obj->foo();
	}
	echo $this->Generator->generate(['obj' => $obj]);
?>
<div>
	<?php foreach ($cars as $car) {
		$finalCar = $this->Helper->out($car);
		echo $finalCar;
	} ?>
	<?php echo h($wheel->id); ?>
	<p>
		<?= $date->format(); ?>
	</p>
	<?php foreach ($cars->engines as $i => $engine) {
		echo h($engine);
	} ?>
</div>
