<?php

namespace App\Admin\Controllers;

use App\Model\idelivery\Store;
use App\Model\idelivery\Orders;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\InfoBox;

use Illuminate\Support\Facades\Session;

class StoreController extends Controller
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
            $content->header('店家');
            $content->description('Description...');

            $store_id = Session::get('store_id');

            $content->row(function ($row) use ($store_id) {
                $row->column(6, new InfoBox('本日訂單數', 'users', 'aqua', '#', Orders::getStoreTodayOrders($store_id)));
                $row->column(6, new InfoBox('本日訂購金額', 'shopping-cart', 'green', '#', Orders::getStoreTodayOrdersAmount($store_id)));
            });

            $content->row(function ($row) use ($store_id) {
                $row->column(6, new InfoBox('本月訂單數', 'users', 'aqua', '#', Orders::getStoreMonthlyOrders($store_id)));
                $row->column(6, new InfoBox('本月訂購金額', 'shopping-cart', 'green', '#', Orders::getStoreMonthlyOrdersAmount($store_id)));
            });
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

            $content->header('header');
            $content->description('description');

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

            $content->header('header');
            $content->description('description');

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

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
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

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }


    protected function info()
    {
        echo '12345';exit(0);
    }
}
