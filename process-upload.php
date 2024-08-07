<?php
session_start();
require __DIR__ . '/Database.php';
if(isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == 0){
    $imageName = basename($_FILES['fileUpload']['name']);
    // place to move the file to
    $uploadTo = './images/' . $imageName;

    // only accept .png and .jpg and check content type
    if(!preg_match("/\.(png|jpg)$/i", $imageName) || strpos($_FILES['fileUpload']['type'], 'image/') === false){
        echo "Only .png and .jpg files<br>";
        echo '<a href="./views/user.php">Return..</a>';
        exit;
    }

    // check if the file is an image
    if(getimagesize($_FILES['fileUpload']['tmp_name']) !== false){
        // move the file
        if(move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploadTo)){
            // connect to database
            $database = new Database('localhost', 'websecuritydb', 'root', '');
            if(!$database->connect()){
                exit;
            }
            // add image name to db
            $sql = 'UPDATE users SET image=:image WHERE username=:username';
            $params = [':username'=>$_SESSION['userinfo']['username'], ':image'=>$imageName];
            Database::query($sql, $params);
            
            // if there was a picture before, delete it
            if($_SESSION['userinfo']['image'] != NULL){
                try{
                    unlink('./images/'.$_SESSION['userinfo']['image']);
                }
                catch(Error $e){
                    echo 'error in deleting image';
                }
            }

            $_SESSION['userinfo']['image'] = $imageName;
            echo "The file ". htmlspecialchars($imageName). " has been uploaded!";
        }
        else{
            echo "There was an error in uploading the file";
        }
    }
    else{
        echo "Only Images.";
    }
}
else{
    echo "no file uploaded...";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image upload</title>
</head>
<body><br>
<a href="./views/user.php">Return..</a>
</body>
</html>
