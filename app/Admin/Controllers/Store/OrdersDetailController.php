<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\OrdersDetail;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class OrdersDetailController extends Controller
{
    use ModelForm;

    public $order_id;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        $order_id = 0;
        
        if (!empty($request))
        {
            $order_id = $request->id;
        }

        return Admin::content(function (Content $content) use ($order_id) {
            $store_id = Session::get('store_id');
            $orders = OrdersDetail::getOrders($order_id);
            if (empty($orders) || empty($orders->store_id)
                || $store_id != $orders->store_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            Session::put('order_id', $order_id);
            $content->header('訂單明細');
            $content->description('Order Detail');
           if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            }else{
                $order = OrdersDetail::getOrders($order_id);
                
                $box = new Box('訂單資訊',  '<div class="col-md-3">訂單號碼: '.$order->sn.'</div>
                                            <div class="col-md-3">訂購人: '.$order->member_name.'</div>
                                            <div class="col-md-3">數量: '.$order->total_qty.'</div>
                                            <div class="col-md-3">金額: '.number_format($order->amount).'</div>');
    
                $content->row($box->collapsable());
    
                $content->row(function(Row $row) {
                    $row->column(12, $this->grid());
                });
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
            $content->header('訂單明細');
            $content->description('Order Detail');

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

            $content->header('訂單明細');
            $content->description('Order Detail');

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
        return Admin::grid(OrdersDetail::class, function (Grid $grid) {

            $grid->paginate(50);
            $grid->disableCreation();
            $grid->disableActions();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->model()->Where('order_id', Session::get('order_id'));

            $grid->column('purchaser_name', '購買者');
            $grid->column('item_name', '商品名稱');

            $grid->column('item_optional', '規格')->display(function ($item_optional) {

                $data = json_decode($item_optional, true);
                $options = "";
                $extra_price = 0;

                if (!empty($data))
                {
                    foreach ($data['option'] as $rows)
                    {                           
                        if (!empty($rows['attribute']))
                        {
                            foreach ($rows['attribute'] as $attr)
                            {
                                if ($attr['selected'] == true)
                                {
                                    $options .= '<span class="label label-success" style="margin-right: 5px">'.$attr["title"].'</span>';

                                    // if ($attr['extra_price'] !== 0)
                                    // {
                                    //     $options .= "<span class='label label-success'>{$attr['title']}</span>&nbsp;&nbsp;"
                                    //                 ."<span class='label label-primary'>加 " . strval($attr['extra_price']) . "元</span>";
                                        
                                    // }
                                    // else
                                    // {
                                    //     $options .= "<span class='label label-success'>{$attr['title']}</span>&nbsp;&nbsp;"
                                    //                 ."<span class='label label-primary'>免費</span>";
                                    // }    
                                }
                            }
                        }
                    }
    
                    $options = substr($options, 0, -2);
                    return $options;
                }
                else
                {
                    return "";
                }
            });

            $grid->column('item_price', '商品價格')->style('text-align: right')->display(function ($item_price) {
                return number_format($item_price);
            });

            $grid->column('qty', '商品數量')->style('text-align: right')->display(function ($qty) {
                return number_format($qty);
            });

            $grid->column('sub_price', '金額小計')->style('text-align: right')->display(function ($sub_price) {
                    return number_format($sub_price);
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });

            $grid->rows(function($row){
                
                if($row->qty < 0) {
                    $row->style('background-color: #D81B60; color: white;');
                }

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
        return Admin::form(OrdersDetail::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
