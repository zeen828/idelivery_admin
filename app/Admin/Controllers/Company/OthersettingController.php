<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\OtherSetting;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class OthersettingController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header('其他設定');
            $content->description('Other Setting');
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

            $othersetting = OtherSetting::find($id);
            if (empty($othersetting) || empty($othersetting->company_id)
                || $company_id != $othersetting->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('其他設定');
            $content->description('Other Setting');

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

            $content->header('其他設定');
            $content->description('Other Setting');

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
        return Admin::grid(OtherSetting::class, function (Grid $grid) {

            $grid->model()->where('company_id', '=', Session::get('company_id'))->orderBy('sn', 'asc');
            $grid->id('ID');
            $grid->column('reason', '無法接單原因')->editable();
            $grid->sn('順序')->orderable();
            
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(OtherSetting::class, function (Form $form) {

            //$store_id = Session::get('store_id');
            //$new_sn = OtherSetting::getNewSN(Session::get('company_id'));
            $other_setting = OtherSetting::where("company_id", Session::get('company_id'))
                ->orderBy('sn', 'desc')
                ->first();

            $new_sn = 1;
            if (!empty($other_setting))
            {
                $new_sn = $other_setting->sn + 1;
            }

            $form->hidden('company_id','公司編號')->default(Session::get('company_id'));
            $form->hidden('store_id','店家編號')->default(Session::get('store_id'));
            $form->hidden('sn','編號')->default($new_sn);
            $form->display('sn', '編號')->value($new_sn); //display無法寫回資料表
            $form->text('reason', '原因')->rules('required|max:30')->placeholder('限30字');

        });
    }

}
