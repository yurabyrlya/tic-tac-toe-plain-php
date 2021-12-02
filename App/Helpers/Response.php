<?php


namespace App\Helpers;


class Response
{
    public static function view(string $view, array $data){
        $viewPath = __DIR__ . '/../Views/'.$view.'.php';
        if (!file_exists($viewPath)) {
            var_dump('View ['. $view .'] not found');
            return;
        }
        // isset template variables
        $title = isset($data['title'])? $data['title']: '';
        $message = isset($data['message'])? $data['message']: '';
        $view = $viewPath;
        $data = isset($data['data'])? $data['data']: [];
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * @param string $url]
     */
    public static function redirect(string $url){
        header("Location: $url");
        die();
    }



}