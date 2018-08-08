# fabiang/doctrine-migrations-liquibase-zf

Zend Framework module for creating changesets for Liquibase with Doctrine.

[![Latest Stable Version](https://poser.pugx.org/fabiang/doctrine-migrations-liquibase-zf/version)](https://packagist.org/packages/fabiang/doctrine-migrations-liquibase-zf)
[![License](https://poser.pugx.org/fabiang/doctrine-migrations-liquibase-zf/license)](https://packagist.org/packages/fabiang/doctrine-migrations-liquibase-zf)

## Installation

New to Composer? Read the [introduction](https://getcomposer.org/doc/00-intro.md#introduction). Run the following Composer command:

```console
$ composer require --dev fabiang/doctrine-migrations-liquibase-zf=@dev
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
But you should not activate the module on production systems, as you don't need it there.

## Usage

You should see two new command for `doctrine-module` when you execute the following command in your project:

    ./vendor/bin/doctrine-module list

* orm:liquibase:createchangelog
* orm:liquibase:creatediff

First creates the whole changelog XML file, second command creates just the diff.

## Licence

BSD-2-Clause. See the [LICENSE.md](LICENSE.md).
