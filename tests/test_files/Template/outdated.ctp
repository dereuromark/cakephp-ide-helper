<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Car $car
 * @var \App\Model\Entity\Wheel[] $wheels
 */
?>
<div>
	<?php echo $this->Form->sth(); ?>
	<?php echo $car->id; ?>
	<?php foreach ($wheels as $wheel); ?>
</div>
