<html>
  <head>
      <title>Demo PHP + PWA!</title>
      <link rel="stylesheet" href="https://unpkg.com/mvp.css">
      <link rel="stylesheet" href="./assets/styles.css">
      <link rel="manifest" href="./manifest.webmanifest">
  </head>
  <body style="text-align:center;">

  
    <?php

    class claimImg {
        /**
         * Retrieve data from database
         */
        public function select() {
            include_once 'db.php';
            
            if($db != null) {
                $result=$db->query("SELECT * FROM claim_images");
            
                $claimData=array();
                while($row=$result->fetchArray(SQLITE3_ASSOC)) {
                    array_push($claimData, array("claim_number"=>$row['claim_number'], "image_url"=>$row['image_url']));
                }
                
                // echo var_dump($claimData); 
                
                // this is useful if PHP is called by ext. - retain
                // print(json_encode(array_reverse($claimData)));
                return $claimData;
                $db->close();

            }
            else {
                print(json_encode(array("Error: Cannot connect to DB")));
            }
        }

        

    }

    

    $claimImg=new claimImg();
    $claimData = $claimImg->select();

    if(isset($_GET["upload"])) {
        $info = $_GET["upload"];
    }
    else {
        $info = "";
    }

    //end
    ?>

    <h2>
        PHP + PWA Demo
    </h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" style="text-align:center;">
        Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">

        Claim Number:
        <input type="text" name="claimNumber" id="claimNumber">

        <input type="submit" value="Upload Image" name="submit">
        <span class="info"><?php echo $info; ?></span>
    </form>

    <div style="text-align:center;">
        <?php foreach( $claimData as $key => $value ): ?>
                <h4 class="margin-top:5em;"> <?= $value['claim_number'] ?></h4>
                <img src="<?= $value['image_url'] ?>" class="claim-img"/>
         <?php endforeach; ?>

    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('./sw.js').then(function(registration) {
                    // Registration was successful
                    console.log('Service Worker registration successful with scope: ', registration.scope);
                }, function(err) {
                    // registration failed :(
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
  </body>

  <footer>
 
  </footer>
</html>