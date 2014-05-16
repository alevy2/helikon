<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");

$dbh = db_connect($athomas2_dsn);

function logIn() {
    global $dbh;
    if(isset($_POST['loginusername'])) {
        $username = $_POST['loginusername'];
        if( loginCredentialsAreOkay($dbh,$username,$_POST['loginpassword']) ) {
          session_start();
            $_SESSION['username'] = $username;
            header('Location: home.php');
        }
        else {
            echo "<p>Sorry, that's incorrect.  Please try again\n";
        }
      }
}

function logOut() {
    session_destroy();
    header('Location: index.php');
}

function signIn(){
  global $dbh;
  if (isset($_REQUEST['username'])) {
    $name = $_REQUEST['name'];
    $username = $_REQUEST['username'];
    $email = $_REQUEST['email'];
    $sql = "select * from user where username = ?";
    $resultset = prepared_query($dbh,$sql,$username);
    $numRows = $resultset->numRows();
    if ($numRows == 1){
      echo "Sorry, that username has already been taken. Pleae choose another.";
      exit();
    }
    else if ($_REQUEST['password'] == $_REQUEST['password1']){
      $values = array($name,$username,$_REQUEST['password'],$email,);
      $sql = "insert into user (name,username,password,email) values (?,?,?,?)";
      $resultset = prepared_query($dbh,$sql,$values);
    }
    else {
      echo "Your passwords did not match. Please try again.";
      exit();
    }

  }

}

function checkLogInStatus() {
  session_start();
  if (isset($_SESSION['username'])){
    return true;
  }
  else {
    header('Location: index.php');
    exit();
  }

}

function loginCredentialsAreOkay($dbh,$username,$password) {
    $check = "SELECT count(*) AS n FROM user WHERE username=? AND password=?";
    $resultset = prepared_query($dbh, $check, array($username,$password));
    $row = $resultset->fetchRow();
    return( $row[0] == 1 );
}

function printLoggedInNavBar() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
<form method="post" action="$script">
  <input type="submit" value="logout">
</form>
EOT;
}

function printPageTop($title) {
    print <<<EOT
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>$title</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="raty-2.5.2/lib/jquery.raty.js"></script>
<script src="raty-2.5.2/lib/jquery.raty.min.js"></script>
</head>
<body>

EOT;
  }

function createNavBar($page) {

echo '<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
     <a class="brand" href="' . $page . '"><img src="logofinal.png"></a> 
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="media.php">Media</a></li>
      </ul>

      <form method="get" action="' . $page . '" class="navbar-nav navbar-form" role="search">
      <div class="form-group">
        <select class="form-control" name="tables">
        <option value="All">All</option>
        <option value="Users">Users</option>
        <option value="People">People</option>
        <option value="Movies">Movies</option>
      <option value="Albums">Albums</option>
      <option value="Songs">Songs</option>
      <option value="TVShows">TV Shows</option>
      <option value="Genres">Genres</option>
      </select>

          <input type="text" class="form-control" name="sought" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="user.php">My Profile</a></li>
            <li><a href="home.php?friends">Friends</a></li>
            <li><a href="adddata.php">Add to Database</a></li>
            <li class="divider"></li>
            <li><a href="' . $page . '?logout">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>';
}

function createActualRating($rating, $numReviews){
  echo "Current Rating: <div class='star' id='star1'></div>
  <script>$('#star1').raty({ score: " . $rating . ", readOnly: true});</script>
   (based on " . $numReviews . " reviews)<br>";
}

function createYourRating($mid, $yourRating, $uid){
  echo 'Your Rating: <div class="star" id="star2"></div>
  <script>
    $("#star2").raty({ score: ' . strval($yourRating) . ',
    click: function(score, evt) {
      data = "mid=' . $mid . '&uid=' . $uid . '&rating=" + score;
      $.ajax({
        type: "POST",
        url: "ratings.php",
        data: data,
        success: function(data){
          console.log(data);
          $("#currentRating").html(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
          alert(textStatus);
        }
      });
    }
    });
  </script>';
}

?>
