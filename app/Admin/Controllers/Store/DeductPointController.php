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
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Carbon\Carbon;

use App\Model\System\PointConsumeLog;
use App\Model\System\Point;
use App\Model\System\PointType;
use App\Model\System\Member;
use DB;
//use Auth;

class DeductPointController extends Controller
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
            $content->header('扣點');
            $content->description('Points Deduction');

            if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            }else{
                $content->body($this->grid());
            }
        });
    }


    /**
     * Save interface.
     *
     * @param $id
     * @return Content
     */
    public function store(Request $request)
    {
        if (!empty($request))
        {
            if (empty($request->input('account')) || empty($request->input('service_id')) || 
                empty($request->input('company_id')) || empty($request->input('description')) ||
                empty($request->input('point')))
            {
                $error = new MessageBag([
                    'title'   => '錯誤',
                    'message' => '資料錯誤 !',
                ]);
                return back()->with(compact('error'));
            }

            $member = Member::getView($request->input('account'), $request->input('service_id'), $request->input('company_id'));
            if ($member->isEmpty())
            {   
                $result = Member::MemberRegister($request->input('account'), 
                    $request->input('service_id'), $request->input('company_id'));

                $member = Member::getView($request->input('account'), $request->input('service_id'), $request->input('company_id'));

            }

            $member_id = $member[0]->member_id;
            $member_detail_id = $member[0]->id;
            $operating_role = Admin::user()->username;

            $args = array(
                'service_id'            => $request->input('service_id'),
                'company_id'            => $request->input('company_id'),
                'store_id'              => $request->input('store_id'),
                'member_id'             => $member_id,
                'member_detail_id'      => $member_detail_id,
                'operating_role'        => $operating_role,
                'description'           => $request->input('description'),
                'point_type_id'         => $request->input('point_type'),
                'point_deducted_total'  => $request->input('point'),
                'exchange_type'         => "admin_users",
                'exchange_id'           => '0',
                'created_at'            => Carbon::now()
            );
    
            $result = Point::reduce($args);
            if ($result === false)
            {
                $error = new MessageBag([
                    'title'   => '錯誤',
                    'message' => '點數扣除失敗 !',
                ]);
                return back()->with(compact('error'));
            }
            else
            {
                $success = new MessageBag([
                    'title'   => '訊息',
                    'message' => '點數扣除成功 !',
                ]);
                return redirect('admin/store/point/deduct_point')->with(compact('success'));
            }
        }
        else
        {
            $error = new MessageBag([
                'title'   => '錯誤',
                'message' => '點數扣除失敗 !',
            ]);
            return back()->with(compact('error'));
        }

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
            $content->header('扣點');
            $content->description('Points Deduction');
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
            $content->header('扣點');
            $content->description('Points Deduction');
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
        return Admin::grid(Point::class, function (Grid $grid) {

            $grid->paginate(50);

            $company_id = Session::get('company_id');
            $service_id = config('damaiapp.SERVICE_ID');

            $grid->model()->getList($service_id, $company_id);

            $grid->account('會員帳號');
            //$grid->name('會員名稱');
            $grid->point_type('點數類型');
            $grid->point_surplus('剩餘點數')->style('text-align: right');
            $grid->expired_at('逾期時間')->style('text-align: center');

            $grid->disableRowSelector();
            $grid->disableActions();

            $grid->filter(function($filter){           
                $filter->like('account', '會員帳號');
            });
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(Point::class, function (Form $form) {
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');
            $service_id = config('damaiapp.SERVICE_ID');

            $form->hidden('company_id','品牌編號')->default($company_id);           
            $form->hidden('store_id','店家編號')->default($store_id); 
            $form->hidden('service_id','服務編號')->default($service_id); 
            $form->hidden('operating_role','操作角色')->default(Admin::user()->username); 
            $form->hidden('order_id','訂單編號')->default(0);

            $form->text('account', '會員帳號')->rules('required');
            //$form->display('name', '會員名稱');

            $form->select('point_type', '點數類型')
                ->options(PointType::pluck('name', 'id'))->rules('required');;
            $form->number('扣點數')->rules('required');;
            $form->text('description', '說明')->rules('required');

        });
    }

}
