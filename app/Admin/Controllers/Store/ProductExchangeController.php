<?php

namespace App\Admin\Controllers\Store;

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
use Illuminate\Support\MessageBag;

use App\Model\idelivery\ProductExchange;
use DB;


class ProductExchangeController extends Controller
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
            $service_id = config('damaiapp.SERVICE_ID');
            $company_id = Session::get('company_id');
            $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');

            $content->header('現場商品兌換紀錄');
            $content->description('On Site Exchange');

            if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
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
    // public function edit($id)
    // {
    //     return Admin::content(function (Content $content) use ($id) {

    //         $content->header('現場商品兌換紀錄');
    //         $content->description('On Site Exchange');

    //         $content->body($this->form()->edit($id));
    //     });
    // }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ProductExchange::class, function (Grid $grid){

            $service_id = config('damaiapp.SERVICE_ID');
            $company_id = Session::get('company_id');
            $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');
            
            $grid->model()->getListByOnSide($service_id, $company_id, $store_id);

            $grid->paginate(20);
            $grid->disableCreation();
            $grid->disableActions();
            $grid->disableRowSelector();

            $grid->id('ID')->sortable();

            $grid->column('date', '兌換日期');

            $grid->name('兌換商品');

            $grid->image('商品圖')->display(function () {
                if (!empty($this->image))
                {
                    return '<img src="' . env('ADMIN_UPLOAD_URL', '') . $this->image.'" width = "50px">';
                }
                else 
                {
                    return '';
                }
            });

            $grid->column('point_type_id', '點數類型')->display(function () {
                switch ($this->point_type_id)
                {
                    case 2:
                        return '<span class="label label-danger">紅利點數</span>';
                    case 3:
                        return '<span class="label label-primary">團體集點</span>';
                    case 1:
                    default:
                        return '<span class="label label-success">點數</span>';

                }
            });

            $grid->point('點數');
            $grid->qty('兌換數量');
            $grid->column('total_point', '兌換總點數');

            $grid->column('status', '狀態')->display(function () {
                switch ($this->status)
                {
                    case '1':
                        return '<span class="label label-success">成功</span>';
                    default:
                        return '<span class="label label-danger">失敗</span>';
                }
            });

            $grid->column('變更狀態')->display(function () {
                return '<a href="/admin/store/set/product_exchange/'.$this->id.'/edit"><i class="fa fa-edit" aria-hidden="true"></i></a>';
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
        // return Admin::form(Point::class, function (Form $form) {

        //     $form->display('id', 'ID');

        //     $form->display('created_at', 'Created At');
        //     $form->display('updated_at', 'Updated At');
        // });
    }

    public function edit($id)
    {
        if (!empty($id))
        {
            $service_id = config('damaiapp.SERVICE_ID');
            $company_id = Session::get('company_id');
            $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');

            $product_exchange = ProductExchange::getView($id);

            if (empty($product_exchange) || empty($product_exchange->store_id)
                || $store_id != $product_exchange->store_id)
            {
                $error = new MessageBag([
                    'title'   => '訊息',
                    'message' => '您無權限訪問本頁面 !',
                ]);
                return redirect('admin/store/set/product_exchange')->with(compact('error'));
            }

            if (!empty($product_exchange) && !empty($product_exchange->id) && !empty($product_exchange->status))
            {
                if ($product_exchange->status == 1)
                {
                    
                    $result = ProductExchange::ExchangeRollBack($service_id, $company_id, $store_id, $product_exchange->id);
                    if ($result === false)
                    {
                        $error = new MessageBag([
                            'title'   => '訊息',
                            'message' => '商品兌換狀態變更失敗 !',
                        ]);
                        return redirect('admin/store/set/product_exchange')->with(compact('error'));
                    }

                    $status = 0;
                    $result = ProductExchange::updateStatus($product_exchange->id, $status);

                    if ($result === false)
                    {
                        $error = new MessageBag([
                            'title'   => '訊息',
                            'message' => '商品兌換狀態變更失敗 !',
                        ]);
                        return redirect('admin/store/set/product_exchange')->with(compact('error'));
                    }

                    $success = new MessageBag([
                        'title'   => '訊息',
                        'message' => '商品兌換狀態變更成功, 並已加回庫存及點數 !',
                    ]);
                    return redirect('admin/store/set/product_exchange')->with(compact('success'));
                }
                else
                {
                    $success = new MessageBag([
                        'title'   => '訊息',
                        'message' => '兌換狀態為失敗, 請重新進行現場商品兌換作業即可 !',
                    ]);
                    return redirect('admin/store/set/product_exchange')->with(compact('success'));
                }
            }
            else
            {
                if (empty($product_exchange->status))
                {
                    $success = new MessageBag([
                        'title'   => '訊息',
                        'message' => '兌換狀態為失敗, 請重新進行現場商品兌換作業即可 !',
                    ]);
                    return redirect('admin/store/set/product_exchange')->with(compact('success'));
                }
            }
        }

        $error = new MessageBag([
            'title'   => '訊息',
            'message' => '資料錯誤, 無法變更商品兌換狀態 !',
        ]);
        return redirect('admin/store/set/product_exchange')->with(compact('error'));
    }



}
