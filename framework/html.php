<?php
/**
* Полностью статичный класс для безопасной работы отрисовкой
* данных клиенту и подключением файлов шаблона
*/
    namespace Framework;

    class html{
        /**
        * Рендеринг файла шаблона
        *
        * @param string $fileName имя файла шаблона
        * @param mixed $data данные которые будут переданы в шаблон
        * @param bool $skipSecurity не производить обработку данных для безопасности
        */
        public static function render($fileName, $data=null, $skipSecurity=null){
            $file = self::getTemplatePath()
                    .$fileName
                    .'.phtml';
            if( ! is_file($file)){
                throw new \Framework\Exception( \Framework\Exception::TEMPLATE_FILE_NOT_FOUND );
            }

            if( ! empty($data)){
                extract($skipSecurity ? $data : self::security($data) );
            }

            ob_start();
            try{
                include($file);
                return ob_get_clean();
            }catch(\Exception $e){
                ob_get_clean();
                throw $e;
            }




        }

        /**
        * Получить глобальный путь к шаблонам
        * @return string
        */
        private static function getTemplatePath(){
            return $path = __ROOT__
                           .\App\App::$config->html_templatepath()
                           .'/';
        }

        /**
        * Подготовка данных у выводу.
        * Рекурсивно обрабатывает все строки с помощью
        * htmlspecialchars
        * @param mixed $data
        */
        public static function security($data){
            if(is_object($data) || is_resource($data)){
                return $data;
            }elseif(is_array($data)){
                foreach ($data as $key => $value) {
                    $data[$key] = self::security($value);
                }
                return $data;
            }else{
                return htmlspecialchars($data, ENT_QUOTES);
            }

        }

        /**
        * Рендеринг файла шаблона внутри главного шаблона
        *
        * @string mixed $fileName - файл шаблона
        * @param mixed $data - данные
        * @param bool $skipSecurity не обрабатывать безопасность
        */
        public static function renderLayout($fileName=null, $data=null, $skipSecurity=null){
            $subTemplateHtml = null;
            if( ! empty($fileName)){
                $subTemplateHtml = self::render($fileName, $data, $skipSecurity);
            }
            return self::render(\App\App::$config->html_mainlayoutname(),
                                    ['subTemplateHtml' => $subTemplateHtml,
                                    'siteTitle' => \App\App::$config->html_sitetitle()],
                                true    );
        }
    }
