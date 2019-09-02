<?php
	if ($obj) {
		echo $obj->foo();
	}
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
