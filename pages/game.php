<?php
session_start();
if (isset($_POST['logout']) ||
    !isset($_SESSION['username'])) {
    // Eliminar sesión y redirigir al inicio
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>

<!doctype html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Pixel-Run demo</title>

        <script src="//cdn.jsdelivr.net/npm/phaser@3.11.0/dist/phaser.js"></script>
        <link rel="stylesheet" href="../styles/game.css"/>

</head>
<body>
    <div class="container">
        <h1>Welcome to the Game</h1>
        <p class="welcome">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p class="game-info">Enjoy playing the game</p>
        <form method="POST" action="" >
            <button type="submit" name="logout">Log Out</button>
        </form>
    </div>

        <script type="text/javascript">

            var config = {
                type: Phaser.AUTO,
                width: 800,
                height: 600,
                physics: {
                    default: 'arcade',
                    arcade: {
                        gravity: { y: 300 },
                        debug: false
                    }
                },
                scene: {
                    preload: preload,
                    create: create,
                    update: update
                }
            };

            var player;
            var platforms;
            var cursors;
            var stars;
            var bombs;
            var score = 0;
            var scoreText;
            let prevDirection = 'left';

            var game = new Phaser.Game(config);

            function preload ()
            {
                this.load.image('sky', '../assets/sky.png');
                this.load.image('ground', '../assets/platform.png');
                this.load.image('star', '../assets/star.png');
                this.load.image('bomb', '../assets/bomb.png');
                this.load.spritesheet('owl', '../assets/OwlSprites.png', { frameWidth: 32, frameHeight: 32 });
            }

            function create ()
            {
                this.add.image(400, 300, 'sky');

                platforms = this.physics.add.staticGroup();

                platforms.create(400, 568, 'ground').setScale(2).refreshBody();

                platforms.create(600, 400, 'ground');
                platforms.create(50, 250, 'ground');
                platforms.create(750, 220, 'ground');

                player = this.physics.add.sprite(100, 450, 'owl');

                player.setBounce(0.2);
                player.setCollideWorldBounds(true);
                //run left
                this.anims.create({
                    key: 'left',
                    frames: this.anims.generateFrameNumbers('owl', { start: 27, end: 30 }),
                    frameRate: 10,
                    repeat: -1
                });
                //idle
                this.anims.create({
                    key: 'idle-left',
                    frames: this.anims.generateFrameNumbers('owl', { start: 31, end: 36 }),
                    frameRate: 10,
                    repeat: -1
                });
                this.anims.create({
                    key: 'idle-right',
                    frames: this.anims.generateFrameNumbers('owl', { start: 37, end: 42 }),
                    frameRate: 10,
                    repeat: -1
                });
                //run right
                this.anims.create({
                    key: 'right',
                    frames: this.anims.generateFrameNumbers('owl', { start: 43, end: 46 }),
                    frameRate: 10,
                    repeat: -1
                });
                //die
                this.anims.create({
                    key: 'die-left',
                    frames: this.anims.generateFrameNumbers('owl', { start: 0, end: 26 }),
                    frameRate: 10,
                    repeat: 0
                });
                this.anims.create({
                    key: 'die-right',
                    frames: this.anims.generateFrameNumbers('owl', { start: 47, end: 72 }),
                    frameRate: 10,
                    repeat: 0
                });

                cursors = this.input.keyboard.createCursorKeys();

                this.physics.add.collider(player, platforms);

                stars = this.physics.add.group({
                    key: 'star',
                    repeat: 11,
                    setXY: {x:12, y: 0, stepX: 70}
                });

                stars.children.iterate(function(child){
                    child.setBounceY(Phaser.Math.FloatBetween(0.4, 0.8));
                });
                this.physics.add.collider(stars, platforms);
                this.physics.add.overlap(player, stars, collectStar, null, this);
                scoreText = this.add.text(16, -180, 'score: 0', {fontSize: '32px', fill: '#FFF'});
                bombs = this.physics.add.group();
                this.physics.add.collider(bombs, platforms);
                this.physics.add.collider(player, bombs, hitBomb, null, this);
            }

            function update ()
            {
                if (cursors.left.isDown)
                {
                    player.setVelocityX(-160);

                    player.anims.play('left', true);
                    prevDirection = 'left';
                }
                else if (cursors.right.isDown)
                {
                    player.setVelocityX(160);

                    player.anims.play('right', true);
                    prevDirection = 'right';
                }
                else
                {
                    player.setVelocityX(0);
                    if(prevDirection === 'left'){
                        player.anims.play('idle-left');
                    } else {
                        player.anims.play('idle-right');
                    }
                    
                }
                //jump
                if (cursors.up.isDown && player.body.touching.down)
                {
                   
                    player.setVelocityY(-330);
                }
            }

            function collectStar(player, star){
                showGameOverScreen.call(this, score);
                star.disableBody(true, true);
                score += 10;
                scoreText.setText('Score: ' + score);
                if(stars.countActive(true)===0){
                    stars.children.iterate(function (child){
                        child.enableBody(true, child.x, 0, true, true);
                    });
                    var x = (player.x < 400 ? Phaser.Math.Between(400, 800) : Phaser.Math.Between(0, 400));
                    var bomb = bombs.create(x, 16, 'bomb');
                    bomb.setBounce(1);
                    bomb.setCollideWorldBounds(true);
                    bomb.setVelocity(Phaser.Math.Between(-200, 200), 20);
                }
            }
            function hitBomb(player, bomb) {
                this.physics.pause(); 
                player.setTint(0xff0000); 
                if(prevDirection === 'left'){
                    player.anims.play('die-left');
                } else {
                    player.anims.play('die-right');
                }
                
                showGameOverScreen.call(this, score);
                gameOver = true; 
                sendScore(score);
            }

            function showGameOverScreen(score) {
                
                let gameOverBackground = this.add.graphics();
                gameOverBackground.fillStyle(0x000000, 0.75); 
                gameOverBackground.fillRect(0, 0, this.cameras.main.width, this.cameras.main.height);

               
                let gameOverText = this.add.text(400, 200, 'Game Over', {
                    fontSize: '64px',
                    fill: '#fff'
                }).setOrigin(0.5);

                
                let scoreText = this.add.text(400, 330, 'Score: ' + score, {
                    fontSize: '32px',
                    fill: '#fff'
                }).setOrigin(0.5);

                
                let restartButton = this.add.text(400, 400, 'Restart', {
                    fontSize: '32px',
                    fill: '#fff',
                    padding: { left: 20, right: 20, top: 10, bottom: 10 }, 
                    width: '100px',
                    height: '100px'
                }).setOrigin(0.5);
                restartButton.setInteractive();

                restartButton.on('pointerdown', function() {
                    this.scene.restart(); 
                    gameOverBackground.destroy(); 
                    gameOverText.destroy(); 
                    scoreText.destroy(); 
                    restartButton.destroy(); 
                }, this);
            }

            function sendScore(score) {
           
                fetch('../backend/insertScore.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ score: score }), 
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            // function handleFormSubmit(event) {
            //     event.preventDefault(); 
            //     sendScore(score)   
            //     event.target.submit(); 
            // }
        </script>
</body>
</html>


