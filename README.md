[![Latest Stable Version](http://poser.pugx.org/vildanbina/laravel-model-json/v)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![Total Downloads](http://poser.pugx.org/vildanbina/laravel-model-json/downloads)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![Latest Unstable Version](http://poser.pugx.org/vildanbina/laravel-model-json/v/unstable)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![License](http://poser.pugx.org/vildanbina/laravel-model-json/license)](https://packagist.org/packages/vildanbina/laravel-model-json)
[![PHP Version Require](http://poser.pugx.org/vildanbina/laravel-model-json/require/php)](https://packagist.org/packages/vildanbina/laravel-model-json)

## Introduction

The Laravel Model JSON package allows you to export data from a specific model in the form of a JSON file. It is based on the `php artisan` command and is easy to use.

## Installation

To install this package, use the following command:

``` bash
composer require vildanbina/laravel-model-json
```

## Usage

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

This package also has several options that allow you to customize the export functionality. For example, you can use the `--path=public` option to save the JSON data in a different folder. Here's an example:

```bash
php artisan model:export User --path=public
```

\
By default, the filename of the JSON data is "Model-Timestamp", but you can also specify a custom filename using the `--filename=data` option. For example:

```bash
php artisan model:export User --filename=data
```

\
You can also exclude certain columns from the export by using the `--except-fields` option. This is useful if you only want to export certain data from the model. For example:

```bash
php artisan model:export User --except-fields=id,deleted_at
```

\
To exclude the `created_at`, `updated_at`, and `deleted_at` columns from the export, use the `--without-timestamps` option. For example:

```bash
php artisan model:export User --without-timestamps
```

\
If a model has a large number of columns and you only want to export a subset of them, you can use the `--only-fields` option. This allows you to specify which columns you want to include in the export. For example:

```bash
php artisan model:export User --only-fields=name,email
```

\
If you want to save JSON in a file as a beautified version, you can use the `--beautify` option or its shorthand `-b`. For example:

```bash
php artisan model:export User --beautify

#or

php artisan model:export User -b
```

By default, it will be exported to an inline JSON.

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
