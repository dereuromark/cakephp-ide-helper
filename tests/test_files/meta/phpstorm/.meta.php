<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'Cookie' => \Cake\Controller\Component\CookieComponent::class,
			'Csrf' => \Cake\Controller\Component\CsrfComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'My' => \TestApp\Controller\Component\MyComponent::class,
			'MyOther' => \TestApp\Controller\Component\MyOtherComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \TestApp\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
			'Shim.Session' => \Shim\Controller\Component\SessionComponent::class,
		])
	);

	override(
		\Cake\Core\PluginApplicationInterface::addPlugin(0),
		map([
			'Bake' => \Cake\Http\BaseApplication::class,
			'Shim' => \Cake\Http\BaseApplication::class,
			'Tools' => \Cake\Http\BaseApplication::class,
			'WyriHaximus/TwigView' => \Cake\Http\BaseApplication::class,
		])
	);

	override(
		\Cake\Database\Type::build(0),
		map([
			'biginteger' => \Cake\Database\Type\IntegerType::class,
			'binary' => \Cake\Database\Type\BinaryType::class,
			'binaryuuid' => \Cake\Database\Type\BinaryUuidType::class,
			'boolean' => \Cake\Database\Type\BoolType::class,
			'date' => \Cake\Database\Type\DateType::class,
			'datetime' => \Cake\Database\Type\DateTimeType::class,
			'decimal' => \Cake\Database\Type\DecimalType::class,
			'float' => \Cake\Database\Type\FloatType::class,
			'integer' => \Cake\Database\Type\IntegerType::class,
			'json' => \Cake\Database\Type\JsonType::class,
			'smallinteger' => \Cake\Database\Type\IntegerType::class,
			'string' => \Cake\Database\Type\StringType::class,
			'text' => \Cake\Database\Type\StringType::class,
			'time' => \Cake\Database\Type\TimeType::class,
			'timestamp' => \Cake\Database\Type\DateTimeType::class,
			'tinyinteger' => \Cake\Database\Type\IntegerType::class,
			'uuid' => \Cake\Database\Type\UuidType::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'Abstract' => \TestApp\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \TestApp\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \TestApp\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \TestApp\Model\Table\CallbacksTable::class,
			'Cars' => \TestApp\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \TestApp\Model\Table\CustomFinderTable::class,
			'Exceptions' => \TestApp\Model\Table\ExceptionsTable::class,
			'Foo' => \TestApp\Model\Table\FooTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	override(
		\Cake\Datasource\QueryInterface::find(0),
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

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
			'Abstract' => \TestApp\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \TestApp\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \TestApp\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \TestApp\Model\Table\CallbacksTable::class,
			'Cars' => \TestApp\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \TestApp\Model\Table\CustomFinderTable::class,
			'Exceptions' => \TestApp\Model\Table\ExceptionsTable::class,
			'Foo' => \TestApp\Model\Table\FooTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	override(
		\Cake\ORM\Table::addBehavior(0),
		map([
			'CounterCache' => \Cake\ORM\Table::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Table::class,
			'Shim.Nullable' => \Cake\ORM\Table::class,
			'Timestamp' => \Cake\ORM\Table::class,
			'Translate' => \Cake\ORM\Table::class,
			'Tree' => \Cake\ORM\Table::class,
		])
	);

	override(
		\Cake\ORM\Table::belongToMany(0),
		map([
			'Abstract' => \Cake\ORM\Association\BelongsToMany::class,
			'Awesome.Houses' => \Cake\ORM\Association\BelongsToMany::class,
			'Awesome.Windows' => \Cake\ORM\Association\BelongsToMany::class,
			'BarBars' => \Cake\ORM\Association\BelongsToMany::class,
			'BarBarsAbstract' => \Cake\ORM\Association\BelongsToMany::class,
			'Callbacks' => \Cake\ORM\Association\BelongsToMany::class,
			'Cars' => \Cake\ORM\Association\BelongsToMany::class,
			'Controllers.Houses' => \Cake\ORM\Association\BelongsToMany::class,
			'CustomFinder' => \Cake\ORM\Association\BelongsToMany::class,
			'Exceptions' => \Cake\ORM\Association\BelongsToMany::class,
			'Foo' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipMe' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipSome' => \Cake\ORM\Association\BelongsToMany::class,
			'Wheels' => \Cake\ORM\Association\BelongsToMany::class,
			'WheelsExtra' => \Cake\ORM\Association\BelongsToMany::class,
		])
	);

	override(
		\Cake\ORM\Table::belongsTo(0),
		map([
			'Abstract' => \Cake\ORM\Association\BelongsTo::class,
			'Awesome.Houses' => \Cake\ORM\Association\BelongsTo::class,
			'Awesome.Windows' => \Cake\ORM\Association\BelongsTo::class,
			'BarBars' => \Cake\ORM\Association\BelongsTo::class,
			'BarBarsAbstract' => \Cake\ORM\Association\BelongsTo::class,
			'Callbacks' => \Cake\ORM\Association\BelongsTo::class,
			'Cars' => \Cake\ORM\Association\BelongsTo::class,
			'Controllers.Houses' => \Cake\ORM\Association\BelongsTo::class,
			'CustomFinder' => \Cake\ORM\Association\BelongsTo::class,
			'Exceptions' => \Cake\ORM\Association\BelongsTo::class,
			'Foo' => \Cake\ORM\Association\BelongsTo::class,
			'SkipMe' => \Cake\ORM\Association\BelongsTo::class,
			'SkipSome' => \Cake\ORM\Association\BelongsTo::class,
			'Wheels' => \Cake\ORM\Association\BelongsTo::class,
			'WheelsExtra' => \Cake\ORM\Association\BelongsTo::class,
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
		\Cake\ORM\Table::hasMany(0),
		map([
			'Abstract' => \Cake\ORM\Association\HasMany::class,
			'Awesome.Houses' => \Cake\ORM\Association\HasMany::class,
			'Awesome.Windows' => \Cake\ORM\Association\HasMany::class,
			'BarBars' => \Cake\ORM\Association\HasMany::class,
			'BarBarsAbstract' => \Cake\ORM\Association\HasMany::class,
			'Callbacks' => \Cake\ORM\Association\HasMany::class,
			'Cars' => \Cake\ORM\Association\HasMany::class,
			'Controllers.Houses' => \Cake\ORM\Association\HasMany::class,
			'CustomFinder' => \Cake\ORM\Association\HasMany::class,
			'Exceptions' => \Cake\ORM\Association\HasMany::class,
			'Foo' => \Cake\ORM\Association\HasMany::class,
			'SkipMe' => \Cake\ORM\Association\HasMany::class,
			'SkipSome' => \Cake\ORM\Association\HasMany::class,
			'Wheels' => \Cake\ORM\Association\HasMany::class,
			'WheelsExtra' => \Cake\ORM\Association\HasMany::class,
		])
	);

	override(
		\Cake\ORM\Table::hasOne(0),
		map([
			'Abstract' => \Cake\ORM\Association\HasOne::class,
			'Awesome.Houses' => \Cake\ORM\Association\HasOne::class,
			'Awesome.Windows' => \Cake\ORM\Association\HasOne::class,
			'BarBars' => \Cake\ORM\Association\HasOne::class,
			'BarBarsAbstract' => \Cake\ORM\Association\HasOne::class,
			'Callbacks' => \Cake\ORM\Association\HasOne::class,
			'Cars' => \Cake\ORM\Association\HasOne::class,
			'Controllers.Houses' => \Cake\ORM\Association\HasOne::class,
			'CustomFinder' => \Cake\ORM\Association\HasOne::class,
			'Exceptions' => \Cake\ORM\Association\HasOne::class,
			'Foo' => \Cake\ORM\Association\HasOne::class,
			'SkipMe' => \Cake\ORM\Association\HasOne::class,
			'SkipSome' => \Cake\ORM\Association\HasOne::class,
			'Wheels' => \Cake\ORM\Association\HasOne::class,
			'WheelsExtra' => \Cake\ORM\Association\HasOne::class,
		])
	);

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'Abstract' => \TestApp\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \TestApp\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \TestApp\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \TestApp\Model\Table\CallbacksTable::class,
			'Cars' => \TestApp\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \TestApp\Model\Table\CustomFinderTable::class,
			'Exceptions' => \TestApp\Model\Table\ExceptionsTable::class,
			'Foo' => \TestApp\Model\Table\FooTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	expectedArguments(
		\Cake\Validation\Validator::requirePresence(),
		1,
		'create',
		'update'
	);

	override(
		\Cake\View\View::element(0),
		map([
			'deeply/nested' => \Cake\View\View::class,
			'example' => \Cake\View\View::class,
		])
	);

	override(
		\Cake\View\View::loadHelper(0),
		map([
			'Breadcrumbs' => \Cake\View\Helper\BreadcrumbsHelper::class,
			'Flash' => \Cake\View\Helper\FlashHelper::class,
			'Form' => \Cake\View\Helper\FormHelper::class,
			'Html' => \TestApp\View\Helper\HtmlHelper::class,
			'IdeHelper.DocBlock' => \IdeHelper\View\Helper\DocBlockHelper::class,
			'My' => \TestApp\View\Helper\MyHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Rss' => \Cake\View\Helper\RssHelper::class,
			'Session' => \Cake\View\Helper\SessionHelper::class,
			'Shim.Configure' => \Shim\View\Helper\ConfigureHelper::class,
			'Shim.Cookie' => \Shim\View\Helper\CookieHelper::class,
			'Shim.Session' => \Shim\View\Helper\SessionHelper::class,
			'Shim.Url' => \Shim\View\Helper\UrlHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		])
	);

}
