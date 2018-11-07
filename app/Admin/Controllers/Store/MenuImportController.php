<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\MenuItem;
use App\Model\idelivery\Store;
use App\Model\idelivery\CuisineGroup;
use App\Model\idelivery\CuisineUnit;
use App\Model\idelivery\MenuStoreItem;
use App\Models\idelivery\Menu_item_unit;
use App\Models\idelivery\Menu_size;
use App\Models\idelivery\Menu_item;
use App\Models\idelivery\Menu_store_item;
use App\Models\idelivery\Cuisine_attr;
use App\Models\idelivery\Publish_version_log;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
//use Illuminate\Support\Collection;
use Illuminate\Http\Request;
//use Encore\Admin\Widgets\Table;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Storage;

class MenuImportController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '啟用', 'color' => 'primary'),
        'off' => array('value' => 2 , 'text' => '關閉', 'color' => 'default'),
    );

    private $menu_item_hidden = array(
        'on'  => array('value' => 0, 'text' => '顯示', 'color' => 'primary'),
        'off' => array('value' => 1, 'text' => '隱藏', 'color' => 'default')
    );

    public function index()
    {
        return Admin::content(function (Content $content) {

            $store_id = Session::get('store_id');
            $content->header('餐點品項匯入');
            $content->description('Menu Item Import');

            if(empty($store_id)) {
                $box = new Box('提示', '請選擇店家!!');
                $content->row($box->removable()->style('warning'));
            } else {
                $publish_version_log = Publish_version_log::where(['store_id'=>$store_id, 'types'=>1])->orderBy('version', 'desc')->first();
                if (empty($publish_version_log)) {
                    $version = '沒有發佈的版本號!';
                    $url     = '#';
                } else {
                    $version = $publish_version_log->version;
                    $url     = $publish_version_log->file_url;
                }

                $box = new Box('所有moneyPOS與app的菜單均以發佈為準', sprintf("目前發佈的版本號: <span id='current_version'>%s</span>\n<a id='preview' href='%s' target='_blank'>預覽</a>", $version, $url));
                $content->row($box->collapsable()->style('info'));
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
        Admin::script('
            var sel_obj = [];
            $(".store_select").change(function() {
                sel_obj = [];
                $(this).find("option:selected").each(function() {
                    sel_obj.push($(this).val());
                });
            });

            $("#all2store").on("click", function () {
                $.ajax({
                    url: "/admin/store/set/menu_import/import_all",
                    type: "POST",
                    dataType : "json",
                    cache: false,
                    headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                    success: function(data) {
                        toastr.success(data.message);
                        window.location.replace("/admin/store/set/menu_import");
                    },
                    error: function() {
                        alert("匯入錯誤 !");
                    }
                });
                
                return false; 
            });
            
            $("#btn_publish").on("click", function () {
                $.ajax({
                    url: "/admin/store/set/publish/menu",
                    type: "POST",
                    dataType: "json",
                    headers: {"X-CSRF-Token" : $("#csrf_token").val()}
                })
                .done(function(obj){
                    alert(obj.message);
                    $("#current_version").text(obj.version);
                    $("#preview").attr("href", obj.url);
                })
                .fail(function(obj){
                    var _response = obj.responseJSON;
                    alert(_response.message);
                });
            });'
        );

        return Admin::grid(MenuStoreItem::class, function (Grid $grid) {

            $grid->paginate(100);
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableActions();
            $grid->disableRowSelector();
            $grid->disableFilter();

            $grid->tools(function ($tools) {
                $store = Store::getStore(Session::get('company_id'));
                $html = '';
                if (!empty($store)) {
                    $html = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />';
                    $html .= '<div class="form-inline pull-left"><a class="btn btn-sm btn-warning" id="btn_publish"><i class="fa fa-archive" aria-hidden="true"></i>&nbsp;發佈</a></div>';
                    $html .= '<div class="form-inline pull-right"><a class="btn btn-sm btn-success" id="all2store"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;匯入品牌的所有品項</a></div>';
                }

                $tools->append($html);
            });

            $grid->model()->getStoreItem(Session::get('store_id'))->orderby('sort_by', 'asc');

            $grid->column('group_id', '餐點分類名稱')->display(function () {
                return CuisineGroup::getGroupName($this->group_id);
            });

            $grid->column('name', '品項名稱');

            $grid->column('picture', '圖片')->display(function () {
                $result = '';
                if (!empty($this->picture)) {
                    $result = "<img src='". env('ADMIN_UPLOAD_URL', '') . $this->picture ."' width = '50px'>";
                }

                return $result;
            });
           
            $grid->column('size', '份量/價格')->style("max-width:100px")->display(function () {
                
                $menu_sizes = MenuStoreItem::getMenuSize($this->item_id);
                $str = '';

                if (!empty($menu_sizes)) {
                    foreach ($menu_sizes as $row) {
                        if (!empty($row->size_name)) {
                            if (!empty($row->price)) {
                                $str .= '<div class="row">'
                                        .'<div class="col-xs-4">'
                                        .'<span class="label label-success">'.$row->size_name.'</span>'
                                        .'</div>'
                                        .'<div class="col-xs-8">'
                                        .'<span class="label label-primary">'.$row->price.'元</span>'
                                        .'</div>'
                                        .'</div><p></p>';
                            } else {
                                $str .= '<div class="row">'
                                        .'<div class="col-xs-12">'
                                        .'<span class="label label-success">'.$row->size_name.'</span>'
                                        .'</div>'
                                        .'</div><p></p>';
                            }
                        } else {
                            if (!empty($row->price)) {
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

            $grid->column('spec_relation', '附加選項')->display(function () {
                $result = '';

                if (!empty($this->spec_relation)) {
                    $spec_relation = json_decode($this->spec_relation, true);

                    foreach ($spec_relation as $options) {
                        $result .= "<span class='label label-success'>{$options['unit_title']}</span><p></p>";
                    }
                }

                return $result;
            });

            $grid->column('points', '獲得點數');
            $grid->sort_by('順序')->orderable();
            $grid->hidden('App隱藏')->switch($this->menu_item_hidden);
            $grid->column('status', '啟用')->switch($this->status_arr);

            // $grid->column('operate', '操作')->display(function () {
            //     $str = '<a type="button" href="#" class="import" data-id='.$this->id.'><i class="fa fa-download fa-lg" aria-hidden="true"></i></a>';
            //     return $str;
            // });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(MenuStoreItem::class, function (Form $form) {
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);
            $form->switch('hidden', 'App隱藏')->states($this->menu_item_hidden)->default(0);
        });
    }

    public function MenuImport(Request $request)
    {
        $response = array();
        $sel_item = null;

        if (!empty($request) && !empty($request['id'])) {
            $sel_item = $request['id'];
        }

        $store_id = array(Session::get('store_id'));
        $result = MenuItem::AddStoreMenuItem($store_id, $sel_item);

        $response = ['status' => 'success', 'message' => '匯入成功'];

        if ($result == false) {
            $response = ['status' => 'error', 'message' => '匯入失敗 !'];
        }

        echo json_encode($response);
    }

    public function MenuImportAll()
    {
        $response = array();
        $store_id = array(Session::get('store_id'));
        $result = MenuItem::AddMenuItem2Store(Session::get('company_id'), $store_id);
        
        $response = ['status' => 'success', 'message' => '匯入成功'];

        if ($result == false) {
            $response = ['status' => 'error', 'message' => '匯入失敗 !'];
        }

        echo json_encode($response);
    }

    public function MenuPublish()
    {
        $company_id = Session::get('company_id');
        $store_id   = Session::get('store_id');

        if (empty($store_id)) {
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        // 取得店家菜單
        $menu_store_item = Menu_store_item::where(['store_id'=>$store_id], ['status'=>1])->orderBy('sort_by', 'asc')->get();

        // 店家品項ID
        $item_list = array();
        foreach ($menu_store_item as $value) {
            $menu_item = Menu_item::find($value->item_id);
            // 整理group
            if ( ! empty($menu_item)) {
                if (empty($item_list[$menu_item->group_id])){
                    $item_list[$menu_item->group_id] = array(
                        'category_name'   => $menu_item->group->group_name,
                        'category_hidden' => $menu_item->group->hidden,
                        'items'           => array()
                    );
                }
                // 整理size
                $base_price = array();
                foreach ($menu_item->size as $size) {
                    if ($size->status == '1') {
                        // is_selected 1=true; 2=false;
                        $is_selected = false;
                        if ($size->is_selected == '1') {
                            $is_selected = true;
                        }
    
                        $obj_size = new \stdClass();
                        $obj_size->id       = $size->id;
                        $obj_size->title    = $size->name;
                        $obj_size->selected = $is_selected;
                        $obj_size->price    = (int) $size->price;
                        $obj_size->points   = 0;
                        $obj_size->exchange = 0;
    
                        $base_price[] = $obj_size;
                    } 
                }
                // 整理附加選項-單位
                $option = array();
                foreach ($menu_item->unit as $unit) {
                    // is_multiple 1=true;2=false;
                    $is_multiple = false;
                    if ($unit->is_multiple == '1') {
                        $is_multiple = true;
                    }
    
                    // is_required 1=true;2=false;
                    $is_required = false;
                    if ($unit->is_required == '1') {
                        $is_required = true;
                    }
    
                    $obj_unit = new \stdClass();
                    $obj_unit->unit_id     = $unit->id;
                    $obj_unit->unit_title  = $unit->unit_name;
                    $obj_unit->is_multiple = $is_multiple;
                    $obj_unit->required    = $is_required;
                    
                    // 整理附加選項-項目
                    $cuisine_attr = Cuisine_attr::where(['unit_id'=>$unit->id, 'status'=>1])->get();
                    $attribute = array();
                    foreach ($cuisine_attr as $attr) {
                        if ($attr->status == '1') {
                            // is_default 1=true;2=false;
                            $is_default = false;
                            if ($attr->is_default == '1') {
                                $is_default = true;
                            }
    
                            $obj_attr = new \stdClass();
                            $obj_attr->id          = $attr->id;
                            $obj_attr->title       = $attr->attr_name;
                            $obj_attr->sub_title   = '';
                            $obj_attr->selected    = $is_default;
                            $obj_attr->extra_price = (int) $attr->extra_price;
    
                            $attribute[] = $obj_attr;
                        }
                    }
    
                    $obj_unit->attribute = $attribute;
    
                    $option[] = $obj_unit;
                }
                // 整理item
                $item_list[$menu_item->group_id]['items'][] = array(
                    'id'         => $menu_item->id,
                    'title'      => $menu_item->name,
                    'subtitle'   => $menu_item->intro,
                    'img_url'    => empty($menu_item->picture) ? null : env('ADMIN_UPLOAD_URL').$menu_item->picture,
                    'hidden'     => empty($value->hidden) ? false : true,
                    'base_price' => $base_price,
                    'option'     => $option,
                );
            }
        }

        $menu_list = array_values($item_list);

        $publish_version = new Publish_version_log;
        $publish_version->store_id = $store_id;
        $publish_version->types    = 1;
        $publish_version->save();

        $publish_version_log_id = $publish_version->id;

        unset($publish_version);

        $version = date('YmdHis').'_'.$publish_version_log_id;
        $data = ['version' => $version, 'data' => $menu_list];

        if ( ! is_dir(sprintf("tmp/version/menu/%s", $store_id))) {
            mkdir(sprintf("tmp/version/menu/%s", $store_id));
        }
        
        $file_tmp_path = sprintf("tmp/version/menu/%s/%s.json", $store_id, $version);
        $result = file_put_contents($file_tmp_path, json_encode($data));
        if (empty($result)) {
            Publish_version_log::destroy($publish_version_log_id);
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        unset($result);

        $file_s3_path = sprintf("version/menu/%s/%s.json", $store_id, $version);
        $result = Storage::disk('s3')->put($file_s3_path, file_get_contents($file_tmp_path));
        if (empty($result)) {
            Publish_version_log::destroy($publish_version_log_id);
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        $file_url = Storage::disk('s3')->url($file_s3_path);

        $publish_version = Publish_version_log::find($publish_version_log_id);
        $publish_version->version  = $version;
        $publish_version->file_url = $file_url;
        $publish_version->save();

        return response()->json(['status' => 'success', 'message' => '發佈成功', 'version' => $version, 'url' => $file_url]);
    }
}