<?php
/**
 * @license MIT
 * @var \TestApp\View\AppView $this
 * @var \TestApp\Model\Entity\Wheel $wheel
 */

/** @var \Authorization\Identity $identity */
$identity = $this->getRequest()
	->getAttribute('identity');
?>
<div>
	<?php echo h($wheel->id); ?>
</div>
