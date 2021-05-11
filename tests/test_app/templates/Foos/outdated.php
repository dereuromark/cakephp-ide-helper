<?php
/**
 * @var \Cake\View\View $this
 * @var \Cake\ORM\Entity $car !
 * @var \Cake\ORM\Entity[] $wheels
 * @var bool $bool
 * @var \Really\Outdated $outdated
 */
?>
<div>
	<?php echo $this->Form->sth(); ?>
	<?php echo $car->id; ?>
	<?php foreach ($wheels as $wheel); ?>

	<?php echo $bool ? 'yes' : 'no'; ?>
</div>
