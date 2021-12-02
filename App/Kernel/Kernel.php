<?php

namespace App\Kernel;

use App\Helpers\Route;
use App\Model\Migration;

//Core
Class Kernel
{
    /**
     * App entry point
     */
    public static function run() {
        self::registerClassLoader();
        Migration::execute();
        Route::handle();

    }

    // Autoload app classes
    private static function registerClassLoader(){
        spl_autoload_register(function ($class){
            $classPath = explode('\\', $class);
            $className = $classPath[count($classPath) - 1];

            if (file_exists("App/Controllers/".$className.'.php')){
                include "App/Controllers/".$className.'.php';
            }
            if (file_exists("App/Helpers/".$className.'.php')){
                include "App/Helpers/".$className.'.php';
            }
            if (file_exists("App/Model/".$className.'.php')){
                include "App/Model/".$className.'.php';
            }


        });
    }

}