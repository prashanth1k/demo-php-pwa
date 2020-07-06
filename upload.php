<?php
$target_dir = "images/";
$claimNumber = $_POST['claimNumber'];
$cameraCheck = $_POST['cameraCheck'];


if ($cameraCheck == "false") {
  // fair warning: messy code ahead
  $uploadOk = 1;
  
  $i=0;
  $total = count($_FILES['fileToUpload']['name']);
  
  for ($i=0 ; $i < $total ; $i++ ) {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"][$i]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  
    echo "<br><br>"."Processing file:" . $target_file . "<br>";
    echo "------------------" . "<br>";
    // Check if image file is a actual image or fake image  
    if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
      if($check !== false) {
        echo "✅ File is an image - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
      } else {
        echo "👎🏻 File is not an image.";
        $uploadOk = 0;
      }

      if($claimNumber == "") {
        echo "👎🏻 Claim Number is required.";
        $uploadOk = 0;
      }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
      echo "👎🏻 File already exists.";
      $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"][$i] > 1000000) {
      echo "👎🏻 Your file is too large.";
      $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
      echo "👎🏻 Only JPG, JPEG, PNG & GIF files are allowed.<br>";
      $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      echo "<br><br>Your file was not uploaded. Fix errors and retry.";
      // header("Location:/server?upload=Sorry, your file was not uploaded.");
    } else {
      // everything is ok, try to upload file
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded.";

        include_once 'db.php';
        $fileURL = "https://$_SERVER[HTTP_HOST]/images/" . $_FILES["fileToUpload"]["name"][$i];
        $insert = $db->query("INSERT INTO claim_images (claim_number, image_url) VALUES ('".$claimNumber."','".$fileURL."')");
        if(!$insert) {
            echo $db->lastErrorMsg() . "<br>";
        }
        $db->close();

        header("Location:/?upload=File uploaded.");
      } else {
        echo "Sorry, there was an error uploading your file.";
      }
    }
  } // for
} // if ($cameraCheck == false)
else {
  // camera supported
  $photos = $_POST["photosData"];
  $photosArr = explode("<#>", $_POST['photosData']); 

  $arrCount = count($photosArr);
  if ($claimNumber == "" || $arrCount < 2) {
    header("Location:/?upload=Claim Id and images are required. Fix problems and retry.");
    exit("error");
  }
  else {
    $i = 0;
    include_once 'db.php';
    for ($i=1; $i < $arrCount; $i++) {

      $img = str_replace('data:image/jpeg;base64,', '', $photosArr[$i]);
      $img = str_replace(' ', '+', $img);
      $image_base64 = base64_decode($img);
      $file = $target_dir . "image" . uniqid() . ".jpg";
      $fileURL = "https://$_SERVER[HTTP_HOST]/" . $file;

      file_put_contents($file, $image_base64);
      $insert = $db->query("INSERT INTO claim_images (claim_number, image_url) VALUES ('".$claimNumber."','".$fileURL."')");
      if(!$insert) {
          echo $db->lastErrorMsg() . "<br>";
      }
    } // for
    $db->close();
    header("Location:/?upload=Images uploaded.");
  } // else for claimNumber
}
?>
