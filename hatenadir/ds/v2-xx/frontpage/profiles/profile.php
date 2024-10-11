<?php
// save file function
function writeToFile($filename, $content) {
  try {
      $fileHandle = fopen($filename, 'w');
      if ($fileHandle === false) {
          throw new Exception("Error opening the file for writing.");
      }

      fwrite($fileHandle, $content);

      fclose($fileHandle);

      echo "Content written to the file successfully.";
  } catch (Exception $e) {
      echo "An error occurred: " . $e->getMessage();
  }
}
//read the args send by movie.py for profile generation
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

// import the ugomenu class:

require("dsphp/class.ugomenu.php");

// start a new ugomenu, for these examples, we'll create a generic "demo" menu:

$html = new ugomenu;

// set the menu type to type 0 (the same as the 'index' menu)

$html->setType("2");

// set the top screen title to "demo page":

$html->setMeta("uppertitle", "Flipnotes by $username");

// set the top screen subtitle to "demo in progress":

$html->setMeta("uppersubbottom", "$username Flipnotes");

// or for a flipnote grid thumbnail:
  if (empty($newestEntries)) {
    // do nothing
} else {
    $filteredEntries = array_filter($newestEntries, function($entry) use ($username, $authors) {
        foreach ($authors as $author) {
            if ($author[0] == $entry[1] && $author[1] == $username) {
                return true;
            }
        }
        return false;
    });

    foreach ($filteredEntries as $entry) {
        $authorName = '';

        foreach ($authors as $author) {
            if ($author[0] == $entry[1]) {
                $authorName = $author[1];
                break;
            }
        }

        $html->addItem([
         "url"  => "http://flipnote.hatena.com/hatenadir/ds/v2-xx/movie/$entry[0]/$entry[1].ppm",
         "file" => "database/Creators/$entry[0]/$entry[1].ppm",
         "icon" => "3"
        ]);
    }
}


// Usage:
$filename = "hatenadir\\ds\\v2-xx\\frontpage\\profiles\\$username.ugo";
$content = $html->getUGO();
writeToFile($filename, $content);
  ?>

