<?php
$target_dir = "images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$claimNumber = $_POST['claimNumber'];
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
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
if ($_FILES["fileToUpload"]["size"] > 1000000) {
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
  // if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";

    include_once 'db.php';
    $fileURL = "https://$_SERVER[HTTP_HOST]/images/" . $_FILES["fileToUpload"]["name"];
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
?>
