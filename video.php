<?php
// Retrieve values from the URL parameters
$CreatorID = isset($_GET['creatorid']) ? htmlspecialchars($_GET['creatorid']) : 'Guest';
$Filename = isset($_GET['filename']) ? htmlspecialchars($_GET['filename']) : 0;
$Name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'anon';
//funny shit loads flipnotes waos
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
  
  //scans the folder for comments
  $folder_path = "hatenadir/ds/v2-xx/comments/$CreatorID/$Filename/comments"; // adjust this to the folder you want to read

  if (!file_exists($folder_path)) {
      mkdir($folder_path, 0777, true); // create the folder with recursive permissions
  }
  
  $files = scandir($folder_path);
  $ppm_files = array();

  foreach ($files as $file) {
      if (substr($file, -4) == '.ppm') { // check if file ends with .ppm
          $ppm_files[$file] = filectime($folder_path . '/' . $file);
      }
  }
  
  asort($ppm_files); // sort by value (file creation time) in ascending order

  function writeToFile($filename, $content) {
    try {
        $fileHandle = fopen($filename, 'w');
        if ($fileHandle === false) {
            throw new Exception("Error opening the file for writing.");
        }
  
        fwrite($fileHandle, $content);
  
        fclose($fileHandle);
  

    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
  }
  
  $ppm_files = array_keys($ppm_files); // get the sorted file names
$commentnumbers = count($ppm_files);
$comments_dat_path = "database/comments.dat";
$comments_dat_contents = file_get_contents($comments_dat_path);
$comments_dat_lines = explode("\n", $comments_dat_contents);
require("dsphp/ugoImager0824.php");


  ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/video.css"> 
    <script src="https://cdn.jsdelivr.net/npm/flipnote.js@5/dist/flipnote.webcomponent.min.js"></script>
    <title>Flipnote by <?=$Name?></title>
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
        <div class="coso" style="border-radius: 8px 8px 0px 0px; margin-bottom:0px;">
        </div>
       
          <div class="main-content">
           
              <!--template grid item-->
              <div class="video-medio" >
                <flipnote-player class="fplayer" width="100vh" src="database/creators/<?=$CreatorID?>/<?=$Filename?>.ppm"></flipnote-player>
              
                
              </div>
          </div>
          
        <div class="coso" style="border-radius: 0px 0px 8px 8px;">
          <div class="footer">

          </div>
        </div>
      </div>
      <!--PROFILE SIDE BAR-->
        <div class="sidebar" style="border-radius: 8px 8px 8px 8px;">
          <div class="fgrid">
            <!--template grid item-->
            <?php $i = 0; ?>
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


    <flipnote-image style="margin:0px;"id="player" class="fplayer"  src="database/creators/<?= htmlspecialchars($entry[0]) ?>/<?= htmlspecialchars($entry[1]) ?>.ppm">"></flipnote-image>


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
    <?php if (++$i == 3) break; ?>    

<?php endforeach; ?>


<?php endif; ?>

</div>
</div>
</div>
            
<table  border="0" cellspacing="0" cellpadding="0" class="tab" style="background-color: #64878c;border-radius: 8px 8px 0px 0px;">
            <tr>
                  <td class="border" width="5" align="center">
                        <div class="border"></div>
                  </td>
                  <td class="border" width="60" align="center">
                        <div class="border"></div>
                  </td>
                  <td class="border" width="95" align="center">
                        <div class="border"></div>
                  </td>
            </tr>
            <tr>
                  <td class="taboff" align="left">
                  <div class="on" align="left"><p style="color: #8ac4c3;padding-left:10px;margin: 3px;">Comments (<?=$commentnumbers?>)</p></div>
                  </td>

            </tr>
      </table>
      <div class="cmnt-list" style="background-color: #dceaee;border-radius: 0px 0px 8px 8px; margin-bottom: 20px;">
            <div class="pager">
                  <table>
                        <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                        </tr>
                        <tr>
                              <td colspan="2">
                                    <div class="pad3" align="left"></div>
                              </td>
                        </tr>
                  </table>
            </div>
           
            <div class="hr"></div>
           <?php
if ($commentnumbers == 0) {
    echo '<div>No comments available.</div>';
}
else {
    foreach ($ppm_files as $file) {
          $filename_without_ppm = substr($file, 0, -4);
          $comuser = '';
          $comtime = '';
      
          foreach ($comments_dat_lines as $line) {
              $line = trim($line); // Remove newline character
              if (empty($line)) {
                  continue; // Skip empty lines
              }
              list($user, $file_name, $date) = explode("\t", $line);
              if ($file_name == $filename_without_ppm) {
                  $comuser = $user;
                  $comtime = $date;
                  break;
              }
          }
       //   $npf = new ugoImager("hatenadir/ds/v2-xx/comments/$CreatorID/$Filename/comments/$file");
       //   $npf->resizeTo(128,84) ;         
          // Usage:
       //   $filename = "hatenadir/ds/v2-xx/comments/$CreatorID/$Filename/comments/$filename_without_ppm.npf";
       //   $content = $npf->returnNPF();
       //   writeToFile($filename, $content);
           ?> 
           <div class="cmnt-box" style="margin-left:10px; margin-bottom:20px;" width="20vw">
    <table>
        <tr>
            <td width="20vh"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div class="item-term" align="left"><a class="more-f"
                        href="user.php?username=<?=$comuser?>">
                        <?=$comuser?>
                </a></div>
            </td>
            <td colspan="2">
                <div class="item-value" align="right"><a class="cmnt-time"
                        href="user.php?username=<?=$comuser?>"><span
                              class="gray"><?=$comtime?></span></a></div>
            </td>
        </tr>
        <tr style=" outline-style: solid;outline-color: #64878c;outline-width: 5px;  border-radius: 0.2px 0.2px 0.2px 0.2px;">
            <td colspan="3" >
                <div class="flipnote-container">
                    <flipnote-image id="player" class="fimage"  src="hatenadir/ds/v2-xx/comments/<?=$CreatorID?>/<?=$Filename?>/comments/<?=$filename_without_ppm?>.ppm" ></flipnote-image>
                </div>
            </td>
        </tr>
    </table>
</div>
                            <div class="pad5t">
                                <div class="hr"></div>
                            </div>

<?php                    }
                  }?>

                  </table>
                  </table>
                  </table>
                  <div style="background-color: #64878c;border-radius: 0px 0px 8px 8px; height:20px;"></div>
                             </div>
                    </body>
                    </html>

</body>
</html>