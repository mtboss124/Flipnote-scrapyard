<?php
// ugoImager by Nitrogen (CastaSereal#2555)
// Made for RexiMemo

class ugoImager {
    /* 
        ugoImager - Converts images to and from Flipnote formats
        Last Modified - 08/xx/24
    */
    private $paletteData;
    private $pixelData;
    private $imageOutput;
    private $file;

    public function __construct($filePath, $width = NULL, $height = NULL) {
        switch(strtoupper(pathinfo($filePath)["extension"])) {
            case "PPM":
                $this->file = $this->parsePPM($filePath);
                return true;
            break;
            case "NPF":
                $this->file = $this->parseNPF($filePath, $width, $height);
                return true;
            break;
            default:
                $this->file = imagecreatefromstring(file_get_contents($filePath));
                return true;
            break;
        }
    }

    public function cropImage($w = 0, $h = 0) {
        // Crops an image
        $dst = imagecrop(
            $this->file,
            ["x" => 0,
            "y" => 0,
            "width" => imagesx($this->file),
            "height" => imagesy($this->file) - 12]
        );
        $this->file = $dst;
        return true;
    }

    public function resizeTo($w, $h) {
        // Resizes an image
        $dst = imagecreatetruecolor($w, $h);
        imagecopyresized($dst, $this->file, 0, 0, 0, 0, $w, $h, imagesx($this->file), imagesy($this->file));
        $this->file = $dst;
        return true;
    }

    public function returnNPF() {
        $palMap = [];
        $pixLst = [];
        $pixelData = [];
        $paletteData = [0];

        // make the image 15 colors
        imagetruecolortopalette($this->file, true, 15);

        for ($y = 0; $y < imagesy($this->file); $y++) {
            for ($x = 0; $x < $this->roundToPower(imagesx($this->file)); $x++) {  
                // assign pixels a slot on the palette
                $clr = imagecolorsforindex($this->file, imagecolorat($this->file, $x, $y));
                $r =floor( $clr["red"] * 0x1F / 0xFF);
                $g =floor( $clr["green"] * 0x1F / 0xFF);
                $b =floor( $clr["blue"] * 0x1F / 0xFF);
                    $clr_converted = ((1 << 15) | ($b << 10) | ($g << 5) | $r);

                    if (!in_array($clr_converted, $paletteData)) {
                        // add the rgb555 value into the paletteData
                        array_push($paletteData, $clr_converted);
                    }
                // add the palette mappings to the pixel list
                $pixLst[] = array_flip($paletteData)[$clr_converted];
            }
        }
        if(count($paletteData) < 15) {
            $c = count($paletteData);
            for($x = 0; $x < $c; $x++) {
                array_push($paletteData, 0x00);
            }
        }       
        foreach($pixLst as $index => $px) {
            if ($index % 2 == 0) {
                // add a pixel pair to the pixel data
                // TODO: fix palette reds and greens being randomly placed
                $pixelData[] = (($pixLst[$index + 1] & 0b1111) << 4) | ($pixLst[$index] & 0b1111);
                //$pixelData[] = ($pixLst[$index] | $pixLst[$index + 1] << 4);
            }
        }

        // Output the image
        $this->imageOutput = "";
        $this->imageOutput .= pack("a4V*", "UGAR", 2, count($paletteData) * 2, count($pixelData));
        $this->imageOutput .= pack('v*', ...$paletteData);
        $this->imageOutput .= pack('C*', ...$pixelData);
        return $this->imageOutput;
    }
    public function returnPNG() {
        // Returns an PNG
        header("Content-Type: image/png");
        return imagepng($this->file);
    }
    // Private Functions
    private function parseNPF($npf, $w, $h) {
        // Parses a NPF file
        $paletteLst = [];
        $image = imagecreatetruecolor($w, $h);

        $file = fopen($npf, "r");
    
        // Palette Length
        fseek($file, 8);
        $paletteLen = unpack("V", fread($file, 4))[1];
    
        // Pixel Length
        fseek($file, 12);
        $pixelLen = unpack("V", fread($file, 4))[1];
    
        // Palette Data
        fseek($file, 16);
        $paletteData = unpack("v*", fread($file, $paletteLen));
    
        // Pixel Data
        fseek($file, 16 + $paletteLen);
        $pixelData = unpack("C*", fread($file, $pixelLen));
    
        foreach ($paletteData as $px) {
            // convert rgb555 values to rgb and add it to the palette stack
            $r = (($px & 0x1F) * 0xFF) / 0x1F;
            $g = ((($px >> 5) & 0x1F) * 0xFF) / 0x1F;
            $b = ((($px >> 10) & 0x1F) * 0xFF) / 0x1F;
            array_push($paletteLst, imagecolorallocate($imageOutput, $r, $g, $b));
        }
    
        $x = 0; $y = 0;
        foreach ($pixelData as $px) {
            // set the first pixel
            imagesetpixel($imageOutput, $x, $y, $paletteLst[$px & 0b1111]);
            // move to the next pixel
            $x++;
            if ($x >= $w) {
                $x = 0;
                $y++;
            }

            // set the second pixel
            if ($y < $h) {
                imagesetpixel($imageOutput, $x, $y, $paletteLst[($px >> 4) & 0b1111]);
                $x++;
                if ($x >= $w) {
                    $x = 0;
                    $y++;
                }
            }
        }
    
        fclose($file);
        return $imageOutput;
    }

    private function parsePPM($ppm) {
    // Parses a ppm from a comment
    $file = fopen($ppm, "r");
    $image = imagecreatetruecolor(256, 192);
    $paperColor = 0x00; // White
    $penColor = 0x01; // Black
    $frame = array_fill(0, 192, array_fill(0, 256, $paperColor));
    
    // Define colors
     // Set up the palette
    $paperColorGD = imagecolorallocate($image, 255, 255, 255); // white
    $penColorGD = imagecolorallocate($image, 0, 0, 0); // black

    // Animation Sequence Header
    fseek($file, 0x6A0);
    $frameOffsetLen = unpack("v", fread($file, 2))[1]; // Read size of the offset table
    fseek($file, 2, SEEK_CUR); // Skip padding
    fseek($file, 4, SEEK_CUR); // Skip unknown

    // Read frame offsets
    $numFrames = $frameOffsetLen / 4;
    $frameOffsets = [];
    for ($i = 0; $i < $numFrames; $i++) {
        $offset = unpack("V", fread($file, 4))[1];
        $frameOffsets[] = $offset + 0x6A0 + 4 * $numFrames; // Adjust for offset relative to the end of the list
    }

    // Read and decode each frame (only one layer needed)
    foreach ($frameOffsets as $frameOffset) {
        fseek($file, $frameOffset);
        
        // Pen and Paper information
        $penPaperInfo = ord(fread($file, 1));
        $paperColor = ($penPaperInfo & 0x01) ? 0x01 : 0x00; // Determine paper color
        
        // Layer 1 line encoding
        $lineEncoding = array_fill(0, 192, 0);
        $lineIndex = 0;

        for ($byteOffset = 0; $byteOffset < 48; $byteOffset++) {
            $byte = ord(fread($file, 1));
            for ($bitOffset = 0; $bitOffset < 8; $bitOffset += 2) {
                $lineEncoding[$lineIndex] = ($byte >> $bitOffset) & 0x03;
                $lineIndex++;
            }
        }

        // Decode frame data for the layer
        for ($line = 0; $line < 192; $line++) {
            $lineEncodingValue = $lineEncoding[$line];
            
            switch ($lineEncodingValue) {
                case 0:
                    // no data
                    break;
                case 1:
                    // type 1 compression
                    $chunkFlags = unpack("N", fread($file, 4))[1];
                    $pixel = 0;
                    for ($chunkIndex = 0; $chunkIndex < 32; $chunkIndex++) {
                        if ($chunkFlags & 0x80000000) {
                            $chunk = ord(fread($file, 1));
                            for ($bit = 0; $bit < 8; $bit++) {
                                if ($pixel < 256) {
                                    $frame[$line][$pixel] = ($chunk >> $bit) & 0x1 ? $penColor : $paperColor;
                                    $pixel++;
                                }
                            }
                        } else {
                            $pixel += 8;
                        }
                        $chunkFlags <<= 1;
                    }
                    break;
                case 2:
                    // type 2 inverted compression
                    $chunkFlags = unpack("N", fread($file, 4))[1];
                    $pixel = 0;
                    for ($chunkIndex = 0; $chunkIndex < 32; $chunkIndex++) {
                        if ($chunkFlags & 0x80000000) {
                            $chunk = ord(fread($file, 1));
                            for ($bit = 0; $bit < 8; $bit++) {
                                if ($pixel < 256) {
                                    $frame[$line][$pixel] = ($chunk >> $bit) & 0x1 ? $paperColor : $penColor;
                                    $pixel++;
                                }
                            }
                        } else {
                            $pixel += 8;
                        }
                        $chunkFlags <<= 1;
                    }
                    break;
                case 3:
                    // type 3 raw data
                    $chunkBytes = fread($file, 32);
                    for ($pixel = 0; $pixel < 256; $pixel++) {
                        $frame[$line][$pixel] = (ord($chunkBytes[(int)($pixel / 8)]) & (1 << ($pixel % 8))) ? $penColor : $paperColor;
                    }
                    break;
            }
        }
    }

    // Draw the frame
    for ($y = 0; $y < 192; $y++) {
        for ($x = 0; $x < 256; $x++) {
            $color = ($frame[$y][$x] == $penColor) ? $penColorGD : $paperColorGD;
            imagesetpixel($image, $x, $y, $color);
        }
    }

    return $image;
}
    

    private function roundToPower($n) {
        // Rounds to the nearest power of 2
        $power = [512, 256, 128, 64, 32, 16, 8, 4, 2, 1];
        foreach ($power as $p) {
            if ($n <= $p) {
                // DO NOT CHANGE THIS TO $p, it will give a number higher than the actual width and imagecolorat will freak out
                return $n; 
            }
        }
        return 1;
    }
}
