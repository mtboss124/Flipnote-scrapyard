<?php


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
  <title>Inicio</title>
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
      <div class="coso" style="border-radius: 8px 8px 0px 0px;">
       <!-- <div class="dropdown">
          <button class="dropbtn" id="dropbtn">ORDENAR</button>
          <div class="dropdown-content">
              <a href="#" onclick="selectOption(this, '+POPULAR')">+POPULAR</a>
              <a href="#" onclick="selectOption(this, '-POPULAR')">-POPULAR</a>
              <a href="#" onclick="selectOption(this, 'NUEVOS')">NUEVOS</a>
              <a href="#" onclick="selectOption(this, 'ANTIGUOS')">ANTIGUOS</a>
          </div>
      </div> -->
      </div>
      <div class="main">
        <div class="main-content">
         <div class="fgrid">
            <!--template grid item-->
            <?php if (empty($newestEntries)): ?>


<li>No entries found.</li>


<?php else: ?>


<?php foreach ($newestEntries as $entry): ?>


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


            <flipnote-image id="player" class="fplayer"  src="database/creators/<?= htmlspecialchars($entry[0]) ?>/<?= htmlspecialchars($entry[1]) ?>.ppm">"></flipnote-image>


        </a>


        <div class="fgridtitle">


            <span style="float:left">


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
  
    <!--PROFILE SIDE BAR-->
   <!-- left out of final school project cause of time constrains
    <div class="sidebar">
      <a href="perfil.html" class="photo" style="margin-left: 0; float:left;">Foto</a>
        <div class="info" style="margin-top: 0px;">
            <div class="rank">♕ 1</div>
            <div class="name">Tempname</div>
        </div>
        <div class="last-flips" style="clear: both; padding-top: 15px;">
            <div class="title">LAST FLIPS</div>
            <div class="flips">
                <div class="flip"></div>
                <div class="flip"></div>
                <div class="flip"></div>
            </div>
        </div>
        <div class="stars">
            <div class="star">★ 1</div>
            <div class="star">🠗 1</div>
            <div class="star">★ 1</div>
        </div>
        <div class="subs">Views 00</div> 
            -->
 <!-- <script>
    let lastScrollTop = 0; // Variable para almacenar la última posición de scroll
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function() {
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      if (currentScroll > lastScrollTop) {
        // Scrolling down
        navbar.style.top = '-60px'; // Ocultar el navbar (ajustar según la altura del navbar)
      } else {
        // Scrolling up
        navbar.style.top = '0'; // Mostrar el navbar
      }
      
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Para Mobile
    });

    // JavaScript para cambiar de página con los botones del footer
    const pageNumber = document.getElementById('page-number');
    const btnIncrement = document.getElementById('increment');
    const btnDecrement = document.getElementById('decrement');

    function updatePageNumber(increment) {
      let currentPage = parseInt(pageNumber.textContent);
      if (increment) {
        currentPage += 1;
      } else {
        if (currentPage > 1) {
          currentPage -= 1;
        }
      }
      pageNumber.textContent = currentPage.toString().padStart(2, '0'); // Asegura que el número tenga dos dígitos
    }

    btnIncrement.addEventListener('click', () => {
      updatePageNumber(true); // Incrementa la página
      // Aquí puedes agregar lógica para redirigir a la nueva página si es necesario
    });

    btnDecrement.addEventListener('click', () => {
      updatePageNumber(false); // Decrementa la página
      // Aquí puedes agregar lógica para redirigir a la nueva página si es necesario
    });

    function selectOption(element, option) {
        document.getElementById('dropbtn').innerText = option;
        
        var links = document.querySelectorAll('.dropdown-content a');
        links.forEach(function(link) {
            link.classList.remove('selected');
        });
        
        element.classList.add('selected');
    }

  </script> -->

</body>
</html>