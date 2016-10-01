<?php
  namespace App;
/**
* объект с конфигурацией для приложения
*/
  class config extends \Framework\config{

      protected $options = [
        'dbHost' => '127.0.0.1',
        'dbUser' => 'dmitry',
        'dbPass' => 'xz2200',
        'dbName' => 'workproject',
        'dbCharset' => 'utf8',

        'templatePath'   => '/src/views',
        'mainLayoutName' => 'layout',

        'siteTitle' => 'База Студентов',

        'studentsPPage' => '20',

        'defaultController' => 'Students',
        'defaultAction'     => 'Hello'
      ];

  }
