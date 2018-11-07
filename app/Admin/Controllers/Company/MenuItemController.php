<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\MenuItem;
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
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;

use App\Models\idelivery\Menu_store_item;
use App\Models\idelivery\Menu_item_unit;
use App\Models\idelivery\Menu_size;
use App\Models\idelivery\Menu_item;

class MenuItemController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '關閉', 'color' => 'default'),
    );

    private $status_size_arr = array(
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '關閉', 'color' => 'default'),
    );

    private $selected_arr = array(
        'on'  => array('value' => 1, 'text' => '是', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '否', 'color' => 'default'),
    );

    private $hidden_arr = array(
        'on'  => array('value' => 0, 'text' => '顯示', 'color' => 'primary'),
        'off' => array('value' => 1, 'text' => '隱藏', 'color' => 'default')
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
            $content->header(trans('idelivery.cuisine.item.config'));
            $content->description(trans('idelivery.admin.index'));

            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬公司品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->grid());
            }
        });
    }

    /**
     * Delete interface.
     *
     * @param $id
     * @return Content
     */
    public function destroy($id)
    {
        Menu_store_item::where('item_id', $id)
            ->delete();

        Menu_item_unit::where('item_id', $id)
            ->delete();

        Menu_size::where('item_id', $id)
            ->delete();

        $menu_item = Menu_item::find($id);
        if (!empty($menu_item))
        {
            $menu_item->delete();
        }

        return array('status' => true, 'message' => '刪除成功 !');
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
            Session::put('menu_item_id', $id);
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');

            $menu_item = MenuItem::find($id);
            if (empty($menu_item) || empty($menu_item->company_id)
                || $company_id != $menu_item->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('idelivery.cuisine.item.config'));
            $content->description(trans('idelivery.admin.edit'));

            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬公司品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form()->edit($id));
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
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.cuisine.item.config'));
            $content->description(trans('idelivery.admin.create'));

            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬公司品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form());
            }
        });
    }

    public function updatePrice()
    {
        $response = array();
        if (!empty($_POST['val']) && !empty($_POST['key'])) {
            $vals = $_POST['val'];
            $keys = $_POST['key'];
            if (count($vals) !== count($keys)) {
                $response['status'] = 'error';
                $response['message'] = '資料錯誤 !';
            }

            $result = MenuItem::updatePrice($keys, $vals);
            if ($result == false) {
                $response['status'] = 'error';
                $response['message'] = '資料更新錯誤 !';
            }
        }

        $response['status'] = 'success';
        $response['message']= '資料更新成功 !';
        echo json_encode($response);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::script('$(".price_submit").on("click", function () {
            var values = [];   
            var keys = []        
            var closest_div = $(this).closest(".price_input");
            closest_div.find("input[id^=\'price_\']").each(function() {
                values.push($(this).val());
                keys.push($(this).data("id"));
            });

            $.ajax({
                url: "/admin/store/menu_item/ajax",
                type: "POST",
                dataType : "json",
                data: {key: keys, val: values},
                cache: false,
                headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                success: function(data) {
                    toastr.success(data.message);
                },
                error: function() {
                    alert("error !");
                }
            });
            
            return false;        
        });
        
        
        // $("#export_data").on("click", function () {
            
        //     $.ajax({
        //         url: "/admin/company/menu_item/export",
        //         type: "POST",
        //         dataType : "json",
        //         cache: false,
        //         headers: { "X-CSRF-Token" : $("#csrf_token").val() },
        //         success: function(data) {
        //             toastr.success(data.message);
        //             window.location.replace("/admin/company/menu_item");
        //         },
        //         error: function() {
        //             alert("error !");
        //         }
        //     });
            
        //     return false; 
        
        // });');

        return Admin::grid(MenuItem::class, function (Grid $grid) {

            $grid->tools(function ($tools) {
                $tools->append('<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />
                <a href="/admin/company/menu_release" id="export_data" type="button" 
                class="btn btn-sm btn-primary pull-right"><i class="fa fa-cutlery" aria-hidden="true"></i>&nbsp;&nbsp;釋出餐點</a>');
            });

            $grid->model()->where('company_id', Session::get('company_id'))
                    ->where('store_id', 0)->orderby('sort_by', 'asc');

            $grid->column('group_id', trans('idelivery.cuisine.group.name'))->display(function () {
                return CuisineGroup::getGroupName($this->group_id);
            });

            $grid->column('name', trans('idelivery.cuisine.item.name'))->editable();
            $grid->intro('簡介');

            $grid->column('picture', '圖片')->display(function () {
                $result = '';

                if (!empty($this->picture)) {
                    $result = "<img src='". env('ADMIN_UPLOAD_URL', '') . $this->picture ."' width = '50px'>";
                }

                return $result;
            });
           
            $grid->column('size', '份量/價格')->style("max-width:100px")->display(function () {

                $result = $this->size;

                if (!empty($result)) {
                    $str = '<form class="form-group" style="text-align: left">';
                    $str .= '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />';
                    $str .= '<div class="price_input">';
                    foreach ($result as $row) {
                        if (!empty($row->size_name)) {
                            if (!empty($row->price)) {
                                $str .= '<div class="row">'
                                            .'<div class="col-xs-4">'
                                                .'<span class="label label-success">'.$row->size_name.'</span>'
                                            .'</div>'
                                            .'<div class="col-xs-8">'
                                                .'<input type="text" id="price_'.$row->id.'" data-id='.$row->id.' name="price_'.$row->id.'" value="'.$row->price.'" class="form-control" placeholder="限數字" />'
                                            .'</div>'
                                        .'</div>';
                            } else {
                                $str .= '<div class="row">'
                                            .'<div class="col-xs-12">'
                                                .'<span class="label label-success">'.$row->size_name.'</span>'
                                            .'</div>'
                                        .'</div>';
                            }
                        } else {
                            if (!empty($row->price)) {
                                $str .= '<div class="row">'
                                            .'<div class="col-xs-12">'
                                                .'<input type="text" id="price_'.$row->id.'" data-id='.$row->id.' name="price_'.$row->id.'" value="'.$row->price.'" class="form-control" placeholder="限數字" />'
                                            .'</div>'
                                        .'</div>';
                            }
                        }
                    }
                }

                $str .= '<p></p><button type="submit" class="btn btn-info btn-xs pull-left price_submit"><i class="fa fa-save"></i>&nbsp;保存'
                        .'</button><button type="reset" class="btn btn-warning btn-xs pull-left" style="margin-left:10px;">'
                        .'<i class="fa fa-trash"></i>&nbsp;復原</button></div></form>';

                return $str;
            });

            $grid->unit(trans('idelivery.cuisine.unit.title'))->pluck('unit_name')->label();
            $grid->column('points', trans('idelivery.cuisine.item.points'));
            $grid->column('hidden', 'App隱藏')->switch($this->hidden_arr);
            $grid->column('status', '狀態')->switch($this->status_arr);
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
    protected function form()
    {
        // Admin::script('$(".grid-expand").trigger("click");');

        return Admin::form(MenuItem::class, function (Form $form) {

            $form->tools(function (Form\Tools $tools) {
                // Disable list btn
                $tools->disableListButton();
                $tools->add('<a href="/admin/company/set/cuisine_group" class="btn btn-sm btn-default pull-right"><i class="fa fa-list"></i>&nbsp;列表</a>');
            });

            $form->hidden('company_id','品牌編號')->default(Session::get('company_id'));
            $form->hidden('store_id','品牌編號')->default(0);

            $group_id = MenuItem::getGroupID(Session::get('menu_item_id'));

            $form->select('group_id', trans('idelivery.cuisine.group.name'))->options(
                CuisineGroup::where('company_id', Session::get('company_id'))->where('store_id', 0)->pluck('group_name', 'id')
            )->default($group_id);

            $form->text('name', trans('idelivery.cuisine.item.name'))->rules('required|max:25')->placeholder('限25字');
            $form->textarea('intro', '簡介')->rows(3);
            $form->image('picture', '圖片')->uniqueName()->rules('max:900')
                ->move(env('ADMIN_UPLOAD_PATH', '') . 'company/image/menu_item/' . Session::get('company_id'));
            $form->divide();

            $unit = json_decode(MenuItem::getUnit(Session::get('company_id'), Session::get('store_id')), true);

            $new = false;
            if (request()->isMethod('POST')) {
                $new = true;
            }

            $options_list = array();

            if (!empty($unit)) {
                $unit_id_list = array();

                foreach ($unit as $row) {
                    $unit_id_list[] = $row['id'];
                }

                $attr = json_decode(MenuItem::getAttr($unit_id_list), true);
                $i = 0;

                foreach ($unit as $unit_row) {
                    $attr_arr = array();
                    $attr_opt = array();
                    $options = array();
                    $default = 0;

                    foreach ($attr as $val) {
                        if ($unit_row['id'] == $val['unit_id']) {
                            $attr_opt[$val['id']] = $val['attr_name'];

                            if ($new == true) {
                                if ($val['is_default'] == '1') {
                                    $default = $val['id'];
                                }
                            } else {
                                $spec = json_decode(MenuItem::getSpecRelation(Session::get('menu_item_id')), true);
                                if (!empty($spec)) {
                                    foreach ($spec as $spec_row) {
                                        if( !empty($spec_row['attribute'])) {
                                            foreach ($spec_row['attribute'] as $attr_row) {
                                                if ($val['id'] == $attr_row['id']) {
                                                    if ($attr_row['selected'] == true) {
                                                        $default = $attr_row['id'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $attr_arr = array(
                                'id' => $val['id'],
                                'title' => $val['attr_name'],
                                'subtitle' => '',
                                'selected' => $val['is_default'] == '1' ? true : false,
                                'extra_price' => $val['extra_price']
                            );

                            $options[] = $attr_arr;
                        }
                    }

                    $options_list[] = array(
                        'unit_id'     => $unit_row['id'],
                        'unit_title'  => $unit_row['unit_name'],
                        'is_multiple' => $unit_row['is_multiple'],
                        'required'    => $unit_row['is_required'],
                        'attribute'   => (empty($options)||$options == "[]") ? null : $options,
                    );
                }
            }
            
            $form->multipleSelect('unit', trans('idelivery.cuisine.unit.title'))->options(
                CuisineUnit::where('company_id', Session::get('company_id'))->where('store_id', 0)->pluck('unit_name', 'id')
             );

            $form->divide();

            $form->hasMany('size', '份量價格', function (Form\NestedForm $form) {
                $form->hidden('id','份量編號');
                $form->hidden('item_id','餐點編號');
                $form->text('size_name', '份量名稱')->rules('nullable');
                $form->number('price', '價格')->rules('required');
                $form->switch('is_selected', '預設選取')->states($this->selected_arr)->default(2);
                $form->switch('status', '份量狀態')->states($this->status_size_arr)->default(1);
            });

            $form->divide();
            $form->number('points', '獲得點數')->min(0);
            $form->switch('hidden', 'App隱藏')->states($this->hidden_arr);
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);

            $form->saving(function (Form $form) {
                $sizes = $form->size;
                $is_price_null = false;
                $selected_cnt = 0;
                if (!empty($sizes)) {
                    foreach ($sizes as $val) {
                        if (empty($val['price'])) {
                            $is_price_null = true;
                        }

                        if ($val['is_selected'] == 'on') {
                            $selected_cnt = $selected_cnt + 1;
                        }
                    }
                } else {
                    $is_price_null = true;
                }

                if ($is_price_null) {
                    admin_toastr('份量價格不得為空!','warning');
                    return back();
                }

                if ($selected_cnt == 0) {
                    admin_toastr('份量預設選取不得全為否!','warning');
                    return back();
                }

                if ($selected_cnt > 1) {
                    admin_toastr('份量預設選取數量超過限制!','warning');
                    return back();
                }

            });

            $form->saved(function (Form $form) use ($options_list) {
                $menu_item_unit = array();
                $unit_select = array();
                $spec = json_decode(MenuItem::getSpecRelation(Session::get('menu_item_id')), true);

                foreach ($options_list as $rows) {
                    foreach ($form->unit as $key => $val) {
                        if ($val == $rows['unit_id']) {
                            $menu_item_unit[] = array(
                                'item_id' => $form->model()->id, 
                                'unit_id' => $val
                            );

                            $unit_select[] = $rows;
                        }
                    }
                }

                MenuItem::addMenuItemUnit($form->model()->id, $menu_item_unit);

                if (!empty($unit_select)) {
                    MenuItem::updateSpecRelation($form->model()->id, json_encode($unit_select));
                } else {
                    MenuItem::updateSpecRelation($form->model()->id, json_encode(array()));
                }

                Session::forget('menu_item_id');

                $size_ids = array();
                $size_names = array();
                
                foreach ($form->size as $row) {
                    $size_ids[] = $row['id'];
                    $size_names[] = $row['size_name'];
                }

                //不知為何沒有update size_name???
                MenuItem::updateSizeName($size_ids, $size_names);

                // $response['status'] = 'success';
                // $response['message']= '資料更新成功 !';

                //echo json_encode($response);

                return redirect('/admin/company/set/cuisine_group');
            });
        });

    }
}