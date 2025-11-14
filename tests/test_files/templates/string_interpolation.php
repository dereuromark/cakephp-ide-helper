<?php
/**
 * @var \TestApp\View\AppView $this
 * @var mixed $name
 */
$url = "/some/path?param={$this->request->getData('field')}";
?>
<div>
	<a href="<?php echo $url; ?>">Test</a>
	<?php echo h($name); ?>
</div>
