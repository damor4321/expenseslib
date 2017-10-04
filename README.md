# ExpensesLib

This is a library for Apigility(http://apigility.org/), uses the following components:

- [rhumsaa/uuid](https://github.com/ramsey/uuid), a library for generating and validating UUIDs.
- [zfcampus/zf-configuration](https://github.com/zfcampus/zf-configuration), used for providing PHP
  files as one possible backend for reading/writing expenses annotations.
- [zendframework/zend-config](https://framework.zend.com/) for the actual configuration writer used
  by the `zf-configuration` module.
- [zendframework/zend-db](https://framework.zend.com/), used for providing a database table as a
  backend for reading/writing expenses annotations.
- [zendframework/zend-stdlib](https://framework.zend.com/), specifically the Hydrator subcomponent,
  for casting data from arrays to objects, and for the `ArrayUtils` class, which provides advanced
  array merging capabilities.
- [zendframework/zend-paginator](https://framework.zend.com/) for providing pagination.

It is written as a Zend Framework module, but could potentially be dropped into other
applications; use the `ExpensesLib\*Factory` classes to see how dependencies might be injected.

## Installation

Use [Composer](https://getcomposer.org/) to install the library in your application:

```console
$ composer require damor4321/expenseslib
```

If you are using this as part of a Zend Framework or Apigility application, you
may need to enable the module in your `config/application.config.php` file, if
you are not using the [zend-component-installer](https://docs.zendframework.com/zend-component-installer/):

```php
return [
    /* ... */
    'modules' => [
        /* ... */
        'ExpensesLib',
    ],
    /* ... */
];
```

## Configuration

When used as a Zend Framework module, you may define the following configuration values in order
to tell the library which adapter to use, and what options to pass to that adapter.

```php
[
    'expenseslib' => [
        'db' => 'Name of service providing DB adapter',
        'table' => 'Name of database table within db to use',
        'array_mapper_path' => 'path to PHP file returning an array for use with ArrayMapper',
    ],
    'service_manager' => [
        'aliases' => [
            // Set to either ExpensesLib\ArrayMapper or ExpensesLib\TableGatewayMapper
            \ExpensesLib\Mapper::class => \ExpensesLib\ArrayMapper::class,
        ],
    ],
]
```

For purposes of the Apigility examples, we suggest the following:

- Create a PHP file in your application's `data/` directory named `expenseslib.php` that returns an
  array:

  ```php
  <?php
  return [];
  ```

- Edit your application's `config/autoload/local.php` file to set the `array_mapper_path`
  configuration value to `data/expenseslib.php`:

  ```php
  <?php
  return [
      /* ... */
      'expenseslib' => [
        'array_mapper_path' => 'data/expenseslib.php',
      ],
  ];
  ```

The above will provide the minimum necessary requirements for experimenting with the library in
order to test an API.

## Using a database

The file `data/expenseslib.sqlite.sql` contains a [SQLite](https://www.sqlite.org/) schema. You can
create a SQLite database using:

```console
$ sqlite3 expenseslib.db < path/to/data/expenseslib.sqlite.sql
```

The schema can be either used directly by other databases, or easily modified to work with other
databases.


## ExpensesLib in a New Zend Framework  Project

1. Create a new Zend Framework project from scratch; we'll use `my-project` as our project folder:

  ```console
  $ composer create-project zendframework/skeleton-application my-project
  ```

2. Install the ExpensesLib module:

  ```console
  $ composer require damor4321/expenseslib
  ```

3. Build a DataSource

    - Option A: Array data source:

      First, copy the sample array to the `data` directory of thet application:

      ```console
      $ cp vendor/damor4321/expenseslib/data/sample-data/array-data.php data/expenses.data.php
      ```

      Then, configure this datasource by setting up a `local.php` configuration file:

      ```console
      $ cp config/autoload/local.php.dist config/autoload/local.php
      ```

      Next, add the ExpensesLib specific configuration for an array based data source:

      ```php
      'expenseslib' => [
         'array_mapper_path' => 'data/expenses.data.php',
      ],
      'service_manager' => [
          'aliases' => [
              \ExpensesLib\Mapper::class => \ExpensesLib\ArrayMapper::class,
          ],
      ],
      ```

    - Option B: Sqlite data source:

      First, create a sqlite3 database, and fill it with the sample data:

      ```console
      $ sqlite3 data/expenses.db < vendor/damor4321/expenseslib/data/expenseslib.sqlite.sql
      $ sqlite3 data/expenses.db < vendor/damor4321/expenseslib/data/sample-data/db-sqlite-insert.sql
      ```
  
      Then, configure this datasource by setting up a `local.php` configuration file:

      ```console
      $ cp config/autoload/local.php.dist config/autoload/local.php
      ```

      Next, add the ExpensesLib specific configuration for a sqlite database based data source:

      ```php
      'db' => [
          'adapters' => [
              'MyDb' => [
                  'driver' => 'pdo_sqlite',
                  'database' => __DIR__ . '/../../data/expenses.db'
              ],
          ],
      ],
      'expenseslib' => [
          'db' => 'MyDb',
          'table' => 'expenses',
      ],
      'service_manager' => [
          'aliases' => [
              \ExpensesLib\Mapper::class => \ExpensesLib\TableGatewayMapper::class,
          ],
      ],
      ```

4. Create a test script to prove the data source is working:

   ```php
   // test.php
   namespace ExpensesLib;

   use Zend\Mvc\Application;
   use Zend\Stdlib\ArrayUtils;

   include 'vendor/autoload.php';

   $appConfig = include 'config/application.config.php';

   if (file_exists('config/development.config.php')) {
       $appConfig = ArrayUtils::merge(
           $appConfig,
           include 'config/development.config.php'
       );
   }

   $app = Application::init($appConfig);
   $services = $app->getServiceManager();

   $expensesMapper = $services->get(Mapper::class);
   foreach ($expensesMapper->fetchAll() as $expenses) {
       printf(
           "[%d] [%s] %s (by %s)\n",
           $expenses->timestamp,
           $expenses->id,
           $expenses->date,
           $expenses->description,
           $expenses->comment,
           $expenses->amount,
       );
   }
   ```
