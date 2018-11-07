<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\Orders;
use App\Model\idelivery\ProductExchange;
use App\Model\idelivery\Exchanges;
use App\Model\system_member\Point;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrdersController extends Controller
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

            $store_id = Session::get('store_id');

            $content->header('訂單列表');
            $content->description('Order List');

            if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇店家!!');
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
            $store_id = Session::get('store_id');
            $orders = Orders::find($id);
            if (empty($orders) || empty($orders->store_id)
                || $store_id != $orders->store_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('訂單列表');
            $content->description('Order List');

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

            $content->header('訂單列表');
            $content->description('Order List');

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
        Admin::script('$(document).ready(function(){
            $(".grid-expand").trigger("click");

        $(".result_change").click(function(){
            var id = $(this).data("id");
            var status = $(this).closest("td").find(".status").data("status");
            $.ajax({
                    url: "/admin/store/rollback/product_exchange",
                    type: "POST",
                    dataType : "json",
                    cache: false,
                    data: {id: id, status: status},
                    headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                    success: function(data) {
                        toastr.success(data.message);
                        //window.location.replace("/admin/store/point");
                        //window.location = "/admin/store/point/account/search?account="+account+"&country="+country;
                    },
                    error: function() {
                        alert("查無會員資料 !");
                    }
            });

            //$(".points").show();
            //window.location.replace("/admin/store/point/search");
        });

    });

        ');


        return Admin::grid(Orders::class, function (Grid $grid){

            $store_id = Session::get('store_id');
            
            $grid->model()->where('store_id', '=', $store_id);

            $grid->paginate(50);
            $grid->disableCreation();
            $grid->disableActions();
            $grid->disableRowSelector();

            $grid->id('ID')->sortable();
            $grid->sn('訂單號碼');
            $grid->column('member_name', '訂購人');
            $grid->column('purchase_phone', '連絡電話');

            $grid->column('product_delivery', '外帶/外送')->display(function ($product_delivery) {
                $delivery = "";
                switch ($product_delivery)
                {
                    case '1':
                        $delivery = "外送";
                        break;
                    case '2':
                        $delivery = "外帶";
                        break;
                }
                return $delivery;
            });

            $grid->column('prefer_datetime', '取貨/外送時間');
            
            $grid->column('delviery_address', '外送地址')->display(function (){
                $addr = Orders::getAddress($this->id);
                return $addr;
            });

            // $grid->column('payment', '付款方式')->display(function ($payment) {
            //     $pay = "";
            //     switch ($payment)
            //     {
            //         case '1':
            //             $pay = "現金付款";
            //             break;
            //         case '2':
            //             $pay = "線上刷卡付款";
            //             break;
            //     }
            //     return $pay;
            // });

            $grid->column('total_qty', '數量')->style('text-align: right');
            $grid->column('amount', '金額')->style('text-align: right');

            $grid->column('status', '訂單狀態')->display(function ($status) {

                $title = "";

                switch ($status)
                {
                    case '1':
                        $title = "訂單待確認";
                        break;
                    case '2':
                        $title = "餐點製作中";
                        break;
                    case '3':
                        $title = "等待外送";
                        break;
                    case '4':
                        $title = "等待外帶";
                        break;
                    case '5':
                        $title = "外送中";
                        break;
                    case '6':
                        $title = "取餐完成";
                        break;
                    case '7':
                        $title = "無法接單";
                        break;
                }
                    return $title;
            });

            $grid->column('兌換紀錄')->expand(function () {

                $str = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />
                        <label class="label label-info pull-right" style="margin-right:7%">無兌換紀錄</label>';
                $product_exchange = ProductExchange::getList($this->id);

                if (!$product_exchange->isEmpty())
                {
                    $str = '<table class="table table-responsive table-hover" style="margin-left:22%; width:78%">
                            <thead>
                            <tr>
                                <th width="30%">兌換商品</th>
                                <th width="15%">圖片</th>
                                <th width="10%">所需點數</th>
                                <th width="10%">兌換數量</th>
                                <th width="15%">消耗總點數</th>
                                <th width="10%">兌換結果</th>
                            </tr>
                            </thead>
                            <tbody>';

                    $product_exchange_id = array();
                    if (!empty($product_exchange))
                    {
                        foreach ($product_exchange as $row)
                        {
                            $str .= "<tr>
                                <td style='vertical-align: middle'>$row->name</td>";

                            if (!empty($row->picture))
                            {
                                $str .= "<td style='vertical-align: middle'><img src='". env('ADMIN_UPLOAD_URL', '') . $row->image ."' width = '50px'></td>";
                            }
                            else
                            {
                                $str .= "<td style='vertical-align: middle'></td>";
                            }

                            $str .= "<td style='vertical-align: middle; text-align: right;'>$row->point</td>
                                    <td style='vertical-align: middle; text-align: right;'>$row->qty</td>
                                    <td style='vertical-align: middle; text-align: right;'>$row->total_point</td>";

                            if ($row->status == 1)
                            {
                                $str .= "<td style='vertical-align: middle'><span class='label label-success status' data-status=".$row->status.">成功</span></td></tr>";
                            }
                            else
                            {
                                $str .= "<td style='vertical-align: middle'><span class='label label-danger status' data-status=".$row->status.">失敗</span></td></tr>";
                            }

                            $product_exchange_id[] = $row->id;
                        }
                    }
                    $str .= '</tbody></table>';

                    // if (!empty($product_exchange))
                    // {
                    //     $str .= "<span style='margin-left: 85%' class='btn btn-primary result_change' data-id=". 
                    //             json_encode($product_exchange_id).">變更兌換結果 <i class='fa fa-edit'></i></span><p></p><hr>";
                    // }
                }

                return $str;

            }, '兌換明細');


            $grid->column('明細')->display(function () {
                return '<a href="/admin/store/orders_detail/'.$this->id.'"><i class="fa fa-list"></i></a>';
            });

            $grid->filter(function($filter){

                //$filter->disableIdFilter();
                //$filter->removeIDFilterIfNeeded();

                $filter->between('create_time', '訂單日期')->date();
                $filter->equal('purchase_phone', '會員電話');

                $filter->equal('product_delivery', '外送/外帶')->radio([
                    ''   => '全部',
                    1    => '外送',
                    2    => '外帶',
                ]);

                $filter->equal('status', '訂單狀態')->select(['1' => '訂單待確認', '2' => '餐點製作中', 
                    '3' => '等待外送', '4' => '等待外帶', '5' => '外送中', '6' => '取餐完成', '7' => '無法接單/取消訂單']);
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Orders::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }


    // public function rollBack(Request $request)
    // {
    //     if (!empty($request))
    //     {
    //         if (!empty($request['status']) && !empty($request['id']))
    //         {
    //             $service_id = config('damaiapp.SERVICE_ID');
    //             $company_id = Session::get('company_id');
    //             $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');
    //             $product_exchange = ProductExchange::getView($request['id']);

    //             if (empty($product_exchange))
    //             {
    //                 $error = new MessageBag([
    //                     'title'   => '訊息',
    //                     'message' => '資料錯誤 !',
    //                 ]);
    //                 return redirect('admin/store/orders')->with(compact('error'));
    //             }

    //             $member_id = empty($product_exchange) ? 0 : $product_exchange->member_id;
    //             $member_detail_id = empty($product_exchange) ? 0 : $product_exchange->member_detail_id;
                
    //             $total_point = Point::getTotalPoint($service_id, $company_id, $member_id, $store_id);

    //             if ($total_point === false || $total_point <= 0)
    //             {
    //                 $error = new MessageBag([
    //                     'title'   => '訊息',
    //                     'message' => '會員點數資料錯誤 !',
    //                 ]);
    //                 return redirect('admin/store/orders')->with(compact('error'));
    //             }
    
    //             foreach ($request['id'] as $row)
    //             {
    //                 //兌換狀態由成功變失敗
    //                 if ($request['status'] == 1)
    //                 {
    //                     //加回庫存及點數
    //                     $result = ProductExchange::OrderExchangeRollBack($service_id, $company_id, $store_id, $product_exchange->id);
    //                     if ($result === false)
    //                     {
    //                         $error = new MessageBag([
    //                             'title'   => '訊息',
    //                             'message' => '商品兌換狀態變更失敗 !',
    //                         ]);
    //                         return redirect('admin/store/orders')->with(compact('error'));
    //                     }
    
    //                     //變更兌換狀態
    //                     $status = 0;
    //                     $result = ProductExchange::updateStatus($product_exchange->id, $status);

    //                     if ($result === false)
    //                     {
    //                         $error = new MessageBag([
    //                             'title'   => '訊息',
    //                             'message' => '商品兌換狀態變更失敗 !',
    //                         ]);
    //                         return redirect('admin/store/orders')->with(compact('error'));
    //                     }
    
    //                 }
    //                 else    //兌換狀態由失敗變成功
    //                 {
    //                     $exchanges = Exchanges::getView($product_exchange->exchanges_id);

    //                     if ($exchanges->isEmpty())
    //                     {
    //                         $error = new MessageBag([
    //                             'title'   => '訊息',
    //                             'message' => '查無兌換商品資料 !',
    //                         ]);
    //                         return redirect('admin/store/orders')->with(compact('error'));
    //                     }

    //                     $args = array(
    //                         'service_id'        => $service_id,
    //                         'company_id'        => $company_id,
    //                         'store_id'          => $store_id,
    //                         'member_id'         => $member_id,
    //                         'member_detail_id'  => $member_detail_id,
    //                         'date'              => Carbon::now(),
    //                         'exchanges_id'      => $product_exchange->exchanges_id,
    //                         'qty'               => $product_exchange->qty,
    //                         'point_type_id'     => $exchanges->point_type_id,
    //                         'point_before'      => $total_point,
    //                         'point_after'       => $total_point - ($exchanges->point * $product_exchange->qty),
    //                         'orders_id'         => $product_exchange->orders_id,
    //                         'status'            => 1,
    //                         'created_at'        => Carbon::now(),
    //                     );

    //                     //新增兌換紀錄
    //                     $result = ProdcutExchange::add($args);

    //                     //扣庫存及點數
    //                     $para_data = array(
    //                         'service_id'            => $service_id, 
    //                         'member_id'             => $member_id,
    //                         'company_id'            => $company_id, 
    //                         'store_id'              => $store_id, 
    //                         'member_detail_id'      => $member_detail_id,
    //                         'operating_role'        => '', 
    //                         'description'           => empty($data_input['raw']['description']) ? "" : $data_input['raw']['description'], 
    //                         'point_type_id'         => $data_input['raw']['point_type_id'], 
    //                         'point_deducted_total'  => $data_input['raw']['point_deducted_total'], 
    //                         'order_id'              => empty($data_input['raw']['exchange_src_id']) ? 0 : $data_input['raw']['exchange_src_id']   
    //                     );
            
    //                     //寫入點數資料表
    //                     $result = Point::reduce($para_data);
            
    //                 }
    //             }
    //         }


    //         $response = ['status' => 'success', 'message' => $request['id'][1]]; 
    //         echo json_encode($response);
    //         exit();
    //     }

    //     $response = ['status' => 'error', 'message' => '資料錯誤, 兌換狀態變更失敗!']; 
    //     echo json_encode($response);
    //     exit();

    // }

}
