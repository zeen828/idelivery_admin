<?php

namespace App\Admin\Controllers\System;

use App\Model\idelivery\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//use App\Lib\resize_img;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $allowed = array('png', 'jpg', 'gif');

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0)
        {
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowed))
            {
                echo '{"錯誤: 檔案格式錯誤"}';
                exit;
            }
            
            $name = uniqid();
            $ext = explode('.', $_FILES['file']['name']);
            $filename = $name . '.' . $ext[1];
            $destination = env('ADMIN_UPLOAD_PATH', '') . 'company/image/summernote/'. $filename; 

            $max_width = 640;
            $max_height = 360;

            list($width, $height, $type, $attr) = getimagesize($_FILES['file']['tmp_name']);
            if ($width > $height) {
                if ($width > $max_width) {
                    $height *= $max_width / $width;
                    $width = $max_width;
                }
            } else {
                if ($height > $max_height) {
                    $width *= $max_height / $height;
                    $height = $max_height;
                }
            }

            // $manager = new ImageManager(array('driver' => 'imagick'));
            // $image = Image::make($destination)->resize(300, 200);

            $image = $request->file('file');
            //Image::make($image->getRealPath())->resize($max_width, $max_height)->save($destination);
            //$img = Image::make($destination);
            $image_url = null;

            switch (env('ADMIN_UPLOAD_DISK', 'admin')) {
                case 's3':
                    $s3 = \Storage::disk('s3');
                    $status = $s3->put($destination, file_get_contents($image), 'public');
                    if (empty($status))
                    {
                        return false;
                    }
        
                    $image_url = sprintf("%s%s", env('ADMIN_UPLOAD_URL', ''), $destination);
                    break;

                case 'default':

                    break;
            }

            return $image_url;
        }

    }
      
}