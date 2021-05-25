<?php

class Steganography {
  public $file;
  public $message;

  function __construct($file = "img/mushroom.jpg", $message = "DEFAULT"){
    $this->file = $file;
    $this->message = $message;
  }

  function steganize() {
    // Encode the message into a binary string.
    $binaryMessage = '';
    for ($i = 0; $i < mb_strlen($this->message); ++$i) {
      $character = ord($this->message[$i]);
      $binaryMessage .= str_pad(decbin($character), 8, '0', STR_PAD_LEFT);
    }
   
    // Inject the 'end of text' character into the string.
    $binaryMessage .= '00000011';
   
    // Load the image into memory.
    $img = imagecreatefromjpeg($this->file);
   
    // Get image dimensions.
    $width = imagesx($img);
    $height = imagesy($img);
   
    $messagePosition = 0;
   
    for ($y = 0; $y < $height; $y++) {
      for ($x = 0; $x < $width; $x++) {
   
        if (!isset($binaryMessage[$messagePosition])) {
          // No need to keep processing beyond the end of the message.
          break 2;
        }
   
        // Extract the colour.
        $rgb = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $rgb);
   
        $red = $colors['red'];
        $green = $colors['green'];
        $blue = $colors['blue'];
        $alpha = $colors['alpha'];
   
        // Convert the blue to binary.
        $binaryBlue = str_pad(decbin($blue), 8, '0', STR_PAD_LEFT);
   
        // Replace the final bit of the blue colour with our message.
        $binaryBlue[strlen($binaryBlue) - 1] = $binaryMessage[$messagePosition];
        $newBlue = bindec($binaryBlue);
   
        // Inject that new colour back into the image.
        $newColor = imagecolorallocatealpha($img, $red, $green, $newBlue, $alpha);
        imagesetpixel($img, $x, $y, $newColor);
   
        // Advance message position.
        $messagePosition++;
      }
    }
   
    // Save the image to a file.
    $newImage = 'secret.png';
    imagepng($img, $newImage, 9);
   
    // Destroy the image handler.
    imagedestroy($img);
  }

  function desteganize() {
    // Read the file into memory.
    $img = imagecreatefrompng($this->file);
   
    // Read the message dimensions.
    $width = imagesx($img);
    $height = imagesy($img);
   
    // Set the message.
    $binaryMessage = '';
   
    // Initialise message buffer.
    $binaryMessageCharacterParts = [];
   
    for ($y = 0; $y < $height; $y++) {
      for ($x = 0; $x < $width; $x++) {
   
        // Extract the colour.
        $rgb = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $rgb);
   
        $blue = $colors['blue'];
   
        // Convert the blue to binary.
        $binaryBlue = decbin($blue);
   
        // Extract the least significant bit into out message buffer..
        $binaryMessageCharacterParts[] = $binaryBlue[strlen($binaryBlue) - 1];
   
        if (count($binaryMessageCharacterParts) == 8) {
          // If we have 8 parts to the message buffer we can update the message string.
          $binaryCharacter = implode('', $binaryMessageCharacterParts);
          $binaryMessageCharacterParts = [];
          if ($binaryCharacter == '00000011') {
            // If the 'end of text' character is found then stop looking for the message.
            break 2;
          }
          else {
            // Append the character we found into the message.
            $binaryMessage .= $binaryCharacter;
          }
        }
      }
    }
   
    // Convert the binary message we have found into text.
    $message = '';
    for ($i = 0; $i < strlen($binaryMessage); $i += 8) {
      $character = mb_substr($binaryMessage, $i, 8);
      $message .= chr(bindec($character));
    }
   
    return $message;
  }

}

