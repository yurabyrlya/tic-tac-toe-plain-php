
<main>
    <div class="container py-4">
        <header class="pb-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                    <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                <span class="fs-4"><?php echo $message ?></span>
            </a>
        </header>

        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <form action="/" method="GET">
                    <div class="mb-3">
                        <label for="name"  class="form-label h5">Enter your name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Name">
                    </div>
                    <button class="btn btn-primary btn-lg" type="submit">New Game</button>
                </form>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">name</th>
                    <th scope="col">Game</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['players'] as $player): ?>
                    <tr>
                        <th scope="row"> <?php  echo $player['id']?></th>
                        <td><?php  echo $player['name']?></td>
                        <td><a href="/game?player_id=<?php echo $player['id']?>&name=<?php echo $player['name']?>"> Open </a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <footer class="pt-3 mt-4 text-muted border-top">
            &copy; 2022
        </footer>
    </div>
</main>
