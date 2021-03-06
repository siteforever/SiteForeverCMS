# Расширение Smarty #

##Автозагрузка своих плагинов##

Чтобы плагины, созданные по правилам Smarty загружались и регистрировались самостоятельно,
в директории вашего модуля нужно создать директорию `Widget`.
В эту директорию можно положить любые плагины, и они будут автоматически подхвачены системой.

##Регистрация метода объекта как плагина##

Это может быть неоходимо, когда нужно использовать в плагине возможности других сервисов.

Создаем класс с методом, который будет плагином:

    class FormPlugin
    {
        public function smarty_function_form(...)
        {
            ...
        }
    }

Добавляем класс в качестве сервиса в контейнер:

    services:
        smarty.plugin.form:
            class: FormPlugin

Отмечаем его тегом `smarty.plugin`:

    services:
        smarty.plugin.form:
            class: FormPlugin
            tags:
                - { name: smarty.plugin, type: block, plugin: form, method: smarty_block_form }

Для тега нужно обязательно указать параметры:

* type: тип плагина
* plugin: имя плагина (под ним можно будет вызывать из шаблонов)
* method: метод объекта

О написании своих плагинов можно почитать на сайте: http://www.smarty.net/docs/en/plugins.tpl
