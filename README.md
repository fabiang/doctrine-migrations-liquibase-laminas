# fabiang/doctrine-migrations-liquibase-zf

Zend Framework module for creating changesets for Liquibase with Doctrine.

## Installation

New to Composer? Read the [introduction](https://getcomposer.org/doc/00-intro.md#introduction). Run the following Composer command:

```console
$ composer require fabiang/doctrine-migrations-liquibase-zf
```

## Configuration

Load the module by adding it to `config/development.config.php`:

```php
return [
    'modules' => [
        /** order shouldn't matter here, but it 'DoctrineModule' should be loaded before **/
        'Fabiang\DoctrineMigrationsLiquibase',
    ],
];
```

If you don't have a recommended `development.config.php` you can also add it to `application.config.php`.
But you should not activate the module on production systems, as you simply to need it there.

## Licence

BSD-2-Clause. See the [LICENSE.md](LICENSE.md).
