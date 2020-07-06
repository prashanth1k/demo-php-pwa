<html>
  <head>
      <title>Demo PHP + PWA!</title>
      <link rel="stylesheet" href="https://unpkg.com/mvp.css">
      <link rel="stylesheet" href="./assets/styles.css">
      <link rel="manifest" href="./manifest.webmanifest">
      <script>
        window.onload = function(e){ 
            initCamera();
        }
      </script>
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
                // print(json_encode(array_reverse($claimData))); // useful if PHP is called by ext. - retain
                
                return $claimData;
                $db->close();
            }
            else {
                print(json_encode(array("Error: Cannot connect to DB")));
            }
        } // select()
    } // claimImg class

    // initiate class and invoke function
    $claimImg=new claimImg();
    $claimData = $claimImg->select();

    // dynamically display upload message 
    // this page will be reloaded when form is submitted - modify for Ajax
    if(isset($_GET["upload"])) $info = $_GET["upload"];
    else $info = ""; 
    //end of main PHP
    ?>

    <!-- 
     * This is the actual HTML
    -->

    <h2>
        PHP + PWA Demo
    </h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" style="text-align:center;">

        <label for="claimNumber">Claim Number</label>
        <input type="text" name="claimNumber" id="claimNumber">
           
        <div id="camerasupport">
            <label for="video">Capture Image</label>
            <video id="video" name="video"></video> 
            <div>
                <button id="startbutton" onclick="takepicture(event)">Take photo</button>
            </div>

            <label for="photos">Your Images</label>
            <div id="photos" name="photos" class="photos"></div>
            <input type="hidden" id="photosData" name="photosData">
            <canvas id="canvas" style="display:none;"></canvas>
        </div>

        <div id="nocamerasupport">
            <label for="fileToUpload">Select image to upload</label>
            <input type="file" name="fileToUpload[]" id="fileToUpload" accept="image/*" multiple> 
        </div>

        <input type="submit" value="Upload Image" name="submit">
        <span class="info"><?php echo $info; ?></span>

        <input type="hidden" id="cameraCheck" name="cameraCheck" value="false">
    </form>

    <div style="text-align:center;">
        <!-- display uploaded images. Modify for actual use -->
        <?php foreach( $claimData as $key => $value ): ?>
                <h4 class="margin-top:5em;"> <?= $value['claim_number'] ?></h4>
                <img src="<?= $value['image_url'] ?>" class="claim-img"/>
         <?php endforeach; ?>

    </div>

    <script>
        var width = 320; 
        var height = 0; 

        var video = null;
        var canvas = null;
        var startbutton = null;
        var streaming = false;

        cameraSupport = false;

        if ('serviceWorker' in navigator) {
            // register service worker (PWA)
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('./sw.js').then(function(registration) {
                    // Registration was successful
                    console.log('Service Worker registration successful with scope: ', registration.scope);

                }, 
                function(err) {
                    // registration failed :(
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        
        function initCamera() {
            video = document.getElementById('video');
            canvas = document.getElementById('canvas');
            startbutton = document.getElementById('startbutton');

            clearphoto();

            navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
                
                cameraSupport = true;
                document.getElementById("cameraCheck").value = true;
                document.getElementById("nocamerasupport").style.display = "none";
            })
            .catch(function(err) {
                cameraSupport = false;
                console.log("Taking images is not supported on your device. " + err);
                document.getElementById("camerasupport").style.display = "none";
            });
            
            video.addEventListener('canplay', function(ev){
                if (!streaming) {
                    height = video.videoHeight / (video.videoWidth/width);
                    video.setAttribute('width', width);
                    video.setAttribute('height', height);
                    canvas.setAttribute('width', width);
                    canvas.setAttribute('height', height);
                    streaming = true;
                }
            }, false);
        }

        function clearphoto() {
            var context = canvas.getContext('2d');
            context.fillStyle = "#AAA";
            context.fillRect(0, 0, canvas.width, canvas.height);

            // var data = canvas.toDataURL('image/png');
            // photo.setAttribute('src', data);

            document.getElementById('photos').innerHTML = "";
            //document.getElementById("photosData").innerHTML = "";
        }

        function takepicture(event) {
            try {
                var context = canvas.getContext('2d');
               
                if (width && height) {
                    canvas.width = width;
                    canvas.height = height;
                    context.drawImage(video, 0, 0, width, height);
                   
                    var data = canvas.toDataURL('image/jpeg', 0.5);

                    document.getElementById("photosData").value = document.getElementById("photosData").value  + "<#>" + data;
                    
                    // const photos = document.getElementById('photos');  
                    const photoDyn = document.createElement('img'); 
                    photoDyn.src = data;
                    photoDyn.id = getUniqueId();
                    const subText = document.createElement('div');

                    const photoDynContainer = document.createElement('div');
                    photoDynContainer.id = `con${photoDyn.id}`;
                    photoDynContainer.appendChild(photoDyn);

                    subText.innerHTML = `<button onclick="removePhoto('` + photoDynContainer.id + `')"> Delete </button>`;
                    subText.className = "imageSubText";

                    photoDynContainer.appendChild(subText);
                    
                    document.getElementById('photos').appendChild(photoDynContainer); 
            
                } else clearphoto();
            }
            catch(e) {
                console.error(e);
            }
            finally{
                event.stopPropagation();
                event.preventDefault();
            }
        }
        
        function getUniqueId() {
            const chars =
            '1234567890' +
            'abcdefghijklmnopqrstuvwxyz' +
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

            const charsLen = chars.length; // 62

            const id = []
            for (let i = 0; i < 10; i++) {
                id.push(chars[(Math.round(Math.random() * 100)) % charsLen]);
            }
            return id.join('');
            
        }

        function removePhoto(id) {
            try {
                document.getElementById('photos').removeChild(document.getElementById(id));
            }
            catch(e) {
            
            }
            finally {
                event.stopPropagation();
                event.preventDefault();
            }
        }
    </script>
  </body>

  <footer>
 
  </footer>
</html>