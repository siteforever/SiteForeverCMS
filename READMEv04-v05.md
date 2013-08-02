
* Config for App may be only relative path

    ```php
    $app = new App('app/cfg/development.php', true);
    ```

* Config for `db` must have `dsn` option
Default dsn: `mysql:host=localhost;dbname=siteforever`
