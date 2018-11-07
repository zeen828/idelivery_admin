<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\News;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class NewsController extends Controller
{
    use ModelForm;

    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '上架', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '下架', 'color' => 'default'),
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
            $content->header('活動訊息');
            $content->description('News');
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

            $news = News::find($id);
            if (empty($news) || empty($news->company_id) || $company_id != $news->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('活動訊息');
            $content->description('News');

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

            $content->header('活動訊息');
            $content->description('News');

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
        return Admin::grid(News::class, function (Grid $grid) {

            $grid->model()->where('company_id', '=', Session::get('company_id'))->where('type', '=', '3');

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            //$grid->id('ID')->sortable();

            $grid->column('title', '標題');
            $grid->column('description', '文字說明');
            $grid->column('image', '圖檔 (建議 400*300px)')->display(function ($image) {
                if (isset($image))
                {
                    return "<img src='" . env('ADMIN_UPLOAD_URL', '') . $image . "' width='50'>";
                }
                else
                {
                    return "";
                }
            });
            $grid->column('link', '活動連結')->display(function ($link) {
                return "<a href='{$link}'>" . $link . "</a>";
            });
            $grid->column('post_date', '發布日期');
            $grid->column('start_date', '起始日期')->sortable();
            $grid->column('end_date', '結束日期');
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
        return Admin::form(News::class, function (Form $form) {
            $company_id = Session::get('company_id');

            $form->hidden('company_id','公司編號')->default($company_id);
            $form->hidden('store_id','店家編號')->default(0);
            $form->hidden('type','類別')->default('3');

            $now = (new \DateTime())->format('Y-m-d H:i:s');
            $start = (new \DateTime())->format('Y-m-d 00:00:00');
            $end = (new \DateTime())->format('Y-m-d 23:59:59');

            $form->datetime('post_date', '發布日期時間')->default($now);
            $form->datetime('start_date', '起始日期時間')->default($start);
            $form->datetime('end_date', '結束日期時間')->default($end);

            $form->text('title', '標題')->rules('required|max:30')->placeholder('限30字');
            $form->textarea('description', '文字說明 (選填)')->rows(10);
            $form->image('image', 'DM圖片 (選填)')->uniqueName()->rules('max:900')
                ->move(env('ADMIN_UPLOAD_PATH', '') . 'company/image/news/'.$company_id);
            $form->url('link', '活動連結 (選填)')->rules('nullable');
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);
        });
    }

}
