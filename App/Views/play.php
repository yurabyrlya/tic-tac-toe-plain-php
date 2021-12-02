<style>
    body {
        /*padding: 15px;*/
        padding-top: 1em;
        font-family: sans-serif;
        color: #333;
    }

    #enemy-screen,
    #intro-screen {
        max-width: 420px;
        text-align: center;  /* Centers h2 and buttons */
    }
    #enemy-screen h2,
    #intro-screen h2 {
        padding: 0 15px;  /* Prevent text from touching the edges of the screen */
    }
    #intro-screen button {
        font-size: 3.4em;
        padding: 3% 6%;  /* With "%" padding is based on container's width */
        margin: 0 4%;  /* Create distance between buttons */
    }
    #enemy-screen button {
        display: block;
        margin: 0.6em auto;
        font-size: 1.9em;
        padding: 0.2em 0.4em;
    }

    /****** GAME GRID ******/


    table {
        max-width: 420px;
        max-height: 420px;
        width: 85vw;  /* 85% of the viewport (body) width */
        height: 85vw;
        border-spacing: 4px 2px;  /* Space between cells */
    }
    td {
        padding: 0;
        width: 33%;  /* Every cell should be 1/3 of table height (and width) */
        height: 33%;
    }
    .cell {
        font-size: 3.4em;
        font-size: 16vmin;  /* Responsive font size! Awesome! */
        height: 100%;  /* Fill the container (td) */
        width: 100%;
    }
    .wincell {
        background-color: #c88;  /* Red background to highlight the "winning" line */
    }
    .player-two {
        color: #933;
    }

    /****** BUTTONS ******/


    button {
        cursor: pointer;  /* Visually show that you can interact with the button */
        border: none;  /* Hides default border (and color, for some reason */
        color: #333;
    }

    #restart {
        font-size: 1.2em;
        padding: 5px 10px;
        /* Center horizontally */
        margin: 20px auto;
        display: block;
    }

    .choose,
    #restart {
        border-radius: 0.3em;
    }
    .choose:active,
    #restart:active {
        background-color: #ccc;  /* Darker color when the button is pressed */
    }

    .btn-green {
        background-color: #70d260;
    }
    .btn-green:active {
        /* Override the active background for #restart */
        background-color: #60c050 !important;
    }

    /******  HELPER CLASSES  ******/

    .center {
        margin: 0 auto;
    }
    .hidden {
        display: none;
    }
    .cannotuse {
        cursor: not-allowed;
    }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="application/javascript">
    var win;  // TRUE if somebody won the game
    var turn; // Number of the current turn
    var row, column;  // Will contain "coordinates"for a specific cell
    var sellectedPoss = {
        playerPos : '',
        cpuPos : ''
    }
    const urlParams = new URLSearchParams(window.location.search);
    var playerId = urlParams.get('player_id');
    var playerSymbol = "X";
    var enemySymbol = "O";
    var cpuEnabled = true;
    $(document).ready(function() {
        startGame();
        listFilledPositions();
        // Game screen buttons
        $("#restart").on("click", function() {
            $.post( "game/restart", {  player_id: playerId})
                .done(function( response ) {
                    restartGame();
                }).fail(function (e){
                    console.log(e);
                    alert('Fail to restart game');
            });
        });
        $(".cell").on("click", function() {
            // If nobody has won yet and clicked cell is empty
            if(!win && this.innerHTML === "") {
                var playerTurn = turn%2 === 0;
                if(playerTurn) {
                    insertSymbol(this, playerSymbol);
                }
                else {
                    insertSymbol(this, enemySymbol);
                }
            }
        });
    });


    /******  FUNCTIONS  ******/


    function postPosition (element){
        console.log(playerId);
        $.post( "game/post", {  player_id: playerId, selected: sellectedPoss })
            .done(function( response ) {
                $('#game-screen').innerHTML = response;
            });
    }

    // list filled positions
    function listFilledPositions (){
            $.get("/game/positions", {  player_id: playerId})
                .done(function( positions ) {
                     for (let key in positions){
                         var pos =  positions[key];
                         if (pos.cp_x != null && pos.cp_y != null ) {
                             cpuCellId = '#cell'+ pos.cp_x.toString() + pos.cp_y.toString();
                             insertSymbol($(cpuCellId).get(0), enemySymbol, true);
                         }
                         if (pos.pl_x != null && pos.pl_y != null ) {
                              playerCellId = '#cell' + pos.pl_x.toString() + pos.pl_y.toString();
                             insertSymbol($(playerCellId).get(0), playerSymbol, true);
                         }




                     }
                   // $('#game-screen').innerHTML = response;
                });
    }
// Inserts a symbol in the clicked cell
    function insertSymbol(element, symbol, fill = false) {
        console.log(element.id);
        element.innerHTML = symbol;
        if(symbol === enemySymbol)
            $("#" + element.id).addClass("player-two"); // Color enemy symbol differently
        $("#" + element.id).addClass("cannotuse");  // Show a "disabled" cursor on already occupied cells

        checkWinConditions(element);
        turn++;
        // Game end - If somebody has won or all cells are filled
        if(win || turn > 8) {
            console.log(win);
            console.log(element.innerHTML);

            $("#restart").addClass("btn-green");  // Highlights "restart" button
            $(".cell").addClass("cannotuse");  // Tells visually you can't interact anymore with the game grid
            if (!fill){
                if (win && element.innerHTML == playerSymbol){
                    sellectedPoss.cpuPos = null;
                    sellectedPoss.playerPos = element.id
                }
                if (win && element.innerHTML == enemySymbol){
                    sellectedPoss.playerPos = null;
                    sellectedPoss.cpuPos = element.id
                }

                postPosition()

            }
        }
        else if(cpuEnabled && turn%2 !== 0) {
            if (!fill){
                sellectedPoss.playerPos = element.id
                cpuTurn();
            }
        }


    }

    function startGame() {
        restartGame();
    }
    function showGameScreen() {
        $("#game-screen").fadeIn(300);
    }
    function showEnemyScreen() {
        $("#enemy-screen").fadeIn(300);
    }

    /* Sets everything to its default value */
    function restartGame() {

        turn = 0;
        win = false;
        $(".cell").text("");
        $(".cell").removeClass("wincell");
        $(".cell").removeClass("cannotuse");
        $(".cell").removeClass("player-two");
        $("#restart").removeClass("btn-green");
    }

    /* Check if there's a winning combination in the grid (3 equal symbols in a row/column/diagonal) */
    function checkWinConditions(element) {
        // Retrieve cell coordinates from clicked button id
        row = element.id[4];
        column = element.id[5];

        // 1) VERTICAL (check if all the symbols in clicked cell's column are the same)

        win = true;
        for(var i=0; i<3; i++) {
            if($("#cell" + i + column).text() !== element.innerHTML) {
                win = false;
            }
        }
        if(win) {
            for(var i=0; i<3; i++) {
                // Highlight the cells that form a winning combination
                $("#cell" + i + column).addClass("wincell");
            }
            return; // Exit from the function, to prevent "win" to be set to false by other checks
        }

        // 2) HORIZONTAL (check the clicked cell's row)

        win = true;
        for(var i=0; i<3; i++) {
            if($("#cell" + row + i).text() !== element.innerHTML) {
                win = false;
            }
        }
        if(win) {
            for(var i=0; i<3; i++) {
                $("#cell" + row + i).addClass("wincell");
            }
            return;
        }

        // 3) MAIN DIAGONAL (for the sake of simplicity it checks even if the clicked cell is not in the main diagonal)

        win = true;
        for(var i=0; i<3; i++) {
            if($("#cell" + i + i).text() !== element.innerHTML) {
                win = false;
            }
        }
        if(win) {
            for(var i=0; i<3; i++) {
                $("#cell" + i + i).addClass("wincell");
            }
            return;
        }

        // 3) SECONDARY DIAGONAL

        win = false;
        if($("#cell02").text() === element.innerHTML) {
            if($("#cell11").text() === element.innerHTML) {
                if($("#cell20").text() === element.innerHTML) {
                    win = true;
                    $("#cell02").addClass("wincell");
                    $("#cell11").addClass("wincell");
                    $("#cell20").addClass("wincell");
                }
            }
        }
    }

    // Simple AI (clicks a random empty cell)
    function cpuTurn() {
        var ok = false;

        while(!ok) {
            row = Math.floor(Math.random() * 3);
            column = Math.floor(Math.random() * 3);
            if( $("#cell"+row+column).text() === "" ) {
                // We have found it! Stop looking for an empty cell
                sellectedPoss.cpuPos = "cell"+row+column
                console.log(sellectedPoss)
                postPosition()
                ok = true;
            }
        }

        $("#cell"+row+column).click(); // Emulate a click on the cell
    }
</script>
<main>
    <div class="container py-4">
        <header class="pb-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                    <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                <span class="fs-4">Home</span>
            </a>
        </header>

        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">

            <?php if (!$data['name']): ?>
                <form action="/" method="GET">
                    <div class="mb-3">
                        <label for="name"  class="form-label h5">Enter your name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Name">
                    </div>
                    <button class="btn btn-primary btn-lg" type="submit">Play</button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <span class="h4"> Player: <?php echo $data['name'] ?> </span>
                </div>

                <div id="intro-screen" class="center">
                    <h5> You : X</h5>
                    <h5> CPU : O</h5>
                </div>

                <div id="game-screen" class="center">
                    <!-- Every cell has an id "cell" followed by cell row and cell column -->
                        <table class="center">
                        <tr>
                            <td>
                                <button type="button" class="cell" id="cell00"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell01"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell02"></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button" class="cell" id="cell10"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell11"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell12"></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button" class="cell" id="cell20"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell21"></button>
                            </td>
                            <td>
                                <button type="button" class="cell" id="cell22"></button>
                            </td>
                        </tr>
                    </table>
                    <button type="button" id="restart">Restart</button>
                    <div>
            <?php endif; ?>
            </div>
        </div>

        <footer class="pt-3 mt-4 text-muted border-top">
            &copy; 2022
        </footer>
    </div>
</main>
