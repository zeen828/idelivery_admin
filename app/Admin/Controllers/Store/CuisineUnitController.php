<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\CuisineUnit;
use App\Model\idelivery\CuisineGroup;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class CuisineUnitController extends Controller
{
    use ModelForm;

    private $multiple_arr = array(
        'on'  => array('value' => 1, 'text' => '多選', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '單選', 'color' => 'default'),
    );

    private $required_arr = array(
        'on'  => array('value' => 1, 'text' => '必填', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '選填', 'color' => 'default'),
    );


    private $default_arr = array(
        'on'  => array('value' => 1, 'text' => '預選', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '非預選', 'color' => 'default'),
    );

    private $status_arr = array(
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
            $store_id = Session::get('store_id');
            $content->header(trans('idelivery.cuisine.unit.config'));
            $content->description(trans('idelivery.admin.index'));
            if (empty($store_id)) {
                $box3 = new Box('提示', '請選擇店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
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
            $store_id = Session::get('store_id');
            $cuisine_unit = CuisineUnit::find($id);
            if (empty($cuisine_unit) || empty($cuisine_unit->store_id) || $store_id != $cuisine_unit->store_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('idelivery.cuisine.unit.config'));
            $content->description(trans('idelivery.admin.edit'));
            if (empty($store_id)) {
                $box3 = new Box('提示', '請選擇店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form($id)->edit($id));
            }
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
            $store_id = Session::get('store_id');
            $content->header(trans('idelivery.cuisine.unit.config'));
            $content->description(trans('idelivery.admin.create'));
            if (empty($store_id)) {
                $box3 = new Box('提示', '請選擇店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form());
            }
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(CuisineUnit::class, function (Grid $grid) {

            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');
            $grid->model()->where('company_id', $company_id)
                        ->where('store_id', $store_id)
                        ->orWhere(function ($query) use ($company_id) {
                            $query->where('company_id', '=', $company_id)
                                ->where('store_id', 0);
                        })->orderby('sort_by', 'asc');

            $grid->group(trans('idelivery.cuisine.group.name'))->pluck('group_name')->label();
            $grid->column('unit_name', trans('idelivery.cuisine.unit.title'));
            $grid->column('is_multiple', '是否多選')->switch($this->multiple_arr);
            $grid->column('is_required', '是否必填')->switch($this->required_arr);
            $grid->sort_by('順序')->orderable();
            $grid->disableFilter();
            $grid->disableRowSelector();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(CuisineUnit::class, function (Form $form) {
            $form->hidden('company_id','品牌編號')->default(Session::get('company_id'));           
            $form->hidden('store_id','店家編號')->default(Session::get('store_id')); 
            
            $form->multipleSelect('group', trans('idelivery.cuisine.group.name'))->options(
                CuisineGroup::where('company_id', Session::get('company_id'))
                ->where('store_id', Session::get('store_id'))
                ->orWhere(function ($query) {
                    $query->where('company_id', '=', Session::get('company_id'))
                        ->where('store_id', '=', 0);
                })
                ->pluck('group_name', 'id')
            );
                            
            $form->text('unit_name', trans('idelivery.cuisine.unit.title'))->rules('required');
            $form->switch('is_multiple', '是否多選')->states($this->multiple_arr)->default(2); 
            $form->switch('is_required', '是否必填')->states($this->required_arr)->default(2); 

            $default_arr = $this->default_arr;
            $status_arr = $this->status_arr;

            $form->hasMany('attrs', '選項', function (Form\NestedForm $form) use ($default_arr, $status_arr) {
                $form->hidden('company_id','品牌編號')->default(Session::get('company_id'));           
                $form->hidden('store_id','店家編號')->default(Session::get('store_id')); 

                $form->text('attr_name', trans('idelivery.cuisine.attr.title'));
                $form->number('extra_price', '額外價格');
                $form->switch('is_default', '是否預選')->states($default_arr)->default(2);
                $form->switch('status', '狀態')->states($status_arr)->default(1);
            });
        });
    }
}
