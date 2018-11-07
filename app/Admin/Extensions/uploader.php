<?php
    // A list of permitted file extensions
    $allowed = array('png', 'jpg', 'gif','zip');

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0)
    {
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($extension), $allowed))
        {
            echo '{"錯誤: 檔案格式錯誤"}';
            exit;
        }
        
        //$name = md5(rand(100, 200));
        $name = uniqid();
        $ext = explode('.', $_FILES['file']['name']);
        $filename = $name . '.' . $ext[1];
        $destination = 'images/' . $filename; 
        $location = $_FILES["file"]["tmp_name"];
        move_uploaded_file($location, $destination);
        echo 'http://idelivery.admin.dev.damaiapp.com/public/upload/images/' . $filename;
    }
    else
    {
      echo  $message = '喔喔!  圖片上傳發生錯誤:  '.$_FILES['file']['error'];
    }
?>
