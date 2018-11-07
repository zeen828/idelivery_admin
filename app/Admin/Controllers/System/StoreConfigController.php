<?php

namespace App\Admin\Controllers\System;

use App\Model\idelivery\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\idelivery\Store;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class StoreConfigController extends Controller
{
    public function upload(Request $request)
    {
        $allowed = array('json');

        if (isset($_FILES['config_json']) && $_FILES['config_json']['error'] == 0) 
        {
            $extension = pathinfo($_FILES['config_json']['name'], PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $allowed))
            {
                $destPath = env('STORE_CONFIG_JSON_PATH', '') . "/{$request->id}/config.json";

                if (file_exists($destPath)) {
                    unlink($destPath);
                }

                if (!is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0777, true);
                }

                move_uploaded_file($_FILES['config_json']['tmp_name'], $destPath);
            }
        }

        return redirect('/admin/management/set/store_config');
    }
}