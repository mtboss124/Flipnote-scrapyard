<?php
$username = $_GET['username']; // Get the username from the URL query string

class Database {


  private $newest;


  private $creator;


  private $authors;


  private $starRatings;


  public function __construct() {


      $this->newest = [];


      $this->creator = [];


      $this->authors = [];


      $this->starRatings = [];


      // Load database entries from the files


      $this->loadDatabase();


  }


  private function loadDatabase() {


      $newestFilename = "database/new_flipnotes.dat"; // Path to your new_flipnotes.dat file


      $authorsFilename = "database/authors.dat"; // Path to your authors.dat file



      if (file_exists($newestFilename)) {


          $file = file_get_contents($newestFilename);


          if ($file) {


              $this->newest = array_map(function($line) {


                  return explode("\t", trim($line));


              }, explode("\n", $file));


          }


      }



      if (file_exists($authorsFilename)) {


          $file = file_get_contents($authorsFilename);


          if ($file) {


              $this->authors = array_map(function($line) {


                  return explode("\t", trim($line));


              }, explode("\n", $file));


          }


      }


      // Load star ratings from flipnote.dat files

      $this->loadStarRatings();


  }


  private function loadStarRatings() {
#current format = [filename, views, stars, green stars, red stars, blue stars, purple stars, Channel, Downloads]
    foreach ($this->newest as &$entry) {

        $creatorId = $entry[0];

        $flipnoteFilename = "database/creators/$creatorId/flipnotes.dat";

        if (file_exists($flipnoteFilename)) {

            $file = file_get_contents($flipnoteFilename);

            if ($file) {

                $flipnotes = array_map(function($line) {

                    return explode("\t", trim($line));

                }, explode("\n", $file));


                // Update the $entry with the star rating

                foreach ($flipnotes as $flipnote) {

                    if ($flipnote[0] == $entry[1]) {

                        $entry[2] = $flipnote[2]; // assuming the star rating is the 3rd column

                        break;

                    }

                }

            }

        }

    }

  }


  public function getNewest() {


      return $this->newest;


  }


  public function getAuthors() {


      return $this->authors;


  }


  // Additional methods can be added here...


}


// Display script


$db = new Database();


$newestEntries = $db->getNewest();


$authors = $db->getAuthors();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>  <?= htmlspecialchars($username) ?> profile</title>
  <link rel="stylesheet" href="./css/index.css"> 
  <!--SUPER IMPORTANT SCRIPT PUT ON EVERY SINGLE PAGE PLEASE-->
  <script src="https://cdn.jsdelivr.net/npm/flipnote.js@5/dist/flipnote.webcomponent.min.js"></script>
</head>
<body>
  <!--style background stuff ignore (mt note if you touch this i am going to fucking kill you :3 )-->
  <div class="bggradient" style="animation: slide 19s linear infinite; z-index: -5;"></div>
  <div class="citybg4" style="animation: slide 15s linear infinite; z-index: -4;"></div>
  <div class="citybg3" style="animation: slide 13s linear infinite; z-index: -3;"></div>
  <div class="citybg2" style="animation: slide 10s linear infinite; z-index: -2;"></div>
  <div class="citybg1" style="animation: slide 8s linear infinite; z-index: -1;"></div>
<!--end of funny art stuff-->
  <div class="aspect-ratio">
    <!-- Load an icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <div class="navbar">
      <div class="nav-left">
        <a class="active" href="Index.php"><img src="img/scrapyard.png" alt="Logo"></a>
      </div>
      <div class="nav-right">
        <a href="#"><i class="fa fa-fw fa-search"></i> News</a>
        <a href="#"><i class="fa fa-fw fa-envelope"></i> Rules</a>
        <a href=""><i class="fa fa-fw fa-user"></i> Credits</a>
      </div>
    </div>
  <!--LEFT FLIPNOTE GRID-->
    <div class="container">
      <div class="left">
<!--profile-->

<div class="pfp" style="width: 436px; margin-bottom: 50px;">

    <div class="coso" style="border-radius: 8px 8px 0px 0px; color: #8ac4c3; width: 436px;height:12px;" width="436" height="12">  </div>

    <div class="main-content-pfp">

        <div class="icon" width="50" style="float:left;">

            <img src="database/profilepictures/<?=htmlspecialchars($username)?>.ntft" style="  outline-style: solid;outline-color: #64878c;outline-width: 3px;" width="48" height="48"></div>

        <p style="margin-left:15px; margin-top:0px;float:left;"> <?= htmlspecialchars($username) ?></p>

        <br>

        <p style="margin-left:60px;text-decoration: underline;">Stars received</p>

        <?php
    $filteredEntries = array_filter($newestEntries, function($entry) use ($username, $authors) {
      foreach ($authors as $author) {
          if ($author[0] == $entry[1] && $author[1] == $username) {
              return true;
          }
      }
      return false;
  });
            $totalStars = array(0, 0, 0, 0, 0); // Initialize an array to store the total stars for each color

            foreach ($filteredEntries as $entry) {

                $flipnoteFilename = "database/creators/$entry[0]/flipnotes.dat";

                if (file_exists($flipnoteFilename)) {

                    $file = file_get_contents($flipnoteFilename);

                    if ($file) {

                        $flipnotes = array_map(function($line) {

                            return explode("\t", trim($line));

                        }, explode("\n", $file));

                        foreach ($flipnotes as $flipnote) {

                            if ($flipnote[0] == $entry[1]) {

                                $totalStars[0] += (int)$flipnote[2]; // Total stars

                                $totalStars[1] += (int)$flipnote[3]; // Green stars

                                $totalStars[2] += (int)$flipnote[4]; // Red stars

                                $totalStars[3] += (int)$flipnote[5]; // Blue stars

                                $totalStars[4] += (int)$flipnote[6]; // Purple stars

                                break;

                            }

                        }

                    }

                }

            }

        ?>

        <p style="margin-left:60px;margin-top:0px">

            <span class="star0c">★</span><span class="star0c"><?= $totalStars[0] ?></span>

            <span class="star1c">★</span><span class="star1"><?= $totalStars[1] ?></span>

            <span class="star2c">★</span><span class="star2"><?= $totalStars[2] ?></span>

            <span class="star3c">★</span><span class="star3"><?= $totalStars[3] ?></span>

            <span class="star4c">★</span><span class="star4"><?= $totalStars[4] ?></span>

        </p>

    </div>     

    <div class="coso" style="border-radius: 0px 0px 8px 8px; color: #8ac4c3; width: 436px;height:12px;" width="436" height="12">  </div>

</div>
<!--posted flips-->
      <div class="coso" style="border-radius: 8px 8px 0px 0px; color: #8ac4c3;">
      Flipnotes made by <?= htmlspecialchars($username) ?>
      </div>
      <div class="main">
        <div class="main-content">
         <div class="fgrid">
            <!--template grid item-->
            <?php if (empty($newestEntries)): ?>

<li>No entries found.</li>

<?php else: ?>

<?php 
    
    $filteredEntries = array_filter($newestEntries, function($entry) use ($username, $authors) {
        foreach ($authors as $author) {
            if ($author[0] == $entry[1] && $author[1] == $username) {
                return true;
            }
        }
        return false;
    });
?>

<?php foreach ($filteredEntries as $entry): ?>

    <?php

        // Find the author's name from the $authors array

        $authorName = '';

        foreach ($authors as $author) {

            if ($author[0] == $entry[1]) {

                $authorName = $author[1];

                break;

            }

        }

    ?>

    <div class="fgriditem">

        <a href="video.php?creatorid=<?= htmlspecialchars($entry[0]) ?>&filename=<?= htmlspecialchars($entry[1]) ?>&name=<?= htmlspecialchars($author[1]) ?>" style="top:0 ;">

            <flipnote-image id="player" class="fplayer"  src="database/creators/<?= htmlspecialchars($entry[0]) ?>/<?= htmlspecialchars($entry[1]) ?>.ppm">

        </a>

        <div class="fgridtitle">

            <span>

       
            <a href="user.php?username=<?= htmlspecialchars($authorName) ?>" id="title">


<?= htmlspecialchars($authorName) ?>


</a>


</span>


<span style="float:right" id="starc">★<?= $entry[2] ?></span>

        </div>

    </div>

<?php endforeach; ?>

<?php endif; ?>
            
            
            
 
        </div>
       
      </div>    
      <div class="coso" style="border-radius: 0px 0px 8px 8px;">
        <div class="footer">
        <!--  <button id="decrement">&lt;</button>
          <span id="page-number">01</span>
          <button id="increment">&gt;</button> -->
        </div>
      </div>
    </div>
    </div>
  
  

</body>
</html>