<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Models\idelivery\Store;
use App\Model\idelivery\District_tw;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class StoreConfigController extends Controller
{
    use ModelForm;
    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '正常', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商店管理 > ' . trans('idelivery.store_config.upload'));
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

            $content->header('商店管理 > ' . trans('idelivery.store_config.upload'));
            $content->description(trans('idelivery.admin.edit'));

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Store::class, function (Grid $grid) {
            // 禁止功能
            $grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            // $grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('name', trans('idelivery.store.name'));
            $grid->column('upload_date', '上傳日期')->display(function () {
                $html = [];

                $pathConfig = env('STORE_CONFIG_JSON_PATH', '') ."/{$this->id}/config.json";
                if (file_exists($pathConfig)) {
                    $html[] = "<span style=\"color: red;\"><b>config.json</b></span> 最後上傳時間為 <span style=\"color: red;\"><b>" . date ("Y-m-d H:i:s.", filemtime($pathConfig)) .'</b></span>';
                }

                return implode('<br/>', $html);
            });
            
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append('<a href="store_config/'. $actions->getKey() .'/edit"><i class="fa fa-upload"></i></a>');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Store::class, function (Form $form) {
            $form->setAction('/admin/system/store_config_upload');
            $form->hidden('id');
            $form->html('<div class="col-sm-8"><input type="file" name="config_json"></div>', '店家設定檔');
        });
    }
}
