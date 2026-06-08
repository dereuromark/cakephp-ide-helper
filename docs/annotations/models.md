# Models

Annotate Tables and their Entities:

```bash
bin/cake annotate models
```

## Tables

Tables annotate their entity-related methods, their relations, and behavior
mixins.

A `LocationsTable` class would gain the following doc-block annotations if not
already present:

```php
/**
 * @method \App\Model\Entity\Location newEmptyEntity()
 * @method \App\Model\Entity\Location newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Location> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Location> find(string $type = 'all', mixed ...$args)
 * @method \App\Model\Entity\Location|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Location saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Location> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\Location>|false saveMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\Location> saveManyOrFail(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\Location>|false deleteMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\Location> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @property \Cake\ORM\Association\HasMany<\App\Model\Table\ImagesTable> $Images
 * @property \Cake\ORM\Association\BelongsTo<\App\Model\Table\UsersTable> $Users
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
```

### Entity-aware `find()` return type

If you want the table annotations to also expose the entity-aware `find()`
return type for IDEs, enable:

```php
'IdeHelper' => [
    'tableEntityQuery' => true,
],
```

This is intentionally optional, because finder result shapes can still widen
beyond plain entities.

### Detailed param types

The `IdeHelper.genericsInParam` option is tri-state:

- `false` (default) — bare `array` params, legacy behavior. Collection return
  types still carry `ResultSetInterface<int, TEntity>` (its real two-param arity).
- `true` — basic generics. Entity-data and `$contain` params use `array<mixed>`
  (`$contain` also accepts the list form `['Comments', 'Tags']`); the `$finder`,
  `$search`, and `$options` params use `array<string, mixed>`, alongside
  `SelectQuery<TEntity>` / `iterable<TEntity>` / `ResultSetInterface<int, TEntity>`.
  No param stays a bare `array` and no generic class is left unparametrized, so
  PHPStan's `missingType.iterableValue` and `missingType.generics` stay clean at the
  strictest level - while IDE/tooling still copes, since there are no nested
  `array<array<...>>` shapes.
- `'detailed'` — fully detailed types throughout (nested `array<array<string, mixed>>`
  for entity lists), matching the richer form PHPStan and Psalm understand best.

With `'detailed'`, the generated method annotations look like:

```php
 * @method \App\Model\Entity\User newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\App\Model\Entity\User> newEntities(array<array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \App\Model\Entity\User get(mixed $primaryKey, array<string, mixed>|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\User findOrCreate(\Cake\ORM\Query\SelectQuery<\App\Model\Entity\User>|callable|array<string, mixed> $search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\User>|false saveMany(iterable<\App\Model\Entity\User> $entities, array<string, mixed> $options = [])
```

Switching the value only tightens the generated types; re-running the annotator
updates the existing `@method` lines in place, and the `'detailed'` opt-in can be
enabled at any time.

### Concrete entity types in params

The `IdeHelper.concreteEntitiesInParam` option is tri-state:

- `false` (default) — entity params accept `\Cake\Datasource\EntityInterface`.
- `true` — `patchEntity()`, `save()`, and `saveOrFail()` declare the concrete
  entity class for their `$entity` parameter, so PHPStan can catch a mismatched
  entity passed to the wrong table.
- `'strict'` — same as `true`, plus:
  - iterable params on `patchEntities` / `saveMany` / `saveManyOrFail` /
    `deleteMany` / `deleteManyOrFail` are narrowed to
    `iterable<\App\Model\Entity\X>` even when `genericsInParam` is `false`.
  - `delete()`, `deleteOrFail()`, and `loadInto()` get explicit `@method` lines
    typed with the concrete entity class.

This mirrors the runtime check proposed upstream in
[cakephp/cakephp#19428](https://github.com/cakephp/cakephp/pull/19428): a
`Table::delete($order)` call against an `InvoicesTable` is rejected at
static-analysis time instead of silently deleting from the wrong table.

With `'strict'` (and `genericsInParam` left at default `false`), the generated
method block adds:

```php
 * @method \App\Model\Entity\User patchEntity(\App\Model\Entity\User $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable<\App\Model\Entity\User> $entities, array $data, array $options = [])
 * @method bool delete(\App\Model\Entity\User $entity, array $options = [])
 * @method bool deleteOrFail(\App\Model\Entity\User $entity, array $options = [])
 * @method \App\Model\Entity\User|array<\App\Model\Entity\User> loadInto(\App\Model\Entity\User|array<\App\Model\Entity\User> $entities, array $contain)
```

Switching is additive — existing `true` users keep their current output, and
`'strict'` can be enabled at any time. Pair with `genericsInParam` at `true` or
`'detailed'` for fully typed params throughout.

## Entities

Entities annotate their properties and relations.

A `Location` entity could look like this afterward:

```php
/**
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $location
 * @property string $details
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\User $user
 *
 * @property-read string|null $virtual_property
 */
class Location extends Entity {
}
```

### Custom type maps

Using the Configure key `'IdeHelper.typeMap'` you can set a custom array of
types to be used for the field mapping. Overwriting the defaults of this
plugin is also possible — to skip (reset) just set the value to `null`:

```php
'IdeHelper' => [
    'typeMap' => [
        'custom' => 'array',
        'longtext' => null,
        // ...
    ],
],
```

Using the Configure key `'IdeHelper.nullableMap'` you can set a custom array
of types and whether they can be nullable:

```php
'IdeHelper' => [
    'nullableMap' => [
        'custom' => false,
        'longtext' => true,
        // ...
    ],
],
```

### Custom enum types

Columns mapped to a backed enum via `EnumType::from(MyEnum::class)` are detected
automatically and annotated with the concrete enum class, e.g.
`@property \App\Model\Enum\MyEnum|null $status`.

A **custom `EnumType` subclass** (for example a lenient type that tolerates legacy
values) registered under its own name is *not* detected out of the box, because the
annotator only sees the type name from the schema and cannot tell that name maps to
an enum — it falls back to `string`:

```php
// config/bootstrap.php
TypeFactory::map('lenient_status', LenientStatusType::class); // extends EnumType

// Table::initialize()
$this->getSchema()->setColumnType('status', 'lenient_status');
// => @property string|null $status   (enum class lost)
```

Register it under an **`enum-` prefixed name** so the resolver recognises it as an
enum and reads the backing class via `getEnumClassName()` — no `typeMap` entry needed:

```php
TypeFactory::map('enum-lenient_status', LenientStatusType::class);
$this->getSchema()->setColumnType('status', 'enum-lenient_status');
// => @property \App\Model\Enum\MyStatus|null $status
```

The only requirement is that the registered type name starts with `enum-` and the
type resolves to a `\Cake\Database\Type\EnumType` instance. (Alternatively, map the
custom name to the enum class via `IdeHelper.typeMap`.)

### Virtual properties

For virtual properties the annotator looks up the respective `_get...()`
methods (e.g. `_getVirtualProperty()` for `$virtual_property`). It first checks
the documented type in the doc block's `@return`, otherwise (given PHP 7.0+)
tries to read it from the return type hint (e.g. `: ?string`). Only if that is
also not present does it fall back to `mixed`.

Pure virtual fields are annotated with the `@property-read` tag instead of
`@property`: a field counts as read-only when it has a `_get...()` accessor but
neither a real DB column / association behind it nor a matching `_set...()`
mutator. If you do add a `_set...()` mutator (or the name also maps to a real
column), the field stays writable and keeps the plain `@property` tag.
