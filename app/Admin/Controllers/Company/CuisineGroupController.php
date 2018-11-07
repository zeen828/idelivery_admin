<?php

namespace App\Admin\Controllers\Company;

use App\Models\idelivery\Cuisine_group;
use App\Models\idelivery\Cuisine_category;
use App\Models\idelivery\Cuisine_unit;
use App\Models\idelivery\Cuisine_type;
use App\Models\idelivery\Menu_item;
use App\Models\idelivery\Menu_size;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class CuisineGroupController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '關閉', 'color' => 'default'),
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
        Admin::js('js/menu_item_del.js');

        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            Session::put('action_roles', 'company');

            $content->header(trans('idelivery.cuisine.group.config'));
            $content->description(trans('idelivery.admin.index'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
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
//    public function destroy($id)
//    {
//        try
//        {
//            $company_id = Session::get('company_id');
//
//            $cuisine_group = Cuisine_group::find($id);
//            if (empty($cuisine_group))
//            {
//                return array('status' => false, 'message' => '查無餐點分類資料!');
//            }
//
//            if ($cuisine_group->store_id != 0 || $cuisine_group->company_id != $company_id)
//            {
//                return array('status' => false, 'message' => '無權限刪除餐點分類資料!');
//            }
//
//            Cuisine_group_category::where('group_id', $id)->delete();
//            Cuisine_group_type::where('group_id', $id)->delete();
//            Cuisine_unit_group::where('group_id', $id)->delete();
//            Cuisine_group::find($id)->delete();
//            CuisineGroup::deleteData($id);
//
//        } catch (\Exception $e) {
//            return array('status' => false, 'message' => '刪除失敗 !');
//        }
//
//        return array('status' => true, 'message' => '刪除成功 !');
//    }


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

            $cuisine_group = Cuisine_group::find($id);
            if (empty($cuisine_group) || empty($cuisine_group->company_id)
                || $company_id != $cuisine_group->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('idelivery.cuisine.group.config'));
            $content->description(trans('idelivery.admin.edit'));
            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
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
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.cuisine.group.config'));
            $content->description(trans('idelivery.admin.create'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
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

        return Admin::grid(Cuisine_group::class, function (Grid $grid) {

            $grid->model()->where('company_id', Session::get('company_id'))
                            ->where('store_id', 0)
                            ->with('menuitem')
                            ->orderby('sort_by', 'asc');

            $grid->group_name(trans('idelivery.cuisine.group.name'));

            $grid->column(trans('idelivery.cuisine.item.title'))->expand(function (){
                $str = '<table class="table table-responsive table-hover" style="margin-left:22%; width:78%">
                        <thead>
                        <tr>
                            <th width="25%">名稱</th>
                            <th width="15%">圖片</th>
                            <th width="10%">份量/價格</th>
                            <th width="20%">附加選項</th>
                            <th width="10%">獲得點數</th>
                            <th width="10%">App隱藏</th>
                            <th width="10%">操作</th>
                        </tr>
                        </thead>
                        <tbody>';

                $menuitems = Menu_item::where('company_id', '=', Session::get('company_id'))
                    ->where('store_id', '=', 0)
                    ->where('group_id', '=', $this->id)
                    ->orderBy('sort_by', 'asc')
                    ->get();

                foreach ($menuitems as $items) {
                   $spec_relation = null;

                    if (!empty($items)) {
                        if (!empty($items->spec_relation)) {
                            foreach (json_decode($items->spec_relation, true) as $spec) {
                                $spec_relation .= "<span class='label label-success'>{$spec['unit_title']}</span><p></p>";
                            }
                        }
                    }

                    $str .= "<tr>
                            <td style='vertical-align: middle'>{$items->name}</td>";

                    if (!empty($items->picture)) {
                        $str .= "<td style='vertical-align: middle'><img src='". env('ADMIN_UPLOAD_URL', '') . $items->picture ."' width = '50px'></td>";
                    } else {
                        $str .= "<td style='vertical-align: middle'></td>";
                    }

                    $menu_item = Menu_size::where('item_id', $items->id)
                        ->select('size_name', 'price')
                        ->where('status', '1')
                        ->orderBy('price', 'desc')
                        ->first();

                    $size_name = null;
                    $price = 0;
                    if (!empty($menu_item)) {
                        $size_name = $menu_item->size_name;
                        $price = $menu_item->price;
                    }

                    if (!empty($size_name)) {
                        $str .= "<td style='vertical-align: middle'><span class='label label-success'>{$size_name}</span>&nbsp;<span class='label label-primary'>{$price}元</span></td>";
                    } else {
                        $str .= "<td style='vertical-align: middle'><span class='label label-primary'>{$price}元</span></td>";
                    }

                    $str .= "<td style='vertical-align: middle'>{$spec_relation}</td>
                            <td style='vertical-align: middle'>{$items->points}</td>";

                    if ($items->hidden) {
                        $str .= "<td style='vertical-align: middle'><span class='label label-danger'>隱藏</span></td>";
                    } else {
                        $str .= "<td style='vertical-align: middle'><span class='label label-warning'>顯示</span></td>";
                    }

                    $str .= "<td style='vertical-align: middle'><a href='/admin/company/set/menu_item/{$items->id}/edit'>
                            <i class='fa fa-edit'></i>
                            </a><a href='javascript:void(0);' class='grid-row-delete-item' onclick='company_item_remove({$items->id});'>
                            <i class='fa fa-trash'></i>
                            </a></td>
                            </tr>";
                }

                $str .= '</tbody></table>';

                return $str;

            }, trans('idelivery.cuisine.item.title'));

            $grid->category(trans('idelivery.cuisine.category.title'))->pluck('name')->label();
            $grid->type(trans('idelivery.cuisine.type.title'))->pluck('name')->label();
            $grid->unit(trans('idelivery.cuisine.unit.title'))->display(function ($unit) {
               $str = "<div>";
               $index = 0;
               foreach ($unit as $item)
               {
                   if ($index < 5)
                   {
                       $str = $str . "<span class='label label-success'>".$item["unit_name"]."</span>&nbsp;";
                   }
                   else
                   {
                       $index = 0;
                       $str = $str . "</div><p></p><div><span class='label label-success'>".$item["unit_name"]."</span>&nbsp;";
                   }

                   $index += 1;
               }
                return $str."</div>";
            });
            $grid->column('分類App隱藏')->display(function() {
                $result = '';
                if ($this->hidden) {
                    $result = "<span class='label label-danger'>隱藏</span>";
                } else {
                    $result = "<span class='label label-warning'>顯示</span>";
                }

                return $result;
            });

            //$grid->sort_by('順序')->orderable();
            $grid->column(sprintf('新增%s', trans('idelivery.cuisine.item.title')))->display(function () {
                return '<a href="/admin/company/set_group_item/'.$this->id.'/menu_item/create"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>' . sprintf('新增%s', trans('idelivery.cuisine.item.title')) . '<i class="fa fa-cutlery" aria-hidden="true"></i></a>';
            });

            $grid->disableFilter();
            $grid->disableRowSelector();

            Admin::script('$(document).ready(function(){
                $(".grid-expand").trigger("click");
            });');
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(Cuisine_group::class, function (Form $form) {
            $company_id = Session::get('company_id');
            $store_id = 0;

            $form->hidden('company_id','品牌編號')->default($company_id);
            $form->hidden('store_id','店家編號')->default($store_id);

            $form->text('group_name', trans('idelivery.cuisine.group.name'))->rules('required');
            $form->multipleSelect('category', trans('idelivery.cuisine.category.title'))->options(Cuisine_category::pluck('name', 'id'));
            $form->multipleSelect('type', trans('idelivery.cuisine.type.title'))->options(Cuisine_type::pluck('name', 'id'));
            
            $form->multipleSelect('unit', trans('idelivery.cuisine.unit.title'))->options(
                Cuisine_unit::where('company_id', Session::get('company_id'))
                    ->where('store_id', 0)
                    ->pluck('unit_name', 'id')
            );

            $form->switch('hidden', 'App隱藏')->states($this->hidden_arr);
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);
        });
    }
}