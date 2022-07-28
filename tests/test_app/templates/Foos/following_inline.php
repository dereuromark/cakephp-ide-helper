<?php
/**
 * @var \App\View\AppView $this
 */

/** @var Authorization\Identity $identity */
$identity = $this->getRequest()
	->getAttribute('identity');
?>
<div>
	<?php echo h($wheel->id); ?>
</div>
