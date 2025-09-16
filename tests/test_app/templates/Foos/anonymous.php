<?php
/**
 * @var \Cake\View\View $this
 * @var array<\App\Model\Entity\ParticipantMood> $participantMoods
 */
?>
<div>
	<?php
	// This should NOT trigger an annotation for $m
	$yourMoodIds = array_map(function($m) { return $m->mood_id; }, $participantMoods ?? []);

	// This should NOT trigger an annotation for $item
	$filtered = array_filter($items, function($item) {
		return $item->active;
	});

	// This should NOT trigger annotations for $a and $b
	usort($data, function($a, $b) {
		return $a->sort_order <=> $b->sort_order;
	});

	// Arrow function - should NOT trigger annotation for $x
	$doubled = array_map(fn($x) => $x * 2, $numbers);

	// Variables with 'use' clause - $multiplier should still get annotation
	$multiplier = 5;
	$result = array_map(function($n) use ($multiplier) {
		return $n * $multiplier;
	}, $values);
	?>

	<ul>
		<?php foreach ($yourMoodIds as $id): ?>
			<li><?= h($id) ?></li>
		<?php endforeach; ?>
	</ul>
</div>