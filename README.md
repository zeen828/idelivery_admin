# 文件
[composer 文件](https://getcomposer.ycnets.com/doc/03-cli.md#remove)  

[laravel 5.5 英文版](https://laravel.com/docs/5.5)  
[Laravel 5.5 中文版](https://d.laravel-china.org/docs/5.5)

[Laravel-Admin 1.5 後台文件](http://laravel-admin.org/docs/#/)  
[Laravel-Admin DEMO](http://laravel-admin.org/demo/auth/login)  
[Laravel-Admin DEMO GitHub](https://github.com/z-song/laravel-admin.org)

[Laravel-Admin 1.6 後台文件](http://laravel-admin.org/docs/#/)
[Laravel-Admin DEMO](http://demo.laravel-admin.org/auth/login)
[Laravel-Admin DEMO GitHub](https://github.com/z-song/demo.laravel-admin.org)

[AdminLTE 版型參考](https://adminlte.io/themes/AdminLTE/)

[Laravel 5.1 速查表](https://cs.laravel-china.org/)  

# 命名規則
    Controller  UserController
    Model       Admin_user = admin_user table
    Model       AdminUser  = 管理的會員行為(不是table)
    Migration   carate_table_name 建立table
    Migration   update_table_name_xxx 更新table

# 目錄規則::app/Admin/Controller
    Management  大後台用
    Company     總公司用
    Store       店家用

# 目錄規則::app/Model
    idelivery   資料庫名稱

# 目錄規則::AWS S3
    damaiapp
        idelivery
            call
                {store_id}取餐叫號HTML
            company
                html
                    {company_id}APP會員服務條款
                images
                    banner
                        {company_id}點餐大圖
                    carousel
                        {company_id}首頁輪播,POS子母畫面
                    exchanges
                        {company_id}兌換商品
                    menu_item
                        {company_id}菜色圖
                    sign
                        店家示意圖(取消搬移至店家)
                    summernote
                        即見所得編輯器
                    news
                        {company_id}活動訊息
            store
                menu_item
                    {store_id}菜色圖
                sign
                    {store_id}店家示意圖
    damaiapp.development

# 本機建置
下載專案套件

    composer install

    sudo chmod -R 777 project-name/storage
    
複製.env.example為.env
修改.env

    DB_DATABASE=資料庫  
    DB_USERNAME=帳號  
    DB_PASSWORD=密碼  
    APP_URL=http://本機網址  

建立Key

    php artisan key:generate

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
到專案目錄下執行建立資料庫與內容  

    php artisan migrate
清除內容再去測試機複製內容回來  
(或是直接去測試機複製結構+內容)  

# 小抄::Controller
建立  

    php artisan make:controller PageController

# 小抄::Admin::Controller
建立(model使用要自己先建立)

    php artisan admin:make Company/CompanyController --model=App\Model\idelivery\Company

# 小抄::Model
建立

    php artisan make:model Model/idelivery/Company

# 小抄::Model用法

    Model應用
    public function scopeConfirm($query, $country, $account)
    {
        return $query
            ->where('country', '=', $country)
            ->where('account', '=', $account);
    }
    model使用&檢查(單筆)
    $Member = Member::find($id);
    if(empty($Company)){
        ...
    }
    model使用&檢查(多筆)
    $Member = Member::Confirm($country, $account)->get();
    if($Member->isEmpty()){
        ...
    }
    model使用&檢查(多筆只取一筆)
    $Member = Member::Confirm($country, $account)->first();
    if(empty($Member)){
        ...
    }

# 小抄::Migration
建立migration檔案(win指定目錄要自己創目錄)

    php artisan make:migration 檔案描述(參考命名規則) --path="database/migrations/create"
    php artisan make:migration 檔案描述(參考命名規則) --path="database/migrations/update"
    php artisan make:migration 檔案描述(參考命名規則) --path="database/migrations/20180525_switch"

執行migration

    php artisan migrate --path="database/migrations/create"
    php artisan migrate --path="database/migrations/update"
    php artisan migrate --path="database/migrations/20180525_switch"

回復migration

    php artisan migrate:rollback --path="database/migrations/create"
    php artisan migrate:rollback --path="database/migrations/update"
    php artisan migrate:rollback --path="database/migrations/20180525_switch"

# 小抄::Seeder
建立

    php artisan make:seeder 檔案描述(參考命名規則)

運行

    php artisan db:seed
    php artisan db:seed --class=檔案描述
    php artisan db:seed --class=AdminTableSeeder

# 小抄::Composer
    composer create-project codeigniter/framework 專案目錄
    composer install
    composer update
    composer dump-autoload


###### 開發紀錄
    // 取得網址某一段值
    use Illuminate\Http\Request;
    public function index(Request $request)
    {
        var_dump($request->segment(1));
    }

# 小抄::GIT分支命名
    功能（feature）分支
    预发布（release）分支
    修补bug（fixbug）分支

# 小抄::GIT
    git clone https://xxxx/xx.git
    git pull
    git branch
    git branch -r
    git fetch origin
    git checkout -b <branch_name>
    # 在 checkout 命令給定 -b 參數執行，可以同時建立分支和切換。
    git checkout <branch_name>
    切換分支

    #删除本地分支
    git branch -d branch_name

    #刪除服務器上已被刪除遠端分支
    git remote prune origin

    # 切換到 issue1 分支。
    git checkout issue1 
    # Switched to branch 'issue1'
    
    #還原回主支線
    git reset --hard origin/master
    
    #還原到某個版本
    git log
    git reset --hard commit ID
    
    #還原到某個版本(推)
    git log --oneline
    git reset e12d8ef^
    
    #還原最後的commit
    git reset --hard HEAD

    #查看檔案差異
    git diff <file>

    #還原單一檔案
    git checkout <file>

    切到某個版號(透過git log查)，要返回就切回主線
    git checkout dd5302bcbd1dcd4b77ca84eabe9bac838b4fa0af

    <Fetch>
    執行 pull，遠端數據庫的內容會自動合併。但是，有時候只是想確認遠端數據庫的內容卻不是真的想合併，在這種情況下，請使用 fetch。
    
# 小抄::GIT建專案
    git init
    git add .
    git commit -m "first commit"
    git remote add origin https://will_lu@bitbucket.org/damaiapp/idelivery_www.git
    git push -u origin master
        OR
    git remote add origin git@bitbucket.org:rainy820605/laadmin.git
    git push -u origin master


# 小抄::資料庫備份
    備份
    mysqldump -u root -p voice>voice.sql
    還原
    mysql -u root -p voice<voice.sql

# 小抄::執行Laravel排程
    php artisan PushJob:pushjob
    需註冊在/app/Console/Kernel.php的$commands

# 小抄::Linux常用指令
#### 歷史指令
    history
    
#### crontab排程
    sudo crontab -e
    sudo /etc/init.d/cron restart

#### SSH連線
金鑰權限不能太高

    chmod 0600 Ubuntu16_PHP7.pem
透過金鑰連線

    ssh -i /home/ubuntu/aws_key/Ubuntu16_PHP7.pem ubuntu@ec2-13-115-205-87.ap-northeast-1.compute.amazonaws.com

#### ubuntu ln
在當下目錄做一個連結Models(目錄)

    ln -s /var/www/development/system/system_damaiapp/app/Models

#### 查LOG用
    tail -f file.log
    grep -r 'DB_MEMBER_HOST' /var/www/testing/*

#### 查檔案用
    find /目錄 -name "*.css"

#### CentOS(改系統提示)
##### 修改設定檔
vim ~/.bashrc

    export PS1="[\[\e[36m\]\u\[\e[m\]@\[\e[33m\]\[WU_TEST]\[\e[m\]\[[\e[1;31m\]\w\[\e[m\]]\e[1;32m\][\A\[\e[m\]]\[\e[31m\]\`parse_git_branch\`\[\e[m\]$ "

##### 重讀設定檔
source .bashrc

# 小抄windows 10 mklink
需要以系統管理員身分執行CMD才能執行

    mklink /d C:\Users\Damai_UX330C\Documents\Ampps\Laravel\system_member\app\Models C:\Users\Damai_UX330C\Documents\Ampps\Laravel\system_damaiapp\app\Models
    mklink /d 要建立目錄連結的目錄 連結來源

