# PHP test task

## Calculate commission fees

### Features

- PHP 7
- Composer
- PSR-4 autoloading
- PSR-1 and PSR-2 compatible code
- PHPUnit
- Configuration using YAML

### Using

* Prepare:
    ```
    composer install
    ```

* Check the configuration file `config.yml`

* Try to run:
    ```
    composer run-script task
    ```

    you can give your own csv:
    ```
    php index.php <your_file.csv>
    ```

### Unit tests

```
composer run-script test
```

### Notes

The following tools and environment were used:
* Mac OS X 10.10.5
* PHP 7.2 (brew)
* PhpStorm 2018.1
* PHP_CodeSniffer
* Tested under Linux Ubuntu 14.04
