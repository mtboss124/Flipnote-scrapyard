<?php
$username = $argv[1];

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

//html stuff u already know lol
?>
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
    

                $html = '

                <html>
            
                <head>
            
                    <meta name="upperlink" content="http://flipnote.hatena.com/images/ds/theme/20_monokurock/background.nbf">
            
                    <meta name="background" content="http://flipnote.hatena.com/images/ds/theme/20_monokurock/bg.nbf,8,8">
            
                    <link rel="stylesheet" type="text/css" href="http://flipnote.hatena.com/css/ds/20_monokurock.css">
            
                </head>
            
                <body>
            
                    <div align="center" class="head"> 
            
                        <img src="http://flipnote.hatena.com/images/ds/theme/20_monokurock/head.ntft" width="218" height="12">
            
                        <div class="prof_out">
            
                            <table width="216" border="0" cellspacing="0" cellpadding="0" class="profbox">
            
                                <tr>
            
                                    <td width="5"></td>
            
                                    <td align="right" width="50"></td>
            
                                    <td></td>
            
                                    <td width="19"></td>
            
                                </tr>
            
                                <tr>
            
                                    <td width="5" rowspan="3"></td>
            
                                    <td rowspan="3" align="right">
            
                                        <div class="icon" width="50"><img src="http://mtboss.ddns.net:8080/profiles/' . htmlspecialchars($username) . '.ntft" width="48" height="48"></div>
            
                                    </td>
            
                                    <td></td>
            
                                    <td width="19"></td>
            
                                </tr>
            
                                <tr>
            
                                    <td class="username">' . htmlspecialchars($username) . '</td>
            
                                </tr>
            
                                <tr>
            
                                    <td> 
            
                                        <img src="http://flipnote.hatena.com/images/ds/spacer.npf" width="3" height="1">
            
                                        <span class="more-f"></span> 
            
                                        <img src="http://flipnote.hatena.com/images/ds/spacer.npf" width="3" height="1">
            
                                        <span class="more-f">Stars recived</span>
            
                                        <br> 
            
                                        <img src="http://flipnote.hatena.com/images/ds/spacer.npf" width="0" height="1">
            
                                        <span class="star0c">★ </span>
            
                                        <span class="star0c">' . $totalStars[0] . ' </span>
            
                                        <span class="star1c">★ </span>
            
                                        <span class="star1">' . $totalStars[1] . '  </span>
            
                                        <span class="star2c">★ </span>
            
                                        <span class="star2">' . $totalStars[2] . ' </span>
            
                                        <span class="star3c">★ </span>
            
                                        <span class="star3">' . $totalStars[3] . '  </span>
            
                                        <span class="star4c">★ </span>
            
                                        <span class="star4">' . $totalStars[4] . '  </span>
            
                                    </td>
            
                                    <td width="19" class="mystatus">
            
                                    </td>
            
                                </tr>
            
                                <tr>
            
                                    <td colspan="4">
            
                                        <div class="item-value">
            
                                        </div>
            
                                    </td>
            
                                </tr>
            
                            </table>
            
                        </div> 
            
                        <img src="http://flipnote.hatena.com/images/ds/theme/20_monokurock/foot.ntft" width="218" height="5">
            
                    </div>
            
                    <div class="send" align="center"> 
            
                        <a href="http://flipnote.hatena.com/ds/v2-xx/frontpage/profiles/' . htmlspecialchars($username) . '.uls">
            
                            <img src="http://flipnote.hatena.com/images/en-gb/ds/theme/20_monokurock/send.ntft" width="218" height="32" class="button">
            
                        </a>
            
                        <table align="center" class="send">
            
                            <tr>
            
                                ' . (empty($newestEntries) ? '<li>No entries found.</li>' : implode('', array_slice(array_filter(array_map(function($entry) use ($username, $authors) {
            
                                    foreach ($authors as $author) {
            
                                        if ($author[0] == $entry[1] && $author[1] == $username) {
            
                                            return '
            
                                            <td width="66" align="center">
            
                                                <div class="icon">
            
                                                    <a href="http://flipnote.hatena.com/ds/v2-xx/movie/' . htmlspecialchars($entry[0]) . '/' . htmlspecialchars($entry[1]) . '.ppm">
            
                                                        <img src="http://flipnote.hatena.com/ds/v2-xx/movie/' . htmlspecialchars($entry[0]) . '' . htmlspecialchars($entry[1]) . '.ppm" width="64" height="48">
            
                                                    </a>
            
                                                </div>
            
                                            </td>
            
                                            <td width="6"></td>
            
                                            ';
            
                                        }
            
                                    }
            
                                    return null;
            
                                }, $newestEntries)), 0, 3))) . '
            
                            </tr>
            
                        </table>
            
                        <div align="right"><a href="http://flipnote.hatena.com/ds/v2-xx/frontpage/profiles/' . htmlspecialchars($username) . '.uls">See more</a></div>
            
                    </div>
            
                </body>
            
                </html>
            
                ';
            
            
echo $html;
?>