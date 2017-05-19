<?php
/**
 * @var \Cake\View\View $this
 * @var \Cake\ORM\Entity $car !
 * @var \Cake\ORM\Entity[]|\Cake\Collection\CollectionInterface $wheels
 */
?>
<div>
	<?php echo $this->Form->sth(); ?>
	<?php echo $car->id; ?>
	<?php foreach ($wheels as $wheel); ?>
</div>
