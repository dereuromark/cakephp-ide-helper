<?php
	if ($obj) {
		echo $obj->foo();
	}
?>
<div>
	<?php foreach ($cars as $car) {
		echo $this->Helper->out($car);
	} ?>
	<?php echo h($wheel->id); ?>
	<p>
		<?= $date->format(); ?>
	</p>
	<?php foreach ($cars->engines as $i => $engine) {
		echo h($engine);
	} ?>
</div>
