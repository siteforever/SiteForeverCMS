
* Config for App may be only relative path

    ```php
    $app = new App('app/cfg/development.php', true);
    ```

* Config for `db` must have `dsn` option
Default dsn: `mysql:host=localhost;dbname=siteforever`


* Routing is performed by using the Symfony/Router component
Routes for modules definind in Module::registerRoutes methods for each Module.
File app/routes.yml can contain custom routes.
see [official doucumentation](http://symfony.com/doc/current/components/routing/introduction.html)
