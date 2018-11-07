# 文件
[laravel 5.5 英文版](https://laravel.com/docs/5.5)  
[Laravel 5.5 中文版](https://d.laravel-china.org/docs/5.5)
  
[Laravel-Admin 1.5 後台文件](http://laravel-admin.org/docs/#/)  

[Laravel 5.1 速查表](https://cs.laravel-china.org/)  

# 本機建置
修改.env

    DB_DATABASE=資料庫  
    DB_USERNAME=帳號  
    DB_PASSWORD=密碼  
    APP_URL=http://本機網址  

修改config/admin.php

    'upload'  => [
        'disk' => 'admin',
        'directory'  => [
            'image'  => 'images',
            'file'   => 'files',
        ],
        'host' => 'http://本機網址/upload/',
    ]

# 備註