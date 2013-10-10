
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


Нужно создать каталог `./app` в котором должны содержаться следующие файлы:
* console - скрипт запуска консольных команд
* cfg/console.php - конфиг для запуска из консоли
* cfg/base.php - базовый конфиг приложения
* modules.php - список подключенных модулей
* parameters.yml - генерируется через инсталяцию композера

* В каталоге `./runtime` нужно создать доступный на запись каталог `./logs`

* Новый файл index.php взять из репозитория keltanas/site-forever-cms
* В каталоге `./themes/name/templates` новый файл `theme.xml` с метаописанием темы

* event-orient architecture for market.order module
* new method `Order->isSale()` indicated about enabled discount
* add controller object to container before dispatch
* `Sfcms\Delivery` renamed to `Sfcms\DeliveryManager`
* New global js events `sfcms.form.beforeSubmit` and `sfcms.form.error` on ajax form
