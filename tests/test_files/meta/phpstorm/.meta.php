<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	expectedArguments(
		\Cake\Cache\Cache::add(),
		2,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::clear(),
		0,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::clearGroup(),
		1,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::decrement(),
		2,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::delete(),
		1,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::deleteMany(),
		1,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::increment(),
		2,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::read(),
		1,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::readMany(),
		1,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::remember(),
		2,
		argumentsSet('cacheEngines')
	);

	expectedArguments(
		\Cake\Cache\Cache::write(),
		2,
		argumentsSet('cacheEngines')
	);

	exitPoint(\Cake\Console\ConsoleIo::abort());

	override(
		\Cake\Console\ConsoleIo::helper(0),
		map([
			'Progress' => \Cake\Shell\Helper\ProgressHelper::class,
			'Table' => \Cake\Shell\Helper\TableHelper::class,
		])
	);

	expectedArguments(
		\Cake\Controller\ComponentRegistry::unload(),
		0,
		'Auth',
		'CheckHttpCache',
		'Flash',
		'FormProtection',
		'My',
		'MyController',
		'MyOther',
		'Paginator',
		'RequestHandler',
		'Security'
	);

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'CheckHttpCache' => \Cake\Controller\Component\CheckHttpCacheComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'FormProtection' => \Cake\Controller\Component\FormProtectionComponent::class,
			'My' => \TestApp\Controller\Component\MyComponent::class,
			'MyController' => \TestApp\Controller\Component\MyControllerComponent::class,
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Controller\Component\MyComponent::class,
			'MyOther' => \TestApp\Controller\Component\MyOtherComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \TestApp\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
		])
	);

	expectedArguments(
		\Cake\Core\Configure::check(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::consume(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::consumeOrFail(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::delete(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::read(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::readOrFail(),
		0,
		argumentsSet('configureKeys')
	);

	expectedArguments(
		\Cake\Core\Configure::write(),
		0,
		argumentsSet('configureKeys')
	);

	override(
		\Cake\Core\PluginApplicationInterface::addPlugin(0),
		map([
			'Bake' => \Cake\Http\BaseApplication::class,
			'Cake/TwigView' => \Cake\Http\BaseApplication::class,
			'Migrations' => \Cake\Http\BaseApplication::class,
			'Shim' => \Cake\Http\BaseApplication::class,
		])
	);

	override(
		\Cake\Database\TypeFactory::build(0),
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
			'timestamptimezone' => \Cake\Database\Type\DateTimeTimezoneType::class,
			'tinyinteger' => \Cake\Database\Type\IntegerType::class,
			'uuid' => \Cake\Database\Type\UuidType::class,
		])
	);

	expectedArguments(
		\Cake\Database\TypeFactory::map(),
		0,
		'biginteger',
		'binary',
		'binaryuuid',
		'boolean',
		'char',
		'date',
		'datetime',
		'datetimefractional',
		'decimal',
		'float',
		'integer',
		'json',
		'smallinteger',
		'string',
		'text',
		'time',
		'timestamp',
		'timestampfractional',
		'timestamptimezone',
		'tinyinteger',
		'uuid'
	);

	expectedArguments(
		\Cake\Datasource\ConnectionManager::get(),
		0,
		'test'
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
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
			'children' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'path' => \Cake\ORM\Query::class,
			'somethingCustom' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
			'treeList' => \Cake\ORM\Query::class,
		])
	);

	expectedArguments(
		\Cake\Http\ServerRequest::getParam(),
		0,
		'_ext',
		'_matchedRoute',
		'action',
		'controller',
		'pass',
		'plugin',
		'prefix'
	);

	override(
		\Cake\Mailer\MailerAwareTrait::getMailer(0),
		map([
			'User' => \TestApp\Mailer\UserMailer::class,
		])
	);

	override(
		\Cake\ORM\Association::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'children' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'path' => \Cake\ORM\Query::class,
			'somethingCustom' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
			'treeList' => \Cake\ORM\Query::class,
		])
	);

	expectedArguments(
		\Cake\ORM\Entity::get(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::getError(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::getInvalidField(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::getOriginal(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::has(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::hasValue(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::isDirty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::isEmpty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::setDirty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	expectedArguments(
		\Cake\ORM\Entity::setError(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity')
	);

	override(
		\Cake\ORM\Locator\LocatorAwareTrait::fetchTable(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	expectedArguments(
		\Cake\ORM\Table::addBehavior(),
		0,
		'CounterCache',
		'MyNamespace/MyPlugin.My',
		'Shim.Nullable',
		'Timestamp',
		'Translate',
		'Tree'
	);

	override(
		\Cake\ORM\Table::belongToMany(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Bars' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Foos' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Users' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipMe' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipSome' => \Cake\ORM\Association\BelongsToMany::class,
			'Wheels' => \Cake\ORM\Association\BelongsToMany::class,
			'WheelsExtra' => \Cake\ORM\Association\BelongsToMany::class,
		])
	);

	override(
		\Cake\ORM\Table::belongsTo(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Bars' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Foos' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Users' => \Cake\ORM\Association\BelongsTo::class,
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
			'children' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'path' => \Cake\ORM\Query::class,
			'somethingCustom' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
			'treeList' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Table::hasMany(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\HasMany::class,
			'Relations.Bars' => \Cake\ORM\Association\HasMany::class,
			'Relations.Foos' => \Cake\ORM\Association\HasMany::class,
			'Relations.Users' => \Cake\ORM\Association\HasMany::class,
			'SkipMe' => \Cake\ORM\Association\HasMany::class,
			'SkipSome' => \Cake\ORM\Association\HasMany::class,
			'Wheels' => \Cake\ORM\Association\HasMany::class,
			'WheelsExtra' => \Cake\ORM\Association\HasMany::class,
		])
	);

	override(
		\Cake\ORM\Table::hasOne(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\HasOne::class,
			'Relations.Bars' => \Cake\ORM\Association\HasOne::class,
			'Relations.Foos' => \Cake\ORM\Association\HasOne::class,
			'Relations.Users' => \Cake\ORM\Association\HasOne::class,
			'SkipMe' => \Cake\ORM\Association\HasOne::class,
			'SkipSome' => \Cake\ORM\Association\HasOne::class,
			'Wheels' => \Cake\ORM\Association\HasOne::class,
			'WheelsExtra' => \Cake\ORM\Association\HasOne::class,
		])
	);

	expectedArguments(
		\Cake\ORM\Table::removeBehavior(),
		0,
		'CounterCache',
		'My',
		'Nullable',
		'Timestamp',
		'Translate',
		'Tree'
	);

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
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
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		])
	);

	expectedArguments(
		\Cake\Routing\Router::pathUrl(),
		0,
		argumentsSet('routePaths')
	);

	expectedArguments(
		\Cake\TestSuite\TestCase::addFixture(),
		0,
		'app.Houses',
		'core.Posts',
		'plugin.IdeHelper.Cars',
		'plugin.MyNamespace/MyPlugin.Sub/My'
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyArray(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyDate(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyDateTime(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyFile(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyFor(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyString(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyTime(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyArray(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyDate(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyDateTime(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyFile(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyString(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyTime(),
		2,
		argumentsSet('validationWhen')
	);

	expectedArguments(
		\Cake\Validation\Validator::requirePresence(),
		1,
		argumentsSet('validationWhen')
	);

	override(
		\Cake\View\CellTrait::cell(),
		map([
			'Test' => \TestApp\View\Cell\TestCell::class,
			'Test::custom' => \TestApp\View\Cell\TestCell::class,
		])
	);

	expectedArguments(
		\Cake\View\Helper\FormHelper::control(),
		0,
		'content',
		'created',
		'id',
		'modified',
		'name',
		'user_id'
	);

	expectedArguments(
		\Cake\View\Helper\HtmlHelper::linkFromPath(),
		1,
		argumentsSet('routePaths')
	);

	expectedArguments(
		\Cake\View\Helper\UrlHelper::buildFromPath(),
		0,
		argumentsSet('routePaths')
	);

	override(
		\Cake\View\View::addHelper(0),
		map([
			'Breadcrumbs' => \Cake\View\Helper\BreadcrumbsHelper::class,
			'Flash' => \Cake\View\Helper\FlashHelper::class,
			'Form' => \Cake\View\Helper\FormHelper::class,
			'Html' => \TestApp\View\Helper\HtmlHelper::class,
			'IdeHelper.DocBlock' => \IdeHelper\View\Helper\DocBlockHelper::class,
			'My' => \TestApp\View\Helper\MyHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Shim.Configure' => \Shim\View\Helper\ConfigureHelper::class,
			'Shim.Cookie' => \Shim\View\Helper\CookieHelper::class,
			'Shim.Form' => \Shim\View\Helper\FormHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		])
	);

	expectedArguments(
		\Cake\View\View::element(),
		0,
		'Awesome.pagination',
		'deeply/nested',
		'example'
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
			'Shim.Configure' => \Shim\View\Helper\ConfigureHelper::class,
			'Shim.Cookie' => \Shim\View\Helper\CookieHelper::class,
			'Shim.Form' => \Shim\View\Helper\FormHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		])
	);

	expectedArguments(
		\Cake\View\ViewBuilder::addHelper(),
		0,
		'Breadcrumbs',
		'Flash',
		'Form',
		'Html',
		'IdeHelper.DocBlock',
		'My',
		'Number',
		'Paginator',
		'Shim.Configure',
		'Shim.Cookie',
		'Shim.Form',
		'Text',
		'Time',
		'Url'
	);

	expectedArguments(
		\Cake\View\ViewBuilder::setLayout(),
		0,
		'ajax'
	);

	expectedArguments(
		\Migrations\AbstractMigration::table(),
		0,
		argumentsSet('tableNames')
	);

	expectedArguments(
		\Migrations\AbstractSeed::table(),
		0,
		argumentsSet('tableNames')
	);

	expectedArguments(
		\Migrations\Table::addColumn(),
		0,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Migrations\Table::addColumn(),
		1,
		argumentsSet('columnTypes')
	);

	expectedArguments(
		\Migrations\Table::changeColumn(),
		0,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Migrations\Table::changeColumn(),
		1,
		argumentsSet('columnTypes')
	);

	expectedArguments(
		\Migrations\Table::hasColumn(),
		0,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Migrations\Table::removeColumn(),
		0,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Migrations\Table::renameColumn(),
		0,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Migrations\Table::renameColumn(),
		1,
		argumentsSet('columnNames')
	);

	expectedArguments(
		\Phinx\Seed\AbstractSeed::table(),
		0,
		argumentsSet('tableNames')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo')
	);

	expectedArguments(
		\Relations\Model\Entity\User::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\Relations\Model\Entity\User::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel')
	);

	expectedArguments(
		\__d(),
		0,
		'awesome',
		'cake',
		'controllers',
		'ide_helper',
		'my_namespace/my_plugin',
		'relations',
		'shim'
	);

	expectedArguments(
		\env(),
		0,
		'HTTP_HOST'
	);

	expectedArguments(
		\urlArray(),
		0,
		argumentsSet('routePaths')
	);

	registerArgumentsSet(
		'cacheEngines',
		'_cake_core_',
		'_cake_model_',
		'default'
	);

	registerArgumentsSet(
		'columnNames',
		'content',
		'created',
		'id',
		'name'
	);

	registerArgumentsSet(
		'columnTypes',
		'biginteger',
		'binary',
		'binaryuuid',
		'bit',
		'blob',
		'boolean',
		'char',
		'date',
		'datetime',
		'decimal',
		'double',
		'float',
		'integer',
		'json',
		'smallinteger',
		'string',
		'text',
		'time',
		'timestamp',
		'uuid',
		'year'
	);

	registerArgumentsSet(
		'configureKeys',
		'App',
		'App.encoding',
		'App.namespace',
		'App.paths',
		'App.paths.templates',
		'IdeHelper',
		'IdeHelper.skipDatabaseTables',
		'debug',
		'plugins',
		'plugins.Bake',
		'plugins.Cake/TwigView',
		'plugins.Migrations',
		'plugins.Shim'
	);

	registerArgumentsSet(
		'entityFields:Cake\ORM\Entity',
		'car',
		'content',
		'created',
		'id',
		'name'
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\Bar',
		'id',
		'name',
		'user',
		'user_id'
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\Foo',
		'id',
		'name',
		'user',
		'user_id'
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\User',
		'bar',
		'foo',
		'id',
		'name'
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\BarBar',
		'content',
		'created',
		'foo',
		'houses',
		'id',
		'name'
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\BarBarsAbstract',
		'content',
		'created',
		'foo',
		'houses',
		'id',
		'name'
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\Car',
		'content',
		'created',
		'id',
		'modified',
		'name',
		'wheels'
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\Wheel',
		'car',
		'content',
		'created',
		'id',
		'name',
		'virtual_one'
	);

	registerArgumentsSet(
		'routePaths',
		'Awesome.Admin/AwesomeHouses::openDoor',
		'Bar::index'
	);

	registerArgumentsSet(
		'tableNames',
		'wheels'
	);

	registerArgumentsSet(
		'validationWhen',
		'create',
		'update'
	);

}
