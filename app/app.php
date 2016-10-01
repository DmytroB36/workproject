<?php
namespace App;

/**
* Класс приложения
* содержит объекты для индивидуализации приложения
*/
class App{
    /** @var \App\config */
    public static $config;

    /** @var Framework\Registry */
    public static $registry;

    /** @var Framework\Message */
    public static $message;
}
