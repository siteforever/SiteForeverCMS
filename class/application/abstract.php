<?php
/**
 * Интерфейс приложения
 * User: keltanas
 */

abstract class Application_Abstract
{

    /**
     * Сигнализирует, как обрабатывать запрос
     * @var bool
     */
    static $ajax = false;

    /**
     * @var Config
     */
    static $config;
    /**
     * @var TPL_Driver
     */
    static $tpl;

    /**
     * Модель для работы с шаблонами из базы
     * Центролизовать необходимо для работы из виджета
     * @var model_Templates
     */
    static $templates;
    /**
     * @var router
     */
    static $router;
    /**
     * @var db
     */
    static $db;
    /**
     * @var Request
     */
    static $request;

    /**
     * @var model_Structure
     */
    static $structure;

    /**
     * @var Basket
     */
    static $basket;

    /**
     * @var model_User
     */
    static $user;

    /**
     * Время запуска
     * @var int
     */
    static $start_time = 0;

    abstract function run();

    abstract function init();

    abstract function handleRequest();

}
