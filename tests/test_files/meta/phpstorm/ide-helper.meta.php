<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\ModelAwareTrait::loadModel(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\Cake\ORM\Table::addBehavior(0),
		map([
			'CounterCache' => \Cake\ORM\Table::class,
			'Timestamp' => \Cake\ORM\Table::class,
			'Translate' => \Cake\ORM\Table::class,
			'Tree' => \Cake\ORM\Table::class,
			'Shim.Nullable' => \Cake\ORM\Table::class,
		])
	);

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'Cookie' => \Cake\Controller\Component\CookieComponent::class,
			'Csrf' => \Cake\Controller\Component\CsrfComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \App\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
			'My' => \App\Controller\Component\MyComponent::class,
			'MyOther' => \App\Controller\Component\MyOtherComponent::class,
			'Shim.Session' => \Shim\Controller\Component\SessionComponent::class,
		])
	);

	override(
		\Cake\View\View::loadHelper(0),
		map([
			'Breadcrumbs' => \Cake\View\Helper\BreadcrumbsHelper::class,
			'Flash' => \Cake\View\Helper\FlashHelper::class,
			'Form' => \Cake\View\Helper\FormHelper::class,
			'Html' => \App\View\Helper\HtmlHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Rss' => \Cake\View\Helper\RssHelper::class,
			'Session' => \Cake\View\Helper\SessionHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
			'My' => \App\View\Helper\MyHelper::class,
			'Shim.Configure' => \Shim\View\Helper\ConfigureHelper::class,
			'Shim.Cookie' => \Shim\View\Helper\CookieHelper::class,
			'Shim.Session' => \Shim\View\Helper\SessionHelper::class,
		])
	);

	override(
		\Cake\ORM\Table::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Association::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

}
