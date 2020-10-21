<?php
declare(strict_types=1);
namespace Autoload;


/** 自动加载类 */
class Autoload
{
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'loader']);
    }


    /**
     * 自定义自动加载函数
     * @param string $name
     * @return void
     * @author chenlin
     * @date 2020/10/16
     */
    public static  function loader(string $name)
    {
        //var_dump($name);
        $file = ROOT_PATH.'/'. str_replace('\\','/', $name).'.php';
        //var_dump($file);
        if (file_exists($file)) {
            include $file;
        }
    }
}