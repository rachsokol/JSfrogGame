<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style>
.footer {
 
   text-align:center;
   bottom: 0;
   width: 100%;
   background-color: white;
   /*color: white;*/
}
canvas {
    border:1px solid #d3d3d3;
    background-image: url('images/forest2.jpg');
    
}
canvas {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);    
}
p {
    font-size:20px;
    text-align:center;
    margin-top:200px;
}
</style>
</head>
<body onload="startGame()">
    <p>Use the arrow keys and avoid the obstacles.</p>
<!--<div style='float: left; width: 300px;color: #f00; font-weight:bold;clear:both;' id='error'>&nbsp;</div>-->
<script> 
//initialize variables
var myGamePiece;
var myObstacles = [];
var myScore;
    
function startGame() {
  myGamePiece = new component(30, 30, "images/newfrog.svg", 10, 120, "image");
  myGamePieceReady = true;
  myScore = new component("30px", "Consolas", "white", 280, 40, "text");
  myGameArea.start();
}

//create game area
var myGameArea = {
  canvas : document.createElement("canvas"),
  start : function() {
    this.canvas.width = 600;
    this.canvas.height = 400;
    this.context = this.canvas.getContext("2d");
    document.body.insertBefore(this.canvas, document.body.childNodes[0]);
    this.frameNo = 0;
    this.interval = setInterval(updateGameArea, 20);
    window.addEventListener('keydown', function (e) {
      myGameArea.keys = (myGameArea.keys || []);
      myGameArea.keys[e.keyCode] = true;
    })
    window.addEventListener('keyup', function (e) {
      myGameArea.keys[e.keyCode] = false;
    })
        },
    clear : function() {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    },
    stop : function() {
        clearInterval(this.interval);
        
    }
    
}

function component(width, height, color, x, y, type) {
  this.type = type;
  this.score = 0;
  if (type == "image") {
    this.image = new Image();
    this.image.src = color;
  }
  this.width = width;
  this.height = height;
  this.x = x;
  this.y = y;
  this.speedX = 0;
  this.speedY = 0;
  this.gravity = 0.08
  this.gravitySpeed = 0;
  this.bounce = 0.9;
//update game area
  this.update = function() {
    ctx = myGameArea.context;
    if (this.type == "text") {
      ctx.font = this.width + " " + this.height;
      ctx.fillStyle = color;
      ctx.fillText(this.text, this.x, this.y);
    }

// update image
    if (type == "image") {
      ctx.drawImage(this.image,
        this.x,
        this.y,
        this.width, this.height);
    } else {
      ctx.fillStyle = color;
      ctx.fillRect(this.x, this.y, this.width, this.height);
    }
  }
//add gravity
  this.newPos = function() {
        this.gravitySpeed += this.gravity;
        this.x += this.speedX;
        this.y += this.speedY + this.gravitySpeed; 
        this.hitBottom();
        this.hitTop();
        this.hitLeft();
        this.hitRight();
    }
//add ceiling
this.hitTop = function() {
        var rockbottom = myGameArea.canvas.height - this.height;
        if (this.y < 0) {
            this.y = 0;
            this.gravitySpeed = -(this.gravitySpeed * this.bounce);
        }
   }
//add a floor
  this.hitBottom = function() {
        var rockbottom = myGameArea.canvas.height - this.height;
        if (this.y > rockbottom) {
            this.y = rockbottom;
            this.gravitySpeed = -(this.gravitySpeed * this.bounce);
        }
   }
//add left wall
   this.hitLeft = function() {
        var rockbottom = myGameArea.canvas.width - this.width;
        if (this.x < 0 ) {
            this.x = 0;
           
        }
   }
      this.hitRight = function() {
        var rockbottom = myGameArea.canvas.width - this.width;
        if (this.x > 600) {
            this.x = 600;
            
        }
   }
    
// crashing into obstacles ends game
   this.crashWith = function(otherobj) {
        var myleft = this.x;
        var myright = this.x + (this.width);
        var mytop = this.y;
        var mybottom = this.y + (this.height);
        var otherleft = otherobj.x;
        var otherright = otherobj.x + (otherobj.width);
        var othertop = otherobj.y;
        var otherbottom = otherobj.y + (otherobj.height);
        var crash = true;
        if ((mybottom < othertop) || (mytop > otherbottom) || (myright < otherleft) || (myleft > otherright)) {
            crash = false;
        }
        return crash;
    }
  
}
//update each component
function updateGameArea() {
    var x, height, gap, minHeight, maxHeight, minGap, maxGap;
    for (i = 0; i < myObstacles.length; i += 1) {
        if (myGamePiece.crashWith(myObstacles[i])) {
            alert("Game Over!");
            myGameArea.stop();
            location.reload();
            return;
        } 
    }
//update Game Area
    myGameArea.clear();
    myGameArea.frameNo += 1;
    if (myGameArea.frameNo == 1 || everyinterval(150)) {
        x = myGameArea.canvas.width;
        minHeight = 50;
        maxHeight = 200;
        height = Math.floor(Math.random()*(maxHeight-minHeight+1)+minHeight);
        minGap = 100;
        maxGap = 250;
        gap = Math.floor(Math.random()*(maxGap-minGap+1)+minGap);
        myObstacles.push(new component(30, height, "images/vine.svg", x, 0, "image"));
        myObstacles.push(new component(20, x - height - gap, "grey", x, height + gap));
        //(gamearea width,heigt)
        // myObstacles.push(new component(10, height, "green", x, 0));
        // myObstacles.push(new component(10, x - height - gap, "green", x, height + gap));
       
    }
    for (i = 0; i < myObstacles.length; i += 1) {
        myObstacles[i].x += -1;
        myObstacles[i].update();
    }
//update Score
    myScore.text="SCORE: " + myGameArea.frameNo;
    myScore.update();
//udpdate Game piece
    myGamePiece.speedX = 0;
    myGamePiece.speedY = 0;
    //left
    if (myGameArea.keys && myGameArea.keys [37]) {
        myGamePiece.speedX = -1; 
        
    }
    //right
    if (myGameArea.keys && myGameArea.keys [39]) {
        myGamePiece.speedX = 1; 
        
    }
    //up
    if (myGameArea.keys && myGameArea.keys [38]) {
        myGamePiece.speedY = -1; 
        
    }
    //down
    if (myGameArea.keys && myGameArea.keys [40]) {
        myGamePiece.speedY = 1; 
        
    }
    myGamePiece.newPos();
    myGamePiece.update();
}
function move(dir) {
    myGamePiece.image.src = "images/frog.svg";
    if (dir == "up") {myGamePiece.speedY = -1; }
    if (dir == "down") {myGamePiece.speedY = 1; }
    if (dir == "left") {myGamePiece.speedX = -1; }
    if (dir == "right") {myGamePiece.speedX = 1; }
}
function everyinterval(n) {
    if ((myGameArea.frameNo / n) % 1 == 0) {return true;}
    return false;
}


function clearmove() {
    myGamePiece.image.src = "images/frog.svg";
    myGamePiece.speedX = 0; 
    myGamePiece.speedY = 0; 
}
//source: https://www.w3schools.com/graphics/game_canvas.asp
</script>




</body>
</html>

