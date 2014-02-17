##Справка по конфигам модулей##

###Captcha###

    captcha:
        width: 100
        height: 25
        color: 0x000000
        bgcolor: 0xFFFFFF

###Catalog###

    catalog:
        level: 0
        onPage: 10
        gallery_dir: '/files/catalog/gallery'
        order_default: name
        order_list:
            - name
        cache:
            type: null
            live_cycle: 3600

###Database###

    database:
        dsn: "mysql:host=%db_host%;port=%db_port%;dbname=%db_name%"
        login: "%db_login%"
        password: "%db_password%"
        migration: "%db_migration%"
        debug: "%debug%"
        options:
            1002: "SET NAMES utf8"

###Guestbook###

    guestbook:
        email: "guest_book_manager@example.com"

###Logger###

    logger:
        handlers:
            rotating_handler:
                type: rotating
                path: "%root%/runtime/logs/%env%.txt"
                max: 5
            fire_handler:
                type: firephp

###SwiftMailer###

    mailer:
        transport: %mailer_transport%
        username:  %mailer_username%
        password:  %mailer_password%
        spool: { type: memory } # Опционально для очереди в памяти
        spool: { type: file } # Опционально для очереди в файлах

Если тип спулинга не указан, то каждое сообщение будет отправлено по отдельности

Если организуется очередь на файлах, то ее неоходимо разгребать (например, по cron) командой

    $ php app/console mailer:spool:send

###Robokassa###

    robokassa:
        MrchLogin: "%robokassa_mrch_login%"
        MerchantPass1: "%robokassa_merchant_pass1%"
        MerchantPass2: "%robokassa_merchant_pass2%"

###System###

    system:
        editor: ckeditor
        session:
            storage: native
            handler: native

