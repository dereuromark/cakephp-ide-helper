<?php
/**
 * @var \TestApp\View\AppView $this
 * @var \TestApp\Model\Entity\Car[]|\Cake\Collection\CollectionInterface $cars
 * @var \TestApp\Model\Entity\Wheel $wheel
 */
?>
<?php echo $this->MyHelper->foo(); ?>
<div>
	<?php foreach ($cars as $car); ?>
	<?php echo h($wheel->id); ?>
</div>
