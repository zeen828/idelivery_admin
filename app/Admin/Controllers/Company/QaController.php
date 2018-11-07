<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\Qa;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class QaController extends Controller
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
            $content->header('常見問題');
            $content->description('Question and Answer');
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

            $qa = Qa::find($id);
            if (empty($qa) || empty($qa->company_id) || $company_id != $qa->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('常見問題');
            $content->description('Question and Answer');
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
            $content->header('常見問題');
            $content->description('Question and Answer');
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
        return Admin::grid(Qa::class, function (Grid $grid) {

            $type = '1';

            //$grid->id('編號');

            $grid->model()->where('company_id', '=', Session::get('company_id'))->orderBy('sn', 'asc');

            $grid->column('question', '問題')->editable();
            $grid->column('answer', '答案')->editable();
            $grid->sn('順序')->orderable();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(Qa::class, function (Form $form) {

            $type = '1';

            //$new_sn = Qa::getNewSN(Session::get('company_id'));

            $form->model()->where('company_id', '=', Session::get('company_id'));

            $form->hidden('company_id','店家編號')->default(Session::get('company_id'));
            $form->hidden('store_id','店家編號')->default(Session::get('store_id'));
            $form->hidden('type','類別')->default($type);
            // $form->hidden('sn','編號')->default($new_sn);
            // $form->display('sn', '編號')->value($new_sn);

            $form->text('question', '問題')->rules('required');
            $form->textarea('answer', '答案')->rows(10);
        });
    }

    
}
