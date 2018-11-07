<?php

namespace App\Admin\Controllers\Store\Campaign;

use App\Models\idelivery\Campaign_setting_form;
use App\Models\idelivery\Campaign_event;
use App\Models\idelivery\Menu_item;
use App\Models\idelivery\Publish_version_log;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class OverViewController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => 1, 'text' => '開啟', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => '關閉', 'color' => 'default'],
    ];

    private $types = array(
        "condition_amount" => 1,//滿多少錢
        "condition_qty"    => 2,//滿多少數量
        "condition_amount_menu_group" => 3,//那些商品滿多少錢
        "condition_qty_menu_group" => 4,//那些商品滿多少數量
        "condition_amount_menu_item" => 5,//那些商品同品項滿多少錢
        "condition_qty_menu_item" => 6,//那些商品同品項滿多少數量
        "offer_discount"   => 1,//總金額打折
        "offer_amount"     => 2,//總金額折扣
        "offer_qty"        => 3,//數量多少錢
        "offer_discount_menu_group" => 4,//那些商品金額打折
        "offer_amount_menu_group" => 5,//那些商品金額折扣
        "offer_qty_menu_group" => 6,//那些商品最低價幾件多少錢
        "offer_discount_menu_group_n" => 7,//那些商品第幾件金額打折
        "offer_amount_menu_group_n" => 8,//那些商品第幾件金額折扣
        "offer_qty_menu_group_n" => 9,//那些商品最低價第幾件多少錢
        "offer_discount_menu_item" => 10,//那些商品同品項金額打折
        "offer_amount_menu_item" => 11,//那些商品同品項金額折扣多少錢
        "offer_qty_menu_item" => 12,//那些商品同品項多少錢
        "offer_discount_menu_item_n" => 13,//那些商品同品項第幾件金額打折
        "offer_amount_menu_item_n" => 14,//那些商品同品項第幾件金額折扣
        "offer_qty_menu_item_n" => 15,//那些商品同品項第幾件多少錢
        "offer_add_item" => 16,//額外贈送一個商品
        "offer_coupon" => 99,//獲得優惠券
        "offer_point"  => 98,//獲得點數
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('總覽');
            $content->description('');

            $store_id = Session::get('store_id');

            if(empty($store_id)) {
                $box = new Box('提示', '請選擇店家!!');
                $content->row($box->removable()->style('warning'));
            } else {
                $publish_version_log = Publish_version_log::where(['store_id'=>$store_id, 'types'=>2])->orderBy('version', 'desc')->first();
                if (empty($publish_version_log)) {
                    $version = '沒有發佈的版本號!';
                    $url     = '#';
                } else {
                    $version = $publish_version_log->version;
                    $url     = $publish_version_log->file_url;
                }
    
                $box = $box = new Box('所有moneyPOS與app的活動設定均以發佈為準', sprintf("目前發佈的版本號: <span id='current_version'>%s</span>\n<a id='preview' href='%s' target='_blank'>預覽</a>", $version, $url));
                $content->row($box->collapsable()->style('info'));
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

            $content->header('總覽');
            $content->description('');

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

            $content->header('總覽');
            $content->description('');

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
            $("#btn_publish").on("click", function () {
                $.ajax({
                    url: "/admin/store/set/publish/campaign",
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

        return Admin::grid(Campaign_setting_form::class, function (Grid $grid) {
            $condition_match = ['Condition_amount' => '消費滿金額', 'Condition_qty' => '消費滿數量', 'Condition_amount_menu_group' => '消費那些商品滿金額', 'Condition_amount_menu_item' => '消費那些商品同品項滿金額', 'Condition_qty_menu_group' => '消費那些商品滿數量', 'Condition_qty_menu_item' => '消費那些商品同品項滿數量'];
            $offer_match = [
                'Offer_amount'            => '現金抵用',
                'Offer_amount_menu_group' => '現金抵用',
                'Offer_amount_menu_group_n' => '第幾件現金抵用',
                'Offer_amount_menu_item' => '同品項現金抵用',
                'Offer_amount_menu_item_n' => '同品項第幾件現金抵用',
                'Offer_discount' => '現金折扣(%)',
                'Offer_discount_menu_group' => '現金折扣(%)',
                'Offer_discount_menu_group_n' => '第幾件現金折扣(%)',
                'Offer_discount_menu_item' => '同品項現金折扣(%)',
                'Offer_discount_menu_item_n' => '同品項第幾件現金折扣(%)',
                'Offer_qty' => '最低價幾件變多少錢',
                'Offer_qty_menu_group' => '最低價幾件變多少錢',
                'Offer_qty_menu_group_n' => '最低價第幾件變多少錢',
                'Offer_qty_menu_item' => '同品項變多少錢',
                'Offer_qty_menu_item_n' => '同品項第幾件變多少錢',
                'Offer_coupon' => '優惠券',
                'Offer_points' => '紅利/點數',
            ];

            // 設定條件
            $event_id = Campaign_event::where('keyword', 'order')->value('id');// 結帳優惠的編號
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');;
            $grid->model()->where('types', 1)
                            ->where('event_id', $event_id)
                            ->where('company_id', $company_id)
                            ->whereIn('store_id', [0, $store_id])
                            ->orderBy('sort_by', 'desc');

            // 禁止功能
            $grid->disableCreation();//創建
            // $grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            $grid->disableActions();//操作

            $grid->tools(function ($tools) {
                $html = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />';
                $html .= '<div class="form-inline pull-left"><a class="btn btn-sm btn-warning" id="btn_publish"><i class="fa fa-archive" aria-hidden="true"></i>&nbsp;發佈</a></div>';

                $tools->append($html);
            });

            // $grid->idID')->sortable();
            $grid->title(trans('campaign.field.title'));
            $grid->column('所屬')->display(function() {
                if (empty($this->store_id)) {
                    return "<span class='label label-primary'>品牌</span>";
                } else {
                    return "<span class='label label-warning'>店家</span>";
                }
            });

            $grid->title(trans('campaign.field.title'));
            $grid->column('活動條件')->display(function() use ($condition_match) {
                if (isset($condition_match[$this->condition_table])) {
                    return $condition_match[$this->condition_table];
                } else {
                    return '無條件';
                }
            });

            $grid->column('活動優惠')->display(function() use ($offer_match) {
                if (isset($offer_match[$this->offer_table])) {
                    return $offer_match[$this->offer_table];
                } else {
                    return '無優惠';
                }
            });

            $grid->start_at(trans('campaign.field.start_at'));
            $grid->end_at(trans('campaign.field.end_at'));
            $grid->is_default(trans('campaign.field.default'))->switch($this->states);
            $grid->status(trans('campaign.field.status'))->switch($this->states);
            $grid->sort_by(trans('campaign.field.sort_by'))->orderable();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Campaign_setting_form::class, function (Form $form) {

            $form->switch('status', trans('campaign.field.status'))->states($this->states);
            $form->switch('is_default', trans('campaign.field.default'))->states($this->states);
        });
    }

    public function publish()
    {
        $event_id   = Campaign_event::where('keyword', 'order')->value('id');// 結帳優惠的編號
        $company_id = Session::get('company_id');
        $store_id   = Session::get('store_id');;
        $campaign_setting = Campaign_setting_form::where('types', 1)
                                ->where('event_id', $event_id)
                                ->where('company_id', $company_id)
                                ->whereIn('store_id', [0, $store_id])
                                ->orderBy('sort_by', 'desc')
                                ->get();
        $list = array();
        if ($campaign_setting->isNotEmpty()) {
            foreach ($campaign_setting as $value) {
                $obj = new \stdClass();
    
                $obj->store_id         = $value->store_id;
                $obj->store_name       = empty($value->store_id) ? '' : $value->store->name;
                $obj->sn               = base64_encode($value->id);
                $obj->title            = $value->title;
                $obj->description      = $value->description;
                $obj->used_count       = $value->used_count;
                $obj->max_count        = $value->max_count;
                $obj->kind             = $value->kind;
                $obj->kind_value       = $value->kind_value;
                $obj->offer_max_value  = $value->offer_max_value;
                $obj->product_delivery = $value->product_delivery;
                $obj->repeat           = $value->repeat;
                $obj->plural           = $value->plural;
                $obj->online           = $value->online;
                $obj->remark           = $value->remark;
                $obj->hidden           = (boolean) $value->hidden;
                $obj->status           = $value->status;
                $obj->is_default       = (boolean) $value->is_default;
                $obj->sort_by          = $value->sort_by;
                $obj->start_at         = empty($value->start_at) ? "" : strtotime($value->start_at);
                $obj->end_at           = empty($value->end_at) ? "" : strtotime($value->end_at);
    
                // 轉成小寫直接關連資料模型
                $condition_table = strtolower($value->condition_table);
                $offer_table     = strtolower($value->offer_table);
    
                $obj_condition = $value->$condition_table;
                $obj_offer     = $value->$offer_table;
                // 條件
                $cond = array();
                if ( ! empty($obj_condition)) {
                    if (isset($this->types[$condition_table])) {
                        $cond["type"] = $this->types[$condition_table];
                    }
    
                    $menu_item = null;
                    if (isset($obj_condition->menu_item_ids)) {
                        $result = Menu_item::whereIn('id', $obj_condition->menu_item_ids)->select('id', 'name')->get();
    
                        if ($result->isNotEmpty()) {
                            foreach ($result as $value) {
                                $menu_item[] = array('id'=>$value['id'], 'name'=>$value['name']);
                            }
                        }
                    }
    
                    $cond["value"]     = empty($obj_condition->value) ? 0 : (float) $obj_condition->value;
                    $cond["menu_item"] = $menu_item;
                }
                // 獎勵
                $offer = array();
                if ( ! empty($obj_offer)) {
                    $menu_item = null;
                    if (isset($obj_offer->menu_item_ids)) {
                        $result = Menu_item::whereIn('id', $obj_offer->menu_item_ids)->select('id', 'name')->get();
    
                        if ($result->isNotEmpty()) {
                            foreach ($result as $value) {
                                $menu_item[] = array('id'=>$value['id'], 'name'=>$value['name']);
                            }
                        }
                    }
    
                    $offer["type"]        = $this->types[$offer_table];
                    $offer["value"]       = empty($obj_offer->value) ? 0 : (float) $obj_offer->value;
                    $offer["max_value"]   = empty($obj_offer->max_value) ? 0 : (float) $obj_offer->max_value;
                    $offer["menu_item"]   = $menu_item;
                    $offer["nth"]         = $obj_offer->n_th;
                    $offer["price"]       = $obj_offer->price;
                }
    
                $obj->condition = $cond;
                $obj->offer     = $offer;
    
                $list[] = $obj;
            }
        }

        $publish_version = new Publish_version_log;
        $publish_version->store_id = $store_id;
        $publish_version->types    = 2;
        $publish_version->save();

        $publish_version_log_id = $publish_version->id;

        unset($publish_version);

        $version = date('YmdHis').'_'.$publish_version_log_id;
        $data = ['version' => $version, 'data' => $list];

        if ( ! is_dir(sprintf("tmp/version/campaign/%s", $store_id))) {
            mkdir(sprintf("tmp/version/campaign/%s", $store_id));
        }
        
        $file_tmp_path = sprintf("tmp/version/campaign/%s/%s.json", $store_id, $version);
        $result = file_put_contents($file_tmp_path, json_encode($data));
        if (empty($result)) {
            Publish_version_log::destroy($publish_version_log_id);
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        unset($result);

        $file_s3_path = sprintf("version/campaign/%s/%s.json", $store_id, $version);
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
