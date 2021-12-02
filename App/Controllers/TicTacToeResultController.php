<?php

namespace  App\Controllers;

use App\Helpers\Response;

class TicTacToeResultController implements ControllerInterface
{

    public function index()
    {
        return Response::view('play', [
            'title'=> 'Tic Tac Toe',
            'message' => 'Play game',
            'data' => [
            ]
        ]);
    }
}