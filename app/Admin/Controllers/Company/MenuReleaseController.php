<?php

namespace App\Admin\Controllers\Company;

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

class MenuReleaseController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $company_id = Session::get('company_id');
            $content->header('餐點品項釋出');
            $content->description('Menu Item Release');

            if(empty($company_id)) 
            {
                $box3 = new Box('提示', '請選擇所屬公司品牌!!');
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::script('$(".store_assign").select2({
                            allowClear: true,
                            placeholder: "欲釋出店家"
                        });

                        $(".store_select").select2({
                            allowClear: true,
                            placeholder: "欲釋出店家"
                        });

            var result = [];
            var obj = {};
            $(".store_assign").change(function() {
                obj.id = $(this).data("id");
                obj.sel = "";
                $(this).find("option:selected").each(function() {
                    obj.sel += $(this).val() + ",";
                });
                result.push(obj);
                obj = {};
            });

            $(".export").on("click", function () {
                var sel_id = $(this).data("id");        
                $.ajax({
                    url: "/admin/company/menu_release/export",
                    type: "POST",
                    dataType : "json",
                    cache: false,
                    data: {id: sel_id, sel: result},
                    headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                    success: function(data) {
                        toastr.success(data.message);
                        window.location.replace("/admin/company/set/menu_release");
                    },
                    error: function() {
                        alert("釋出錯誤 !");
                    }
                });
                
                return false; 
            });


            var sel_obj = [];
            $(".store_select").change(function() {
                sel_obj = [];
                $(this).find("option:selected").each(function() {
                    sel_obj.push($(this).val());
                });
            });

            $("#all2store").on("click", function () {
                $.ajax({
                    url: "/admin/company/set/menu_release/export_all",
                    type: "POST",
                    dataType : "json",
                    cache: false,
                    data: {sel: sel_obj},
                    headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                    success: function(data) {
                        toastr.success(data.message);
                        window.location.replace("/admin/company/menu_release");
                    },
                    error: function() {
                        alert("釋出錯誤 !");
                    }
                });
                
                return false; 
            });'
        );

        return Admin::grid(MenuItem::class, function (Grid $grid) {
            $grid->tools(function ($tools) {
                $store = Store::getStore(Session::get('company_id'));
                $str = '';
                if (!empty($store))
                {
                    $str = '<div class="form-inline pull-right">
                            <label>釋出所有餐點品項 &nbsp;</label>
                            <div class="input-group">';
                    $str .= '<select class="form-control store_select" style="width: 100%;" name="store_select[]" 
                                 multiple="multiple" data-placeholder="選擇 欲釋出餐點店家">';

                    foreach ($store as $val)
                    {
                        $str .= '<option value="'.$val->id.'" >'.$val->name.'</option>';
                    }

                    $str .= '</select>
                            <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="all2store"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>
                            </span>
                            </div>
                            </div>';
                }

                $tools->append($str);
            });


            $grid->model()->getStoreItem(Session::get('company_id'));

            $grid->column('group_id', '餐點分類名稱')->display(function () {
                return CuisineGroup::getGroupName($this->group_id);
            });

            $grid->column('name', '品項名稱');

            $grid->column('picture', '圖片')->display(function () {
                if (!empty($this->picture))
                {
                    return "<img src='". env('ADMIN_UPLOAD_URL', '') . $this->picture ."' width = '50px'>";
                }
                else 
                {
                    return '';
                }
            });
           
            $grid->column('size', '份量/價格')->style("max-width:100px")->display(function () {
                $str = '';
                if (!empty($this->size))
                {
                    foreach ($this->size as $row)
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

            $grid->column('menu_store', '已釋出店家')->display(function () {
                $store = MenuItem::getMenuItemStore($this->id);
                $str = '';

                foreach ($store as $row)
                {
                    $str .= '<span class="label label-info">'.$row->name.'</span><p></p>';
                }

                return $str;
            });

            $grid->column('store_assign', '欲釋出店家')->style('width: 18%')->display(function () {

                $store_id = array();

                if (!empty($this->store))
                {
                    foreach ($this->store as $rows)
                    {
                        $store_id[] = $rows['id'];
                    }
                }

                $store = Store::getStore(Session::get('company_id'), $store_id);
                $str = '';
                if (!empty($store))
                {
                    $str = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />';
                    $str .= '<select class="form-control store_assign" style="width: 100%;" name="store_assign[]" 
                    multiple="multiple" data-placeholder="選擇 欲釋出菜單店家" data-id='.$this->id.'>';

                    foreach ($store as $val)
                    {
                        $str .= '<option value="'.$val->id.'" >'.$val->name.'</option>';
                    }

                    $str .= '</select><input type="hidden" name="store_assign[]" id="assign"/>';
                }

                return $str;
            });

            $grid->column('release', '操作')->display(function () {
                $str = '<a type="button" href="#" class="export" data-id='.$this->id.'><i class="fa fa-share-square-o fa-lg" aria-hidden="true"></i></a>';
                return $str;
            });

            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableActions();

            $grid->disableFilter();
        });
    }



    public function MenuExport(Request $request)
    {
        $response=array();
        $data = null;

        foreach ($request['sel'] as $row)
        {
            if ($request['id'] == $row['id'])
            {
                $data = $row['sel'];
            }
        }

        $data = substr($data, 0, -1);
        $sel_store = explode(',', $data);

        try
        {
            if (!empty($sel_store))
            {
                $result = MenuItem::AddStoreMenuItem($sel_store, $request['id']);
    
                if ($result == false)
                {
                    $response = ['status' => 'error', 'message' => '釋出失敗 !'];
                }
                else
                {
                     $response = ['status' => 'success', 'message' => '釋出成功 !'];
                }
            }
        } 
        catch (Exception $e) 
        {
            echo json_encode($e);
        }

        echo json_encode($response);
    }


    public function MenuExportAll(Request $request)
    {
        $response=array();

        $sel_store = $request['sel'];

        try
        {
            if (!empty($sel_store))
            {
                $result = MenuItem::AddMenuItem2Store(Session::get('company_id'), $sel_store);
                
                if ($result == false)
                {
                    $response = ['status' => 'error', 'message' => '釋出失敗 !'];
                }
                else
                {
                     $response = ['status' => 'success', 'message' => '釋出成功 !'];
                }
            }
        } 
        catch (Exception $e) 
        {
            echo json_encode($e);
        }

        echo json_encode($response);
    }


}

?>