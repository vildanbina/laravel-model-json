[![Latest Stable Version](https://poser.pugx.org/vildanbina/laravel-model-json/v)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![Total Downloads](https://poser.pugx.org/vildanbina/laravel-model-json/downloads)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![Latest Unstable Version](https://poser.pugx.org/vildanbina/laravel-model-json/v/unstable)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![License](https://poser.pugx.org/vildanbina/laravel-model-json/license)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![PHP Version Require](https://poser.pugx.org/vildanbina/laravel-model-json/require/php)](https://packagist.org/packages/vildanbina/laravel-model-json)

## Introduction

The Laravel Model JSON package allows you to export data from a specific model in the form of a JSON file. It is based on the `php artisan` command and is easy to use.

## Installation

To install this package, use the following command:

``` bash
composer require vildanbina/laravel-model-json
```

## Usage

# Export

The command to export data from a model is `php artisan model:export {model}`, where `{model}` is the class name of the model you wish to export. After running this command, the data will be automatically saved in the `storage/app` folder.

For example, to export data from the `User` model, you would run the following command:

```bash
php artisan model:export User
```

If your model is located in a different folder, you can specify the exact location, like so:

```bash
php artisan model:export App\Models\User
```

## Options

### Choose your path to save

This package also has several options that allow you to customize the export functionality. For example, you can use the `--path=public` option to save the JSON data in a different folder. Here's an example:

```bash
php artisan model:export User --path=public
```

### Filename

By default, the filename of the JSON data is "Model-Timestamp", but you can also specify a custom filename using the `--filename=data` option. For example:

```bash
php artisan model:export User --filename=data
```

### Except Fields from export

You can also exclude certain columns from the export by using the `--except-fields` option. This is useful if you only want to export certain data from the model. For example:

```bash
php artisan model:export User --except-fields=id,deleted_at
```

### Without timestamps

To exclude the `created_at`, `updated_at`, and `deleted_at` columns from the export, use the `--without-timestamps` option. For example:

```bash
php artisan model:export User --without-timestamps
```

### Without global scopes

You can remove registered global scopes from the export with the `--without-global-scopes` option. For example:

```bash
php artisan model:export User --without-global-scopes
```

### With hidden

By default, only visible fields are included in the export. To also include all hidden fields in the export, use the `--with-hidden` option. For example:

```bash
php artisan model:export User --with-hidden
```

This will also apply to any included relation(s) if used in combination with the `--with-relationships` option.

### Select only specific fields

If a model has a large number of columns and you only want to export a subset of them, you can use the `--only-fields` option. This allows you to specify which columns you want to include in the export. For example:

```bash
php artisan model:export User --only-fields=name,email
```

### Forget data

You can forget data from the export by using the dot notation, accepting wildcards using asterisks. For example:

```bash
php artisan model:export Post --forget-data=comments.*.moderated_at
```

This can be useful if you include relations with the `--with-relationships` option and you would like to remove `chaperone()`'d relations from the nested data.

The `--forget-data` option supports one or more keys, comma separated.

### Apply a specific scope to the query

If you wish to apply a scope to the model query because you wish to exclude certain records, you can use the `--scope={scope}` option. This allows you to specify a scope for the records you want to include in the export. For example:

```bash
php artisan model:export User --scope=verified
```

On your User Model you would have the following function:

```php
    public function scopeVerified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }
```

### Relationships

You can now export models along with their specified relationships using the new option `--with-relationships={relations}`. `{relations}` are the names of the relationships and can be separated by `+` symbol if you want to attach more than one relationship.

For example, if you want to export a Product model along with its Category relationship, you can use the command:

```bash
php artisan model:export Product --with-relationships=category
```

If you want to export a Product model along with both its Category and Supplier relationships, you can use the command:

```bash
php artisan model:export Product --with-relationships=category+supplier
```

Additionally, you can choose to only export specific columns of the relationship by using the syntax `{relationship_name}:{columns_to_export}`.

For example, if you want to export a `Product` model along with its `Category` relationship and only export the `id` and `name` columns of the `Category`, you can use the command:

```bash
php artisan model:export Product --with-relationships=category:id,name
```

\
If you want to save JSON in a file as a beautified version, you can use the `--beautify` option or its shorthand `-b`. For example:

```bash
php artisan model:export User --beautify

#or

php artisan model:export User -b
```

By default, it will be exported to an inline JSON.

***

# Import

The `model:import` command allows you to import data from a JSON file and store it in your database.

For example, to import data for the `User` model, you would run the following command:

## Parameters

+ `model`: Required. The name of the model to be imported.
+ `path`: Required. The path to the JSON file, which must contain valid JSON data.

Example:

```bash
php artisan model:import User public/Users.json
```

This command will store all the data found in the JSON file in the database.

## Except Fields from importing

You can exclude specific columns by using the `--except-fields` option, separated by commas, ex:

```bash
php artisan model:import User public/Users.json --except-fields=email_verified_at
```

You can also exclude timestamps by using the `--without-timestamps` option.

## Select only specific fields to import

If you only want to store specific fields, you can use the `--only-fields` option, separated by commas. Ex:

```bash
php artisan model:import User public/Users.json --only-fields=first_name,last_name,email
```

## Forget data

You can forget data from the import by using the dot notation, accepting wildcards using asterisks. For example:

```bash
php artisan model:import Post public/Posts.json --forget-data=comments.*.moderated_at
```

The `--forget-data` option supports one or more keys, comma separated.

## Update existing records

You can update existing records in the database instead of creating duplicates by using the `--update-when-exists` option, ex:

```bash
php artisan model:import User public/Users.json --update-when-exists
```

If you want to group the updates based on a different column, you can use the `--update-keys option`. The records will be updated based on the matching existing records.

```bash
php artisan model:import User public/Users.json --update-when-exists --update-keys=email
```

Note: The `--update-when-exists` option must be present in order for the update feature to be enabled.

## Relationships

In addition to importing models from JSON, this package also allows you to import relationships between models.
Currently supported relationship types are:

- HasOne
- HasMany
- HasOneThrough
- HasManyThrough
- MorphOne
- MorphMany
- MorphToMany
- MorphTo
- BelongsTo
- BelongsToMany

You can import models along with their specified relationships using the new
option `--with-relationships={relations}`. `{relations}` are the names of the relationships and can be separated by `+`
symbol if you want to attach more than one relationship.

For example, if you want to import a Category model along with its Product relationship, you can use the command:

```bash
php artisan model:import Category public/Categories.json --with-relationships=products
```

If you want to import a Category model along with both its Product and User relationships, you can use the command:

```bash
php artisan model:import Category public/Categories.json --with-relationships=products+user
```

Additionally, you can choose to only import specific columns of the relationship by using the
syntax `{relationship_name}:{columns_to_import}`.

For example, if you want to import a `Category` model along with its `Product` relationship and only import the `id`
and `name` columns of the `Product`, you can use the command:

```bash
php artisan model:import Category public/Categories.json --with-relationships=products:id,name
```

**Note:** In addition to the assignment that will be done in the above examples for importing a product to a category with relationships, Category will also be updated with the attributes found in the JSON.


---

## Conclusion

The Laravel Model JSON package is a useful tool for exporting data from a specific model in a JSON format. It offers various options to customize the export process and make it more convenient for your needs. Give it a try and see how it can help you in your project.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please e-mail vildanbina@gmail.com to report any security vulnerabilities instead of the issue tracker.

## Credits

- [Vildan Bina](https://github.com/vildanbina)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
