<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Car[]|\Cake\Collection\CollectionInterface $cars
 * @var \App\Model\Entity\Wheel $wheel
 */
use Something;
?>
<div>
	<?php foreach ($cars as $car); ?>
	<?php echo h($wheel->id); ?>

	<?php foreach ($wheel->foos as $foos): ?>
		<tr>
			<td><?= h($foos->x) ?></td>
		</tr>
	<?php endforeach; ?>
</div>
