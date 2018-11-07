<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\MenuStoreItem;
use App\Model\idelivery\MenuItem;
use App\Model\idelivery\Store;
use App\Model\idelivery\CuisineGroup;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Table;

class MenuStoreItemController extends Controller
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

    private $menu_item_hidden = array(
        'on'  => array('value' => 0, 'text' => '顯示', 'color' => 'primary'),
        'off' => array('value' => 1, 'text' => '隱藏', 'color' => 'default')
    );


    public function index()
    {
        return Admin::content(function (Content $content) {

            $store_id = Session::get('store_id');
            $content->header('菜單管理');
            $content->description('Menu Management');

            if(empty($store_id)) 
            {
                $box3 = new Box('提示', '請選擇店家!!');
                $content->row($box3->removable()->style('warning'));
            }
            else
            {
                // $store = Store::getStore(Session::get('company_id'));
                // $str = '';
                // if (!empty($store))
                // {
                //     $str = '<div class="row">
                //             <div class="col-md-6  pull-right" style="margin-right: 10px">
                //             <div class="input-group">';
                //     $str .= '<select class="form-control store_select" style="width: 100%;" name="store_select[]" 
                //                  multiple="multiple" data-placeholder="選擇 欲釋出菜單店家">';

                //     foreach ($store as $val)
                //     {
                //         $str .= '<option value="'.$val->id.'" >'.$val->name.'</option>';
                //     }

                //     $str .= '</select>
                //             <span class="input-group-btn">
                //             <button class="btn btn-primary" type="button" id="all2store"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>
                //             </span>
                //             </div>
                //             </div>                            
                //             </div>';
                // }

                // $box = new Box('釋出所有菜單品項',  $str);
                // $rows = [
                //     ['釋出所有菜單品項', $str],
                // ];
                // $table = new Table([], $rows);
    
                // $content->row($table);
    
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
            $menu_store_item = MenuStoreItem::find($id);
            if (empty($menu_store_item) || empty($menu_store_item->store_id)
                || $store_id != $menu_store_item->store_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('菜單管理');
            $content->description('Menu Management');

            Session::put('menu_item_id', $id);

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
            $content->header('菜單管理');
            $content->description('Menu Management');
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
        Admin::script('
            $("#all2store").on("click", function () {
                $.ajax({
                    url: "/admin/store/set/menu_import/import_all",
                    type: "POST",
                    dataType : "json",
                    cache: false,
                    headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                    success: function(data) {
                        toastr.success(data.message);
                        window.location.replace("/admin/store/set/menu_store_item");
                    },
                    error: function() {
                        alert("匯入錯誤 !");
                    }
                });
                
                return false; 
            });'
        );

        return Admin::grid(MenuStoreItem::class, function (Grid $grid) {
            $grid->tools(function ($tools) {
                $store = Session::get('store_id');
                $str = '';
                if (!empty($store))
                {
                    $str = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />
                            <div class="form-inline pull-right">
                            <button class="btn btn-primary" type="button" id="all2store"><i class="fa fa-download" aria-hidden="true"></i> 匯入所有餐點品項</button>
                            </div>';
                }

                $tools->append($str);
            });

            $grid->model()->getStoreItem(Session::get('store_id'))->orderby('sort_by', 'asc');
            $grid->column('group_id', '餐點分類名稱')->display(function () {
                return CuisineGroup::getGroupName($this->group_id);
            });

            $grid->name('品項名稱');
            $grid->column('picture', '圖片')->display(function () {
                $result = '';
                if (!empty($this->picture))
                {
                    $result = "<img src='". env('ADMIN_UPLOAD_URL', '') . $this->picture ."' width = '50px'>";
                }

                return $result;
            });
           
            $grid->column('size', '份量/價格')->style("max-width:100px")->display(function () {
                $sizes = MenuStoreItem::getMenuSize($this->item_id);
                $str = '';
                if (!empty($sizes))
                {
                    foreach ($sizes as $row)
                    {
                        if (!empty($row->size_name))
                        {
                            if (!empty($row->price))
                            {
                                $str .= '<div class="row">'
                                        .'<div class="col-xs-4">'
                                        .'<span class="label label-success">'.$row->size_name.'</span>'
                                        .'</div>'
                                        .'<div class="col-xs-8">'
                                        .'<span class="label label-primary">'.$row->price.'元</span>'
                                        .'</div>'
                                        .'</div><p></p>';
                            }
                            else
                            {
                                $str .= '<div class="row">'
                                        .'<div class="col-xs-12">'
                                        .'<span class="label label-success">'.$row->size_name.'</span>'
                                        .'</div>'
                                        .'</div><p></p>';
                            }
                        }
                        else
                        {
                            if (!empty($row->price))
                            {
                                $str .= '<div class="row">'
                                        .'<div class="col-xs-12">'
                                        .'<span class="label label-primary">'.$row->price.'元</span>'
                                        .'</div>'
                                        .'</div>';
                            }
                        }
                    }
                }

                return $str;
            });

            $grid->column('unit', '附加選項')->display(function () {

                $units = MenuStoreItem::getItemUnit($this->item_id);
                $str = '';

                if (!empty($units))
                {
                    foreach ($units as $row)
                    {
                        $str .= "<span class='label label-success'>".$row->unit_name."</span><br/>";
                    }
                }

                return $str;
            });

            $grid->sort_by('順序')->orderable();
            $grid->column('status', '狀態')->switch($this->status_arr);

            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableActions();

            $grid->disableFilter();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(MenuItem::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                $tools->disableListButton();
            });

            $form->hidden('company_id','品牌編號')->default(Session::get('company_id'));  
            $form->hidden('store_id','店家編號')->default(Session::get('store_id'));

            $group_id = MenuItem::getGroupID(Session::get('menu_item_id'));

            $form->select('group_id', '群組名稱')->options(
                CuisineGroup::where('company_id', Session::get('company_id'))
                ->where('store_id', Session::get('store_id'))
                ->orWhere(function ($query) {
                    $query->where('company_id', '=', Session::get('company_id'))
                        ->where('store_id', '=', 0);
                })
                ->pluck('group_name', 'id')
            )->default($group_id);

            $form->text('name', '品項名稱')->rules('required|max:25')->placeholder('限25字');
            $form->image('picture', '圖片')->uniqueName()->rules('max:900')
                ->move(env('ADMIN_UPLOAD_PATH', '') . 'store/image/menu_item/' . Session::get('store_id'));
            $form->divide();

            $unit = json_decode(MenuItem::getUnit(Session::get('company_id'), Session::get('store_id')), true);
            
            $new = false;
            if (request()->isMethod('POST'))
            {
                $new = true;
            }

            $options_list = array();

            if (!empty($unit))
            {
                $unit_id_list = array();

                foreach ($unit as $row)
                {
                    $unit_id_list[] = $row['id'];
                }

                $attr = json_decode(MenuItem::getAttr($unit_id_list), true);
                $i = 0;

                foreach ($unit as $unit_row)
                {
                    $attr_arr = array();
                    $attr_opt = array();
                    $options = array();
                    $default = 0;

                    foreach ($attr as $val)
                    {
                        if ($unit_row['id'] == $val['unit_id'])
                        {
                            $attr_opt[$val['id']] = $val['attr_name'];

                            if ($new == true)
                            {
                                if ($val['is_default'] == '1')
                                {
                                    $default = $val['id'];
                                }
                            }
                            else 
                            {
                                $spec = json_decode(MenuItem::getSpecRelation(Session::get('menu_item_id')), true);
                                if (!empty($spec))
                                {
                                    foreach ($spec as $spec_row)
                                    {
                                        foreach ($spec_row['attribute'] as $attr_row)
                                        {
                                            if ($val['id'] == $attr_row['id'])
                                            {
                                                if ($attr_row['selected'] == true)
                                                {
                                                    $default = $attr_row['id'];
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
                        'unit_id'       => $unit_row['id'],
                        'unit_title'    => $unit_row['unit_name'],
                        'is_multiple'   => $unit_row['is_multiple'],
                        'required'      => $unit_row['is_required'],
                        'attribute'     => $options
                    );
                }
            }
            
            $form->multipleSelect('unit', '附加選項')->options(
                CuisineUnit::where('company_id', Session::get('company_id'))
                    ->where('store_id', Session::get('store_id'))
                    ->orWhere(function ($query) {
                        $query->where('company_id', '=', Session::get('company_id'))
                            ->where('store_id', '=', 0);
                    })
                    ->pluck('unit_name', 'id')
             );

            $form->ignore('spec_relation');
            $form->divide();

            $form->hasMany('size', '份量價格', function (Form\NestedForm $form)  {
                $form->hidden('id','份量編號');
                $form->hidden('item_id','餐點編號')->default(Session::get('menu_item_id'));
                $form->text('size_name', '份量名稱')->rules('nullable');
                $form->number('price', '價格')->rules('required');
                $form->switch('is_selected', '預設選取')->states($this->selected_arr)->default(2);
                $form->switch('status', '份量狀態')->states($this->status_size_arr)->default(1);
            });

            $form->divide();
            $form->switch('hidden', 'App隱藏')->states($this->menu_item_hidden)->default(0);
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);   

            $form->saving(function (Form $form) {

                $size_set = $form->size;
                $is_price_null = false;
                if (!empty($sizes))
                {
                    foreach ($sizes as $key => $val)
                    {
                        if ($key == 'price' && empty($val))
                        {
                            $is_price_null = true;
                        }
                    }
                }
                else
                {
                    $is_price_null = true;
                }

                if ($is_price_null)
                {
                    $error = new MessageBag([
                        'title'   => '錯誤訊息: ',
                        'message' => '份量價格不得為空, 請重新確認 !',
                    ]);

                    return back()->with(compact('error'));
                }

            });


            $form->saved(function (Form $form) use ($options_list) {

                $menu_item_unit = array();
                $unit_select = array();

                foreach ($options_list as $rows)
                {
                    foreach ($form->unit as $key => $val)
                    {
                        if ($val == $rows['unit_id'])
                        {
                            $menu_item_unit[] = array(
                                'item_id' => $form->model()->id, 
                                'unit_id' => $val
                            );

                            $unit_select[] = $rows;
                        }
                    }
                }

                MenuItem::addMenuItemUnit($form->model()->id, $menu_item_unit);

                if (!empty($unit_select))
                {
                    MenuItem::updateSpecRelation($form->model()->id, json_encode($unit_select));
                }

                Session::forget('menu_item_id');

                $size_ids = array();
                $size_names = array();
                
                foreach ($form->size as $row)
                {
                    $size_ids[] = $row['id'];
                    $size_names = $row['size_name'];
                }

                //不知為何沒有update size_name???
                MenuItem::updateSizeName($size_ids, $size_names);

                return redirect('/admin/store/set/cuisine_group');
            });

        });
    }

    public function MenuImport(Request $request)
    {
        $response = array();
        
        $sel_item = null;

        if (!empty($request) && !empty($request['id']))
        {
            $sel_item = $request['id'];
        }

        $store_id = array(Session::get('store_id'));

        $result = MenuItem::AddStoreMenuItem($store_id, $sel_item);

        if ($result == false)
        {
            $response = ['status' => 'error', 'message' => '匯入失敗 !'];
        }
        else
        {
            $response = ['status' => 'success', 'message' => '匯入成功 !'];
        }

        echo json_encode($response);
    }

    public function MenuImportAll()
    {
        $response = array();

        $store_id = array(Session::get('store_id'));

        $result = MenuItem::AddMenuItem2Store(Session::get('company_id'), $store_id);
        
        if ($result == false)
        {
            $response = ['status' => 'error', 'message' => '匯入失敗 !'];
        }
        else
        {
            $response = ['status' => 'success', 'message' => '匯入成功 !'];
        }

        echo json_encode($response);
    }
}
?>