<?php
/**
 * @var \TestApp\View\AppView $this
 * @var \Cake\ORM\Entity $car !
 * @var \TestApp\Model\Entity\Wheel[] $wheels
 * @var bool $bool
 */
?>
<div>
	<?php echo $this->Form->sth(); ?>
	<?php echo $car->id; ?>
	<?php foreach ($wheels as $wheel); ?>

	<?php echo $bool ? 'yes' : 'no'; ?>
</div>
