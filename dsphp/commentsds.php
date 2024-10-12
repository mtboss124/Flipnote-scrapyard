<?php
$creatorid = $argv[3];
#unused because i dont know how to pass this value back to python 
#hacky af code
#update this variable is used but in a even more hacky way!!!!!!!!!!!
$filename = $argv[1]; #the un-used in question 
$commentnumbers = $argv[2];
$filepp = $filename;

#scans the folder for comments
$folder_path = "hatenadir/ds/v2-xx/comments/$creatorid/$filename/comments"; // adjust this to the folder you want to read
$files = scandir($folder_path);

$ppm_files = array();
foreach ($files as $file) {
    if (substr($file, -4) == '.ppm') { // check if file ends with .ppm
        $ppm_files[] = $file;
    }
}
//wirte to file function
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
//counts the comments
$commentnumbers = count($ppm_files);

// Read the comments.dat file
$comments_dat_path = "database/comments.dat";
$comments_dat_contents = file_get_contents($comments_dat_path);
$comments_dat_lines = explode("\n", $comments_dat_contents);

#ugoimager by Nitrogen!!! thank you!!!! you have saved my life!!!!!!!!!!!!!!!!!!
require("dsphp/ugoImager0824.php");
#there has to be a better way to serve php
#just got the idea of executing php in a .htm file but i havent tested it and the deadline is in a few hours (hypotetical might not even be posible)
#but gota reach the deadline


#arguments necesary for the dsi client(i am tired)
                $html = '
<html>

<head>

      <meta name="commentbutton" content="http://flipnote.hatena.com/ds/v2-xx/post/flipnote.reply?' . http_build_query(array(

          'channel' => '',

          'creator_id' => $creatorid,

          'flipnote_id' => $filename

      )) . '">



      <link rel="stylesheet" type="text/css" href="http://flipnote.hatena.com/css/ds/basic.css">
</head>

<body>
      <table width="240" border="0" cellspacing="0" cellpadding="0" class="tab">
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
                  <td class="space"></td>
                  <td class="taboff" align="center"><a class="taboff"
                              href="http://flipnote.hatena.com/ds/v2-xx/movie/' . $creatorid . '/' . $filename . '.ppm">Description</a>
                  </td>
                  <td class="tabon" align="center">
                        <div class="on" align="center">Comments ( ' . $commentnumbers. ')</div>
                  </td>
            </tr>
      </table>
      <div class="cmnt-list">
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
            ';
            if ($commentnumbers == 0) {
                  $html .= '<div class="cmnt-list">No comments yet</div>';
              }
              else {
                  foreach ($ppm_files as $file) {
                        $filename_without_ppm = substr($file, 0, -4);
                        $comuser = '';
                        $comtime = '';
                        $npf = new ugoImager("hatenadir/ds/v2-xx/comments/$creatorid/$filename/comments/$file");
                        $npf->resizeTo(128,84) ;         
                        // Usage:
                        $filenamew = "hatenadir/ds/v2-xx/comments/$creatorid/$filename/comments/$filename_without_ppm.npf";
                        $content = $npf->returnNPF();
                        writeToFile($filenamew, $content);

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

                        $html .= '
                            <div class="cmnt-box">
                                <table>
                                    <tr>
                                        <td width="85"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="item-term" align="left"><a class="more-f"
                                                        href="http://flipnote.hatena.com/ds/v2-xx/frontpage/profiles/' . $comuser . '.uls">
                                                ' . $comuser . '
                                                </a></div>
                                        </td>
                                        <td colspan="2">
                                            <div class="item-value" align="right"><a class="cmnt-time"
                                                        href="http://flipnote.hatena.com/ds/v2-xx/frontpage/profiles/' . $comuser . '.uls"><span
                                                              class="gray">' . $comtime . '</span></a></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div class="comment-memo"><a
                                                        href="http://flipnote.hatena.com/ds/v2-xx/comments/' . $creatorid . '/' . $filepp . '/comments/' . $file . '"><img
                                                              src="http://flipnote.hatena.com/ds/v2-xx/comments/' . $creatorid . '/' . $filepp . '/comments/' . $filename_without_ppm . '.npf"
                                                              width="128" height="84"></a></div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="pad5t">
                                <div class="hr"></div>
                            </div>
                        ';
                    }
                  }
                    
                    $html .= '
                        </div>
                    </body>
                    </html>
                    ';
                    
                    echo $html;
                    ?>