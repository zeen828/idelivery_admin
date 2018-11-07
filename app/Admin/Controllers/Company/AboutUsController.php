<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\Setting;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Session;

class AboutUsController extends Controller
{
    use ModelForm;

    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '已選', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '未選', 'color' => 'default'),
    );


    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header('關於我們');
            $content->description('About Us');
            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            }else{
                $content->body($this->grid());
            }
        });
    }
    
    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $company_id = Session::get('company_id');

            $setting = Setting::find($id);
            if (empty($setting) || empty($setting->company_id) || $company_id != $setting->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->body($this->form()->edit($id));

        });

    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->body($this->form());

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function grid()
    {
        return Admin::grid(Setting::class, function (Grid $grid) {

            $grid->model()->where('company_id', '=', Session::get('company_id'))->where('type', '=', '1');
            $grid->model()->orderBy('id', 'asc');

            $grid->id('編號')->sortable();

            $grid->column('title', '標題')->editable();

            // $grid->column('content', '內容')->style('width: 150px;')->display(function ($content) {
            //     return html_entity_decode($content);
            // });
            
            $grid->column('status', '狀態')->switch($this->status_arr);

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Setting::class, function (Form $form) {

            $type = '1';

            $form->hidden('company_id','公司編號')->default(Session::get('company_id'));
            $form->hidden('store_id','店家編號')->default(Session::get('store_id'));
            $form->hidden('type','類別')->default($type);

            $form->text('title', '標題')->rules('required|max:100')->placeholder('限100字');
            $form->summernote('content', '內容');
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);            

        });
    }

}
