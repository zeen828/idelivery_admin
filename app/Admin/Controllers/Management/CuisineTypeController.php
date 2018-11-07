<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Model\idelivery\CuisineType;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;

class CuisineTypeController extends Controller
{
    use ModelForm;

    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '關閉', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商店管理 > 營業型態設定');
            $content->description(trans('idelivery.admin.index'));

            //幫助
            $box1 = new Box(trans('idelivery.box.help.title'), '內容');
            $content->row($box1->collapsable());

            $content->body($this->grid());
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

            $content->header('商店管理 > 營業型態設定');
            $content->description(trans('idelivery.admin.edit'));

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

            $content->header('商店管理 > 營業型態設定');
            $content->description(trans('idelivery.admin.create'));

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(CuisineType::class, function (Grid $grid) {
            $grid->model()->orderBy('sort_by', 'asc');
            $grid->column('name', '型態名稱');
            $grid->column('status', '狀態')->switch($this->status_arr);
            $grid->sort_by('順序')->orderable();
            $grid->disableFilter();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(CuisineType::class, function (Form $form) {
            //$form->hidden('sort_by','編號')->default(CuisineType::getNewSN());           
            $form->text('name', '型態名稱')->rules('required|max:25')->placeholder('限25字');
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);   
        });
    }

    
}
?>