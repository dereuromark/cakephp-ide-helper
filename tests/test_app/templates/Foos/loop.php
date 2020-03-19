<?php
use Something;
?>
<div>
	<?php foreach ($cars as $car) {} ?>
	<?php echo h($wheel->id); ?>

	<?php foreach ($wheel->foos as $foos): ?>
		<tr>
			<td><?= h($foos->x) ?></td>
		</tr>
	<?php endforeach; ?>
</div>
