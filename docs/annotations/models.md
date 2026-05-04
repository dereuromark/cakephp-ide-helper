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
 * @method \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location>|false saveMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location> saveManyOrFail(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location>|false deleteMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location> deleteManyOrFail(iterable $entities, array $options = [])
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

- `false` (default) — bare `array` params, legacy behavior.
- `true` — basic generics: `array<mixed>` / `array<string, mixed>` / `iterable<TEntity>`.
- `'detailed'` — fully detailed types throughout, matching the richer form PHPStan and Psalm understand best.

With `'detailed'`, the generated method annotations look like:

```php
 * @method \App\Model\Entity\User newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\App\Model\Entity\User> newEntities(array<array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \App\Model\Entity\User get(mixed $primaryKey, array<string, mixed>|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\User findOrCreate(\Cake\ORM\Query\SelectQuery<\App\Model\Entity\User>|callable|array<string, mixed> $search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \App\Model\Entity\User>|false saveMany(iterable<\App\Model\Entity\User> $entities, array<string, mixed> $options = [])
```

Switching the value is additive — existing `true` users keep their current
output, and the new `'detailed'` opt-in can be enabled at any time.

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
 * @property string|null $virtual_property
 *
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\User $user
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

### Virtual properties

For virtual properties the annotator looks up the respective `_get...()`
methods (e.g. `_getVirtualProperty()` for `$virtual_property`). It first checks
the documented type in the doc block's `@return`, otherwise (given PHP 7.0+)
tries to read it from the return type hint (e.g. `: ?string`). Only if that is
also not present does it fall back to `mixed`.

Note: You can also use the `@property-read` tag if it is a pure virtual field
getter.
