<?php
session_start();

$scriptPath = "/var/www/html/xray1/prediction/prediction.py";
$modelPath =  "/var/www/html/xray1/prediction/oH1Rl.h5";
$storageDir = "/var/www/html/xray1";



define(BR, "<br/>");
try {

  if ( isset($_FILES["file"]["type"]) )
  {
    $max_size = 500 * 1024; // 500 KB
    $destination_directory = "upload/";
    $validextensions = array("jpeg", "jpg", "png");

    $temporary = explode(".", $_FILES["file"]["name"]);
    $file_extension = end($temporary);

    // We need to check for image format and size again, because client-side code can be altered
    if ( (($_FILES["file"]["type"] == "image/png") ||
          ($_FILES["file"]["type"] == "image/jpg") ||
          ($_FILES["file"]["type"] == "image/jpeg")
        ) && in_array($file_extension, $validextensions))
    {
      if ( $_FILES["file"]["size"] < ($max_size) )
      {
        if ( $_FILES["file"]["error"] > 0 )
        {
          echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file"]["error"] . "</strong></div>";
        }
        else
        {
          if ( file_exists($destination_directory . $_FILES["file"]["name"]) )
          {
            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $_FILES["file"]["name"] . "</strong> already exists.</div>";
          }
          else
          {
            $sourcePath = $_FILES["file"]["tmp_name"];
            $targetPath = $destination_directory . $_FILES["file"]["name"];
            $fileMove =  move_uploaded_file($sourcePath, $targetPath);

            if($fileMove != true)
            {
              echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File from  <strong>" . $sourcePath . "</strong> to ".$targetPath." move failed.</div>";
            }
            else
            {
              $command ="python3 ".$scriptPath." ".$storageDir."/".$targetPath." ".$modelPath;
              $result = null;
              exec($command,$result);
              var_dump($result);
              $classLabelString = $result[0];
              $precentages = $result[1];
              // array(2) { [0]=> string(31) "['BACTERIA', 'NORMAL', 'VIRUS']" [1]=> string(34) "[0.67382556 0.20724042 0.00542949]" }

              echo "<div class=\"alert alert-success\" role=\"alert\">";
              echo "<p>Image uploaded successful</p>";
              echo "<p>File Name: <a href=\"". $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
              echo "<p>Type: <strong>" . $_FILES["file"]["type"] . "</strong></p>";
              echo "<p>Size: <strong>" . round($_FILES["file"]["size"]/1024, 2) . " kB</strong></p>";
              echo "<p>Temp file: <strong>" . $_FILES["file"]["tmp_name"] . "</strong></p>";
              echo "</div>";
            }
          }
        }
      }
      else
      {
        echo "<div class=\"alert alert-danger\" role=\"alert\">The size of image you are attempting to upload is " . round($_FILES["file"]["size"]/1024, 2) . " KB, maximum size allowed is " . round($max_size/1024, 2) . " KB</div>";
      }
    }
    else
    {
      echo "<div class=\"alert alert-danger\" role=\"alert\">Unvalid image format. Allowed formats: JPG, JPEG, PNG.</div>";
    }
  }
  
}
catch(Exception $ex) {
    die("caught exception: ". $ex->getMessage());

}
echo "Keep on going!. ..." . BR;

?>
