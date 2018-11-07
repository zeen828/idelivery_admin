<?php

namespace App\Admin\Controllers\Store;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use App\Http\Controllers\Controller;
use App\Model\idelivery\Company;
use App\Model\idelivery\Store;
use App\Model\idelivery\District_tw;
use App\Lib\District;
use App\Lib\Geocoding;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;

class ProfileController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '正常', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    private $switch_arr = array(
        'off' => array('value' => 0, 'text' => '關閉', 'color' => 'default'),
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
    );

    protected function showFormParameters($content)
    {
        $parameters = request()->except(['_pjax', '_token']);
        if (!empty($parameters)) {
            ob_start();
            dump($parameters);
            $contents = ob_get_contents();
            ob_end_clean();
            $content->row(new Box('Form parameters', $contents));
        }
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('商店基本資料設定');
            $content->description();

            $content->body($this->grid());
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
            $store = Store::getData($id);

            $store_id = Session::get('store_id');
            if (empty($store) || empty($store->id)
                || $store_id != $store->id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header($store->name);
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

            $content->header('商店基本資料');
            $content->description('設定');

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
        return Admin::grid(Store::class, function (Grid $grid) {

            $store_id = Session::get("store_id");

            $grid->model()->where('id', '=', $store_id);
            //禁用建立
            $grid->disableCreation(); 
            // 禁用篩選
            $grid->disableFilter();
            // 禁用匯出
            $grid->disableExport();

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });            

            $grid->actions(function($actions){
                $actions->disableDelete();
            });

            $grid->name('店名');
            $grid->supervisor_name('店長');
            $grid->supervisor_phone('店長聯絡電話');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Store::class, function (Form $form) {

            $company_id = Session::get("company_id");
            $store_id   = Session::get("store_id");

            $district = District::get();

            $form->hidden('id');
            $form->hidden('company_id')->default($company_id);

            $form->tab('折扣設定', function ($form) {
                $form->text('promotion_amount', '促銷活動滿多少金額')->placeholder('空或0代表沒有活動');
                $form->text('promotion_discount', '促銷活動滿額打多少折')->help('EX:九折請輸入 0.1');
            })->tab('外帶外送設定', function($form) {
                $arr_prepare_time = array('-1' => '關閉', '0' => '立即', '10' => '10 分鐘', '20' => '20 分鐘', '30' => '30 分鐘', '40' => '40 分鐘', '50' => '50 分鐘', '60' => '一小時', '90' => '一小時30分鐘', '120' => '二小時');
                $form->hidden('delivery_conditions');
                $form->select('take_out', '外帶前置時間')->options($arr_prepare_time);
                $form->divide();
                $form->select('delivery_order', '外送前置時間')->options($arr_prepare_time);
                $form->number('deliveryconditions.scope', '範圍');
                $form->radio('deliveryconditions.type', '滿額類型')->options(['money' => '金額', 'qty' => '數量'])->default('money');
                $form->number('deliveryconditions.value', '滿額數值');
                $form->number('delivery_interval_quota', '每個時間區段可外送量設定');
                $form->textarea('delivery_area', '外送區域設定')->default(json_encode([array('post_code'=>0, 'city'=>'尚未設定', 'area'=>'尚未設定')]));
            })->tab('營業時間設定', function($form) {
                $form->hidden('business_hours');
                $form->hasMany('businesshours', '營業時間', function($form){
                    $form->checkbox('week_day', '星期')->options(['1'=>'一', '2'=>'二', '3'=>'三', '4'=>'四', '5'=>'五', '6'=>'六', '7'=>'日']);
                    $form->time('start_time', '開始時間')->rules('required');
                    $form->time('end_time', '結束時間')->rules('required');
                    $form->hidden('type')->default('1');
                });

            })->tab('接單時間設定', function($form) {
                $form->hidden('order_hours');
                $form->hasMany('orderhours', '接單時間', function($form) {
                    $form->checkbox('week_day', '星期')->options(['1'=>'一', '2'=>'二', '3'=>'三', '4'=>'四', '5'=>'五', '6'=>'六', '7'=>'日']);
                    $form->time('start_time', '開始時間')->rules('required');
                    $form->time('end_time', '結束時間')->rules('required');
                    $form->hidden('type')->default('2');
                });
            });

            $form->hidden('create_time', 'Created At');
            $form->hidden('updated_at', 'Updated At');

            //
            $form->saving(function (Form $form) {
                // 地址追加紀錄資料
                $district_id = $form->district_id;// 直接判斷物件會錯亂所以存變數
                $address = $form->address;
                $district_name = false;
                if(!empty($district_id)){
                    $district = District_tw::find((int) $district_id);
                    $form->city_id = $district->city_id;
                    $form->post_code = $district->post_code;
                    $form->district_name = $district->name;
                    $district_name = $district->name;
                }
                //有區域或地址就查詢經緯度
                if(!empty($district_name) && !empty($address)){
                    //經緯度
                    $Geocoding = new Geocoding;
                    $result = $Geocoding->addr2latlng(sprintf("%s%s", $district->name, $address));

                    $form->latitude  = 0;
                    $form->longitude = 0;
                    if ($result !== false) {
                        $form->latitude  = $result['lat'];
                        $form->longitude = $result['lng'];
                    }

                    unset($result);
                    unset($Geocoding);
                }
                // 外送條件(deliveryconditions)
                // var_dump($form->delivery_order);
                $tmp_arr = array();
                if($form->delivery_order !== '-1')
                {
                    $id = 0;
                    $text = '';
                    switch($form->deliveryconditions['type']){
                        case 'money':
                            $id = 1;
                            $text = sprintf("滿$%s外送", $form->deliveryconditions['value']);
                            break;
                        case 'qty':
                            $id = 2;
                            $text = sprintf("滿%s份外送", $form->deliveryconditions['value']);
                            break;
                        default:
                            $id = 3;
                            break;
                    }
                    $tmp_arr['id'] = $id;
                    $tmp_arr['key'] = $form->deliveryconditions['type'];
                    $tmp_arr['value'] = $form->deliveryconditions['value'];
                    $tmp_arr['distance'] = $form->deliveryconditions['scope'];
                    $tmp_arr['text'] = $text;

                    $form->delivery_conditions = json_encode($tmp_arr);
                    unset($tmp_arr);
                }


                // 營業時間(businesshours
                //var_dump($form->businesshours);
                $tmp_arr = array();
                if(count($form->businesshours) >= 1){
                    foreach ($form->businesshours as $hours){
                        //檢查時間
                        if(empty($hours['start_time']) || empty($hours['end_time'])){
                            // 輸出異常
                            //throw new \Exception('營業時間錯誤');
                            // 輸出錯誤訊息
                            $error = new MessageBag([
                                'title'   => '發生錯誤',
                                'message' => '營業時間錯誤',
                            ]);
                            return back()->with(compact('error'));
                        }
                        if(count($hours['week_day']) >= 1){}
                        foreach ($hours['week_day'] as $week_day){
                            if (in_array($week_day, array('1','2','3','4','5','6','7'))){
                                if($week_day == '7'){
                                    $week_day = '0';
                                }
                                $tmp_arr[$week_day]['week'] = $week_day;

                                $arr_time_data = array();
                                $dt1 = new \DateTime($hours['start_time']);
                                $dt2 = new \DateTime($hours['end_time']);
                                
                                if($dt1 < $dt2)
                                {
                                    $arr_time_data['interval'] = ($dt1->diff($dt2)->h * 60) + $dt1->diff($dt2)->i;
                                    $arr_time_data['start']    = $hours['start_time'];
                                    $arr_time_data['end']      = $hours['end_time'];
                                }
                                else
                                {
                                    $end_date_time = $dt2->add(new \DateInterval('PT24H'));
                                    $arr_time_data['interval'] = ($end_date_time->diff($dt1)->h * 60) + $end_date_time->diff($dt1)->i;
                                    $arr_time_data['start']    = $hours['start_time'];
                                    $arr_time_data['end']      = $hours['end_time'];
                                }

                                $tmp_arr[$week_day]['time'][] = $arr_time_data;
                            }
                        }
                    }
                }
                $tmp_arr = array_values($tmp_arr);
                $form->business_hours = json_encode($tmp_arr);
                unset($tmp_arr);

                // 接單時間(orderhours)
                //var_dump($form->orderhours);
                $tmp_arr = array();
                if(count($form->orderhours) >= 1){
                    foreach ($form->orderhours as $hours){
                        //檢查時間
                        if(empty($hours['start_time']) || empty($hours['end_time'])){
                            // 輸出異常
                            //throw new \Exception('接單時間錯誤');
                            // 輸出錯誤訊息
                            $error = new MessageBag([
                                'title'   => '發生錯誤',
                                'message' => '接單時間錯誤',
                            ]);
                            return back()->with(compact('error'));
                        }
                        if(count($hours['week_day']) >= 1){}
                        foreach ($hours['week_day'] as $week_day){
                            if (in_array($week_day, array('1','2','3','4','5','6','7'))){
                                if($week_day == '7'){
                                    $week_day = '0';
                                }
                                $tmp_arr[$week_day]['week'] = $week_day;

                                $arr_time_data = array();
                                $dt1 = new \DateTime($hours['start_time']);
                                $dt2 = new \DateTime($hours['end_time']);
                                
                                if($dt1 < $dt2)
                                {
                                    $arr_time_data['interval'] = ($dt1->diff($dt2)->h * 60) + $dt1->diff($dt2)->i;
                                    $arr_time_data['start']    = $hours['start_time'];
                                    $arr_time_data['end']      = $hours['end_time'];
                                }
                                else
                                {
                                    $end_date_time = $dt2->add(new \DateInterval('PT24H'));
                                    $arr_time_data['interval'] = ($end_date_time->diff($dt1)->h * 60) + $end_date_time->diff($dt1)->i;
                                    $arr_time_data['start']    = $hours['start_time'];
                                    $arr_time_data['end']      = $hours['end_time'];
                                }

                                $tmp_arr[$week_day]['time'][] = $arr_time_data;
                            }
                        }
                    }
                }
                $tmp_arr = array_values($tmp_arr);
                $form->order_hours = json_encode($tmp_arr);
                unset($tmp_arr);
                //exit();
            });
        });
    }
}