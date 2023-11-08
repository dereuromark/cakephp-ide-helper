<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	expectedArguments(
		\Cake\Cache\Cache::add(),
		2,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::clear(),
		0,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::clearGroup(),
		1,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::decrement(),
		2,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::delete(),
		1,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::deleteMany(),
		1,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::increment(),
		2,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::read(),
		1,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::readMany(),
		1,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::remember(),
		2,
		argumentsSet('cacheEngines'),
	);

	expectedArguments(
		\Cake\Cache\Cache::write(),
		2,
		argumentsSet('cacheEngines'),
	);

	exitPoint(\Cake\Console\ConsoleIo::abort());

	override(
		\Cake\Console\ConsoleIo::helper(0),
		map([
			'Progress' => \Cake\Command\Helper\ProgressHelper::class,
			'Table' => \Cake\Command\Helper\TableHelper::class,
		]),
	);

	expectedArguments(
		\Cake\Controller\ComponentRegistry::unload(),
		0,
		'CheckHttpCache',
		'Flash',
		'FormProtection',
		'My',
		'MyController',
		'MyOther',
	);

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'CheckHttpCache' => \TestApp\Controller\Component\CheckHttpCacheComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'FormProtection' => \Cake\Controller\Component\FormProtectionComponent::class,
			'My' => \TestApp\Controller\Component\MyComponent::class,
			'MyController' => \TestApp\Controller\Component\MyControllerComponent::class,
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Controller\Component\MyComponent::class,
			'MyOther' => \TestApp\Controller\Component\MyOtherComponent::class,
		]),
	);

	expectedArguments(
		\Cake\Core\Configure::check(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::consume(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::consumeOrFail(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::delete(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::read(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::readOrFail(),
		0,
		argumentsSet('configureKeys'),
	);

	expectedArguments(
		\Cake\Core\Configure::write(),
		0,
		argumentsSet('configureKeys'),
	);

	override(
		\Cake\Core\PluginApplicationInterface::addPlugin(0),
		map([
			'Bake' => \Cake\Http\BaseApplication::class,
			'Cake/TwigView' => \Cake\Http\BaseApplication::class,
			'Migrations' => \Cake\Http\BaseApplication::class,
			'Shim' => \Cake\Http\BaseApplication::class,
		]),
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
		]),
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
		'uuid',
	);

	expectedArguments(
		\Cake\Datasource\ConnectionManager::get(),
		0,
		'test',
	);

	override(
		\Cake\Datasource\ModelAwareTrait::fetchModel(0),
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
			'Foos' => \TestApp\Model\Table\FoosTable::class,
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		]),
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
		]),
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
		'prefix',
	);

	override(
		\Cake\Mailer\MailerAwareTrait::getMailer(0),
		map([
			'User' => \TestApp\Mailer\UserMailer::class,
		]),
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
		]),
	);

	expectedArguments(
		\Cake\ORM\Entity::get(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::getError(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::getInvalidField(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::getOriginal(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::has(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::hasValue(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::isDirty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::isEmpty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::setDirty(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
	);

	expectedArguments(
		\Cake\ORM\Entity::setError(),
		0,
		argumentsSet('entityFields:Cake\ORM\Entity'),
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
			'Foos' => \TestApp\Model\Table\FoosTable::class,
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		]),
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
			'Foos' => \TestApp\Model\Table\FoosTable::class,
			'MyNamespace/MyPlugin.My' => \MyNamespace\MyPlugin\Model\Table\MyTable::class,
			'Relations.Bars' => \Relations\Model\Table\BarsTable::class,
			'Relations.Foos' => \Relations\Model\Table\FoosTable::class,
			'Relations.Users' => \Relations\Model\Table\UsersTable::class,
			'SkipMe' => \TestApp\Model\Table\SkipMeTable::class,
			'SkipSome' => \TestApp\Model\Table\SkipSomeTable::class,
			'Wheels' => \TestApp\Model\Table\WheelsTable::class,
			'WheelsExtra' => \TestApp\Model\Table\WheelsExtraTable::class,
		]),
	);

	expectedArguments(
		\Cake\ORM\Table::addBehavior(),
		0,
		'CounterCache',
		'MyNamespace/MyPlugin.My',
		'Shim.Nullable',
		'Timestamp',
		'Translate',
		'Tree',
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
			'Foos' => \Cake\ORM\Association\BelongsToMany::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Bars' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Foos' => \Cake\ORM\Association\BelongsToMany::class,
			'Relations.Users' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipMe' => \Cake\ORM\Association\BelongsToMany::class,
			'SkipSome' => \Cake\ORM\Association\BelongsToMany::class,
			'Wheels' => \Cake\ORM\Association\BelongsToMany::class,
			'WheelsExtra' => \Cake\ORM\Association\BelongsToMany::class,
		]),
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
			'Foos' => \Cake\ORM\Association\BelongsTo::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Bars' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Foos' => \Cake\ORM\Association\BelongsTo::class,
			'Relations.Users' => \Cake\ORM\Association\BelongsTo::class,
			'SkipMe' => \Cake\ORM\Association\BelongsTo::class,
			'SkipSome' => \Cake\ORM\Association\BelongsTo::class,
			'Wheels' => \Cake\ORM\Association\BelongsTo::class,
			'WheelsExtra' => \Cake\ORM\Association\BelongsTo::class,
		]),
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
		]),
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
			'Foos' => \Cake\ORM\Association\HasMany::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\HasMany::class,
			'Relations.Bars' => \Cake\ORM\Association\HasMany::class,
			'Relations.Foos' => \Cake\ORM\Association\HasMany::class,
			'Relations.Users' => \Cake\ORM\Association\HasMany::class,
			'SkipMe' => \Cake\ORM\Association\HasMany::class,
			'SkipSome' => \Cake\ORM\Association\HasMany::class,
			'Wheels' => \Cake\ORM\Association\HasMany::class,
			'WheelsExtra' => \Cake\ORM\Association\HasMany::class,
		]),
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
			'Foos' => \Cake\ORM\Association\HasOne::class,
			'MyNamespace/MyPlugin.My' => \Cake\ORM\Association\HasOne::class,
			'Relations.Bars' => \Cake\ORM\Association\HasOne::class,
			'Relations.Foos' => \Cake\ORM\Association\HasOne::class,
			'Relations.Users' => \Cake\ORM\Association\HasOne::class,
			'SkipMe' => \Cake\ORM\Association\HasOne::class,
			'SkipSome' => \Cake\ORM\Association\HasOne::class,
			'Wheels' => \Cake\ORM\Association\HasOne::class,
			'WheelsExtra' => \Cake\ORM\Association\HasOne::class,
		]),
	);

	expectedArguments(
		\Cake\ORM\Table::removeBehavior(),
		0,
		'CounterCache',
		'My',
		'Nullable',
		'Timestamp',
		'Translate',
		'Tree',
	);

	expectedArguments(
		\Cake\Routing\Router::pathUrl(),
		0,
		argumentsSet('routePaths'),
	);

	expectedArguments(
		\Cake\TestSuite\TestCase::addFixture(),
		0,
		'app.BarBars',
		'app.Cars',
		'app.Foos',
		'app.Houses',
		'app.Wheels',
		'app.Windows',
		'core.Articles',
		'core.ArticlesMoreTranslations',
		'core.ArticlesTags',
		'core.ArticlesTagsBindingKeys',
		'core.ArticlesTranslations',
		'core.Attachments',
		'core.AuthUsers',
		'core.Authors',
		'core.AuthorsTags',
		'core.AuthorsTranslations',
		'core.BinaryUuidItems',
		'core.BinaryUuidItemsBinaryUuidTags',
		'core.BinaryUuidTags',
		'core.CakeSessions',
		'core.Categories',
		'core.ColumnSchemaAwareTypeValues',
		'core.Comments',
		'core.CommentsTranslations',
		'core.CompositeIncrements',
		'core.CompositeKeyArticles',
		'core.CompositeKeyArticlesTags',
		'core.CounterCacheCategories',
		'core.CounterCacheComments',
		'core.CounterCachePosts',
		'core.CounterCacheUserCategoryPosts',
		'core.CounterCacheUsers',
		'core.Datatypes',
		'core.DateKeys',
		'core.FeaturedTags',
		'core.Members',
		'core.MenuLinkTrees',
		'core.NullableAuthors',
		'core.NumberTrees',
		'core.NumberTreesArticles',
		'core.OrderedUuidItems',
		'core.Orders',
		'core.OtherArticles',
		'core.PolymorphicTagged',
		'core.Posts',
		'core.Products',
		'core.Profiles',
		'core.Sections',
		'core.SectionsMembers',
		'core.SectionsTranslations',
		'core.Sessions',
		'core.SiteArticles',
		'core.SiteArticlesTags',
		'core.SiteAuthors',
		'core.SiteTags',
		'core.SpecialTags',
		'core.SpecialTagsTranslations',
		'core.Tags',
		'core.TagsShadowTranslations',
		'core.TagsTranslations',
		'core.TestPluginComments',
		'core.Things',
		'core.Translates',
		'core.UniqueAuthors',
		'core.Users',
		'core.UuidItems',
		'plugin.IdeHelper.BarBars',
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Foos',
		'plugin.IdeHelper.Houses',
		'plugin.IdeHelper.Wheels',
		'plugin.IdeHelper.Windows',
		'plugin.MyNamespace/MyPlugin.Sub/My',
		'plugin.Shim.Articles',
		'plugin.Shim.Authors',
		'plugin.Shim.Cars',
		'plugin.Shim.CarsWheels',
		'plugin.Shim.Comments',
		'plugin.Shim.NullableTenants',
		'plugin.Shim.Nullables',
		'plugin.Shim.Posts',
		'plugin.Shim.TimeTypes',
		'plugin.Shim.Users',
		'plugin.Shim.UuidItems',
		'plugin.Shim.Wheels',
		'plugin.Shim.YearTypes',
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyArray(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyDate(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyDateTime(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyFile(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyFor(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyString(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::allowEmptyTime(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyArray(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyDate(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyDateTime(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyFile(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyString(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::notEmptyTime(),
		2,
		argumentsSet('validationWhen'),
	);

	expectedArguments(
		\Cake\Validation\Validator::requirePresence(),
		1,
		argumentsSet('validationWhen'),
	);

	override(
		\Cake\View\CellTrait::cell(),
		map([
			'Test' => \TestApp\View\Cell\TestCell::class,
			'Test::custom' => \TestApp\View\Cell\TestCell::class,
		]),
	);

	expectedArguments(
		\Cake\View\Helper\FormHelper::control(),
		0,
		'content',
		'created',
		'id',
		'modified',
		'name',
		'user_id',
	);

	expectedArguments(
		\Cake\View\Helper\HtmlHelper::linkFromPath(),
		1,
		argumentsSet('routePaths'),
	);

	expectedArguments(
		\Cake\View\Helper\UrlHelper::buildFromPath(),
		0,
		argumentsSet('routePaths'),
	);

	expectedArguments(
		\Cake\View\View::element(),
		0,
		'Awesome.pagination',
		'deeply/nested',
		'example',
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
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		]),
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
		'Text',
		'Time',
		'Url',
	);

	expectedArguments(
		\Cake\View\ViewBuilder::setLayout(),
		0,
		'ajax',
	);

	expectedArguments(
		\Migrations\AbstractMigration::table(),
		0,
		argumentsSet('tableNames'),
	);

	expectedArguments(
		\Migrations\AbstractSeed::table(),
		0,
		argumentsSet('tableNames'),
	);

	expectedArguments(
		\Migrations\Table::addColumn(),
		0,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Migrations\Table::addColumn(),
		1,
		argumentsSet('columnTypes'),
	);

	expectedArguments(
		\Migrations\Table::changeColumn(),
		0,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Migrations\Table::changeColumn(),
		1,
		argumentsSet('columnTypes'),
	);

	expectedArguments(
		\Migrations\Table::hasColumn(),
		0,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Migrations\Table::removeColumn(),
		0,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Migrations\Table::renameColumn(),
		0,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Migrations\Table::renameColumn(),
		1,
		argumentsSet('columnNames'),
	);

	expectedArguments(
		\Phinx\Seed\AbstractSeed::table(),
		0,
		argumentsSet('tableNames'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Bar::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Bar'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\Foo::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\Foo'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::get(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::getError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::getInvalidField(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::getOriginal(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::has(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::hasValue(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::isDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::isEmpty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::setDirty(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\Relations\Model\Entity\User::setError(),
		0,
		argumentsSet('entityFields:Relations\Model\Entity\User'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBar::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBar'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\BarBarsAbstract::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\BarBarsAbstract'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Car::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Car'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Foo::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Foo'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::get(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getInvalidField(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::getOriginal(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::has(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::hasValue(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::isDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::isEmpty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::setDirty(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
	);

	expectedArguments(
		\TestApp\Model\Entity\Wheel::setError(),
		0,
		argumentsSet('entityFields:TestApp\Model\Entity\Wheel'),
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
		'shim',
	);

	expectedArguments(
		\env(),
		0,
		'CGI_MODE',
		'CINNAMON_VERSION',
		'CONTENT_LENGTH',
		'CONTENT_TYPE',
		'DBUS_SESSION_BUS_ADDRESS',
		'DEFAULTS_PATH',
		'DESKTOP_SESSION',
		'DESKTOP_STARTUP_ID',
		'DISPLAY',
		'DOCUMENT_ROOT',
		'DOCUMENT_URI',
		'FIG_JETBRAINS_SHELL_INTEGRATION',
		'GATEWAY_INTERFACE',
		'GDMSESSION',
		'GDM_LANG',
		'GIO_LAUNCHED_DESKTOP_FILE',
		'GIO_LAUNCHED_DESKTOP_FILE_PID',
		'GJS_DEBUG_OUTPUT',
		'GJS_DEBUG_TOPICS',
		'GNOME_DESKTOP_SESSION_ID',
		'GPG_AGENT_INFO',
		'GTK3_MODULES',
		'GTK_MODULES',
		'GTK_OVERLAY_SCROLLING',
		'HOME',
		'HTTPS',
		'HTTP_ACCEPT',
		'HTTP_ACCEPT_ENCODING',
		'HTTP_ACCEPT_LANGUAGE',
		'HTTP_CONNECTION',
		'HTTP_COOKIE',
		'HTTP_HOST',
		'HTTP_USER_AGENT',
		'LANG',
		'LANGUAGE',
		'LC_ADDRESS',
		'LC_IDENTIFICATION',
		'LC_MEASUREMENT',
		'LC_MONETARY',
		'LC_NAME',
		'LC_NUMERIC',
		'LC_PAPER',
		'LC_TELEPHONE',
		'LC_TIME',
		'LESSCLOSE',
		'LESSOPEN',
		'LOGNAME',
		'LS_COLORS',
		'MANDATORY_PATH',
		'NVM_BIN',
		'NVM_CD_FLAGS',
		'NVM_DIR',
		'NVM_INC',
		'PAPERSIZE',
		'PATH',
		'PATH_TRANSLATED',
		'PHP_SELF',
		'PWD',
		'QT_ACCESSIBILITY',
		'QT_QPA_PLATFORMTHEME',
		'QUERY_STRING',
		'REDIRECT_STATUS',
		'REMOTE_ADDR',
		'REMOTE_PORT',
		'REQUEST_METHOD',
		'REQUEST_SCHEME',
		'REQUEST_TIME',
		'REQUEST_TIME_FLOAT',
		'REQUEST_URI',
		'SCRIPT_FILENAME',
		'SCRIPT_NAME',
		'SERVER_NAME',
		'SERVER_PORT',
		'SERVER_PROTOCOL',
		'SESSION_MANAGER',
		'SHELL',
		'SHLVL',
		'SSH_AGENT_PID',
		'SSH_AUTH_SOCK',
		'TERM',
		'TERMINAL_EMULATOR',
		'TERM_SESSION_ID',
		'USER',
		'XAUTHORITY',
		'XDG_CONFIG_DIRS',
		'XDG_CURRENT_DESKTOP',
		'XDG_DATA_DIRS',
		'XDG_GREETER_DATA_DIR',
		'XDG_RUNTIME_DIR',
		'XDG_SEAT',
		'XDG_SEAT_PATH',
		'XDG_SESSION_CLASS',
		'XDG_SESSION_DESKTOP',
		'XDG_SESSION_ID',
		'XDG_SESSION_PATH',
		'XDG_SESSION_TYPE',
		'XDG_VTNR',
		'argc',
		'argv',
	);

	expectedArguments(
		\urlArray(),
		0,
		argumentsSet('routePaths'),
	);

	registerArgumentsSet(
		'cacheEngines',
		'_cake_core_',
		'_cake_model_',
		'default',
	);

	registerArgumentsSet(
		'columnNames',
		'content',
		'created',
		'id',
		'modified',
		'name',
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
		'year',
	);

	registerArgumentsSet(
		'configureKeys',
		'App',
		'App.encoding',
		'App.namespace',
		'App.paths',
		'App.paths.templates',
		'Shim',
		'Shim.deprecations',
		'debug',
		'plugins',
		'plugins.Bake',
		'plugins.Cake/TwigView',
		'plugins.Migrations',
		'plugins.Shim',
	);

	registerArgumentsSet(
		'entityFields:Cake\ORM\Entity',
		'id',
		'name',
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\Bar',
		'id',
		'name',
		'user',
		'user_id',
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\Foo',
		'id',
		'name',
		'user',
		'user_id',
	);

	registerArgumentsSet(
		'entityFields:Relations\Model\Entity\User',
		'bar',
		'foo',
		'id',
		'name',
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\BarBar',
		'content',
		'created',
		'foo',
		'houses',
		'id',
		'name',
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\BarBarsAbstract',
		'content',
		'created',
		'foo',
		'houses',
		'id',
		'name',
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\Car',
		'content',
		'created',
		'id',
		'modified',
		'name',
		'wheels',
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\Foo',
		'content',
		'created',
		'id',
		'name',
	);

	registerArgumentsSet(
		'entityFields:TestApp\Model\Entity\Wheel',
		'car',
		'content',
		'created',
		'id',
		'name',
		'virtual_one',
	);

	registerArgumentsSet(
		'routePaths',
		'Awesome.Admin/AwesomeHouses::openDoor',
		'Bar::index',
	);

	registerArgumentsSet(
		'tableNames',
		'bar_bars',
		'cars',
		'foos',
		'houses',
		'wheels',
		'windows',
	);

	registerArgumentsSet(
		'validationWhen',
		'create',
		'update',
	);

}
