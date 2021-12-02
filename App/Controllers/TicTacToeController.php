<?php

namespace  App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Model\DB;
use App\Model\Migration;


class TicTacToeController implements ControllerInterface
{

    public function index()
    {
        $request = new Request();
        $requestData = $request->all();
        $responseData = [];

        $responseData['title'] = 'Tic Tac Toe';
        $responseData['message'] = 'Tic Tac Toe';

        $db = Migration::db();
        $players = $db->run("SELECT * FROM player")->fetchAll();
        $responseData['data']['players'] = $players;

        if (!isset($requestData['name'])) {
           $responseData['data']['name'] = '';
           return Response::view('home', $responseData);
        }
        if (!$requestData['name']) {
           return Response::view('home', $responseData);
        }

        if (isset($requestData['id'])) {
            $id = $requestData['id'];
            $player = $db->run("SELECT * FROM player WHERE id = $id")->fetch();
        } else {
            $playerName = $requestData['name'];
            $db->run("INSERT INTO player (`name`) VALUES ('$playerName')");
            $player = $db->run("SELECT * FROM player ORDER BY id DESC LIMIT 1")->fetch();
        }

        Response::redirect(sprintf('/game?name=%s&player_id=%s', $player['name'], $player['id']));

    }

    /**
     * Post selected position
     */
    public function store(){
        $request = new Request();
        $requestData = $request->all();
        $playerId = $requestData['player_id'];

        $selectedPlayerCell = $requestData['selected']['playerPos'];
        $px = null;
        $py = null;
        if ($selectedPlayerCell){
            $selectedPlayerCell = explode('cell', $selectedPlayerCell);
            $selectedPlayerCell = $selectedPlayerCell[1];
            $px = $selectedPlayerCell[0];
            $py = $selectedPlayerCell[1];
        }


        $selectedCpuCell = $requestData['selected']['cpuPos'];
        $cx = null;
        $cy = null;
        if ($selectedCpuCell) {
            $selectedCpuCell = explode('cell', $selectedCpuCell);
            $selectedCpuCell = $selectedCpuCell[1];
            $cx = $selectedCpuCell[0];
            $cy = $selectedCpuCell[1];
        }

        $db = Migration::db();
        /// not secure SQL, review
        $db->run("
                INSERT INTO game 
                    (player_id, pl_x, pl_y, cp_x, cp_y ) 
                    VALUES (?, ?, ?, ?, ?)
                    ", [$playerId, $px, $py, $cx, $cy]);
    }

    /**
     * @param Request $request
     */
    public function startGame(){
        $request = new Request();
        $requestData = $request->all();

        $responseData = [];
        $responseData['title'] = 'Tic Tac Toe';
        $responseData['message'] = 'Play game';
        $responseData['data'] = $requestData;

        return Response::view('play', $responseData);
    }

    public function show(){
        $request = new Request();
        $requestData = $request->all();
        $playerId = $requestData['player_id'];

        $db = Migration::db();
        $result = $db->run("SELECT * FROM game WHERE player_id  = $playerId")->fetchAll();

        header('Content-type: application/json');
        echo json_encode($result);
    }

    // restart game
    public function restart(){
       // return
        $request = new Request();
        $requestData = $request->all();
        Migration::db()
            ->run("DELETE  FROM game WHERE player_id  = ?", [$requestData['player_id']]);
    }
}