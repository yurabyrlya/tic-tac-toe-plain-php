<?php

namespace App\Helpers;
use App\Controllers\TicTacToeController;
use App\Controllers\TicTacToeResultController;

class Route
{

    public static function handle(){
        self::handleController();
    }

    /**
     *
     */
    private static function handleController()
    {
        $request = $_SERVER['REQUEST_URI'];
        $request = explode('?',$request)[0];
        switch ($request) {
            case '':
            case '/' :
                $controller = new TicTacToeController();
                $controller->index();
                return;
            case '/game' :
                $controller = new TicTacToeController();
                $controller->startGame();
                return;
            // post position
            case '/game/post' :
                $controller = new TicTacToeController();
                $controller->store();
                return;
            //list filed positions
            case '/game/positions' :
                $controller = new TicTacToeController();
                $controller->show();
                return;
            case '/game/restart' :
                $controller = new TicTacToeController();
                $controller->restart();
                return;
            case '/played/results' :
                $controller = new TicTacToeResultController();
                $controller->index();
                return;
            default:
                // by default page not found
                http_response_code(404);
                Response::view('404', [
                    'title' => 'Page not found',
                    'message' => 'Page not found, please go to home page to play'
                    ]);

        }

    }

}