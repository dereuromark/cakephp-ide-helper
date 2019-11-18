<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'FormProtection' => \Cake\Controller\Component\FormProtectionComponent::class,
			'My' => \App\Controller\Component\MyComponent::class,
			'MyOther' => \App\Controller\Component\MyOtherComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \App\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
			'Tools.Common' => \Tools\Controller\Component\CommonComponent::class,
			'Tools.Mobile' => \Tools\Controller\Component\MobileComponent::class,
			'Tools.Url' => \Tools\Controller\Component\UrlComponent::class,
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
			'char' => \Cake\Database\Type\StringType::class,
			'date' => \Cake\Database\Type\DateType::class,
			'datetime' => \Cake\Database\Type\DateTimeType::class,
			'datetimefractional' => \Cake\Database\Type\DateTimeFractionalType::class,
			'decimal' => \Cake\Database\Type\DecimalType::class,
			'float' => \Cake\Database\Type\FloatType::class,
			'integer' => \Cake\Database\Type\IntegerType::class,
			'json' => \Cake\Database\Type\JsonType::class,
			'smallinteger' => \Cake\Database\Type\IntegerType::class,
			'string' => \Cake\Database\Type\StringType::class,
			'text' => \Cake\Database\Type\StringType::class,
			'time' => \Cake\Database\Type\TimeType::class,
			'timestamp' => \Cake\Database\Type\DateTimeType::class,
			'timestampfractional' => \Cake\Database\Type\DateTimeFractionalType::class,
			'tinyinteger' => \Cake\Database\Type\IntegerType::class,
			'uuid' => \Cake\Database\Type\UuidType::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'Abstract' => \App\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \App\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \App\Model\Table\CallbacksTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \App\Model\Table\CustomFinderTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'SkipMe' => \App\Model\Table\SkipMeTable::class,
			'SkipSome' => \App\Model\Table\SkipSomeTable::class,
			'Tools.Tokens' => \Tools\Model\Table\TokensTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
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
			'Abstract' => \App\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \App\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \App\Model\Table\CallbacksTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \App\Model\Table\CustomFinderTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'SkipMe' => \App\Model\Table\SkipMeTable::class,
			'SkipSome' => \App\Model\Table\SkipSomeTable::class,
			'Tools.Tokens' => \Tools\Model\Table\TokensTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
		])
	);

	override(
		\Cake\ORM\Table::addBehavior(0),
		map([
			'CounterCache' => \Cake\ORM\Table::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Table::class,
			'Shim.Nullable' => \Cake\ORM\Table::class,
			'Timestamp' => \Cake\ORM\Table::class,
			'Tools.AfterSave' => \Cake\ORM\Table::class,
			'Tools.Bitmasked' => \Cake\ORM\Table::class,
			'Tools.Confirmable' => \Cake\ORM\Table::class,
			'Tools.Jsonable' => \Cake\ORM\Table::class,
			'Tools.Neighbor' => \Cake\ORM\Table::class,
			'Tools.Passwordable' => \Cake\ORM\Table::class,
			'Tools.Reset' => \Cake\ORM\Table::class,
			'Tools.Slugged' => \Cake\ORM\Table::class,
			'Tools.String' => \Cake\ORM\Table::class,
			'Tools.Toggle' => \Cake\ORM\Table::class,
			'Tools.TypeMap' => \Cake\ORM\Table::class,
			'Tools.Typographic' => \Cake\ORM\Table::class,
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
			'Tools.Tokens' => \Cake\ORM\Association\BelongsToMany::class,
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
			'Tools.Tokens' => \Cake\ORM\Association\BelongsTo::class,
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
			'Tools.Tokens' => \Cake\ORM\Association\HasMany::class,
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
			'Tools.Tokens' => \Cake\ORM\Association\HasOne::class,
			'Wheels' => \Cake\ORM\Association\HasOne::class,
			'WheelsExtra' => \Cake\ORM\Association\HasOne::class,
		])
	);

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'Abstract' => \App\Model\Table\AbstractTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'BarBarsAbstract' => \App\Model\Table\BarBarsAbstractTable::class,
			'Callbacks' => \App\Model\Table\CallbacksTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Controllers.Houses' => \Controllers\Model\Table\HousesTable::class,
			'CustomFinder' => \App\Model\Table\CustomFinderTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'SkipMe' => \App\Model\Table\SkipMeTable::class,
			'SkipSome' => \App\Model\Table\SkipSomeTable::class,
			'Tools.Tokens' => \Tools\Model\Table\TokensTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
		])
	);

	expectedArguments(
		\Cake\Routing\Router::pathUrl(),
		0,
		'Bar::action',
		'Controllers.Generic::action',
		'Controllers.Houses::action',
		'Foo::action',
		'Tools.ShuntRequest::action'
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
			'Tools.pagination' => \Cake\View\View::class,
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
			'Html' => \App\View\Helper\HtmlHelper::class,
			'IdeHelper.DocBlock' => \IdeHelper\View\Helper\DocBlockHelper::class,
			'My' => \App\View\Helper\MyHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Shim.Configure' => \Shim\View\Helper\ConfigureHelper::class,
			'Shim.Cookie' => \Shim\View\Helper\CookieHelper::class,
			'Shim.Form' => \Shim\View\Helper\FormHelper::class,
			'Shim.Session' => \Shim\View\Helper\SessionHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Tools.Common' => \Tools\View\Helper\CommonHelper::class,
			'Tools.Form' => \Tools\View\Helper\FormHelper::class,
			'Tools.Format' => \Tools\View\Helper\FormatHelper::class,
			'Tools.Gravatar' => \Tools\View\Helper\GravatarHelper::class,
			'Tools.Html' => \Tools\View\Helper\HtmlHelper::class,
			'Tools.Number' => \Tools\View\Helper\NumberHelper::class,
			'Tools.Obfuscate' => \Tools\View\Helper\ObfuscateHelper::class,
			'Tools.Progress' => \Tools\View\Helper\ProgressHelper::class,
			'Tools.QrCode' => \Tools\View\Helper\QrCodeHelper::class,
			'Tools.Text' => \Tools\View\Helper\TextHelper::class,
			'Tools.Time' => \Tools\View\Helper\TimeHelper::class,
			'Tools.Timeline' => \Tools\View\Helper\TimelineHelper::class,
			'Tools.Tree' => \Tools\View\Helper\TreeHelper::class,
			'Tools.Typography' => \Tools\View\Helper\TypographyHelper::class,
			'Tools.Url' => \Tools\View\Helper\UrlHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		])
	);

}
