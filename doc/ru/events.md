#События#

Событийная модель построена на базе [Symfony/EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)

##Подключение обработчиков##

Для того, чтобы подключить класс (метод) в качестве обработчика какого-либо события,
его необходимо объявить в контейнере и пометить соответствующим тегом.

###Subscriber###

    services:
        system.kernel.subscriber:
            class: Module\System\Subscriber\KernelSubscriber
            calls:
                - [setContainer, ["@service_container"]]
            tags:
                - { name: event.subscriber }

###Listener###

    services:
        elfinder.static.listener:
            class: Module\Elfinder\Listener\StaticListener
            arguments: ["@assetic_service", "%root%"]
            tags:
                - { name: event.listener, event: static.install , method: installElFinder }
