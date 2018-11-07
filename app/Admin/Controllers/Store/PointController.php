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
use Encore\Admin\Layout\Row;

use App\Models\system_member\Point;
use App\Models\system_member\Member;
use App\Models\system_member\Member_detail;
use App\Models\system_member\Point_consume_log;

use DB;
//use Auth;

class PointController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    //public function index(Request $request)
    public function index()
    {
        return Admin::content(function (Content $content) {
            $store_id = Session::get('store_id');

            $content->header('加扣點');
            $content->description('Points Processing');

            if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            }else{
                $content->body(new Box('會員查詢', view('admin.points.query')));
            }
        });
    }


    public function search()
    {
        return Admin::content(function (Content $content) {

            $content->header('加扣點');
            $content->description('Points Processing');

            $content->row(new Box('會員查詢', view('admin.points.query')));

            $msg   = '查詢結果';
            $style = 'primary';

            if (!empty(request()->all()))
            {
                $country = request()->query('country', '');
                $account = request()->query('account', '');
                $header = null;

                if (!empty($country) && !empty($account))
                {
                    $member = Member::where("country", $country)
                        ->where("account", $account)
                        ->first();

                    if (!empty($member))
                    {
                        $member_detail = Member_detail::where("member_id", $member->id)
                            ->where("service_id", config('damaiapp.SERVICE_ID'))
                            ->where("company_id", Session::get('company_id'))
                            ->first();

                        if (!empty($member_detail))
                        {
                            $points = Point::where("member_id", $member_detail->id)
                                ->where("service_id", config('damaiapp.SERVICE_ID'))
                                ->where("company_id", Session::get('company_id'))
                                ->orderBy("expired_at", "ASC")
                                ->get();
                        }

                        $header = array(
                            "name" => empty($member_detail->name) ? "" : $member_detail->name,
                            "country" => empty($country) ? "" : $country,
                            "account" => empty($account) ? "" : $account,
                            //"point_type" => empty($points) ? "" : $points[0]->point_type->name,
                            "total_points" => ($points->isEmpty()) ? 0 : $points->sum("point_surplus"),
                            "last_expired_at" => ($points->isEmpty()) ? "" : $points[0]->expired_at,
                        );

                        $point_consume_log = Point_consume_log::join('point_type', 'point_type.id', '=', 'point_consume_log.point_type_id')
                            ->where('point_consume_log.service_id', config('damaiapp.SERVICE_ID'))
                            ->where('point_consume_log.company_id', Session::get('company_id'))
                            ->where('point_consume_log.member_id', $member->id)
                            ->where('point_consume_log.exchange_src_id', 0)  //限後台扣點
                            ->select('point_consume_log.store_id',
                                'point_type.name as point_type',
                                'point_consume_log.point_deducted_total as point',
                                'point_consume_log.description',
                                'point_consume_log.created_at',
                                'point_consume_log.exchange_src_id as order_id',
                                DB::raw('2 as status')
                            );

                        $lists = Point::join('point_type', 'point_type.id', '=', 'point.point_type_id')
                            ->where('point.service_id', config('damaiapp.SERVICE_ID'))
                            ->where('point.company_id', Session::get('company_id'))
                            ->where('point.member_id', $member->id)
                            ->where('point.order_id', 0)  //限後台灌點
                            ->select('point.store_id',
                                'point_type.name as point_type',
                                'point.point_surplus as point',
                                'point.description',
                                'point.created_at',
                                'point.order_id',
                                DB::raw('case when point.expired_at >= now() then 1 when point.expired_at < now() then 3 end as status'))
                            ->union($point_consume_log)
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);
                    }
                }
                else
                {
                    $rows  = [];
                    $msg   = '查無相關資料';
                    $style = 'danger';
                }

//                $content->row(new Box('會員點數', view('admin.points.header', ['header'=>$header])));
//                $content->row(new Box('加扣點歷史紀錄', view('admin.points.result', ['lists'=>$lists])));

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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Point::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }


    public function PointHistoryList($account, $country)
    {
        // return Admin::grid(Point::class, function (Grid $grid) use ($account, $country) {

        //     $service_id = config('damaiapp.SERVICE_ID');
        //     $company_id = Session::get('company_id');
        //     $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');
            
        //     $member = Member::getID($account, $country);
        //     if (empty($member))
        //     {
        //         $member_id = 0;
        //     }
        //     else
        //     {
        //         $member_id = $member->id;
        //     }

        //     $grid->paginate(50);
        //     $grid->disableRowSelector();
        //     $grid->disableActions();

        //     $grid->model()->getPointHistory($service_id, $member_id, $company_id);

        //     $grid->created_at('日期');
        //     $grid->store_name('店家');
        //     $grid->point_type('點數類型');
        //     $grid->point('點數');
        //     $grid->description('說明');
        //     $grid->status('狀態');

        // });



        $service_id = config('damaiapp.SERVICE_ID');
        $company_id = Session::get('company_id');
        $store_id = Session::get('store_id');
        
        $member = Member::where("country", $country)
            ->where("account", $account)
            ->first();

        if (empty($member))
        {
            $member_id = 0;
        }
        else
        {
            $member_id = $member->id;
        }

        $points = Point::getPointHistory($service_id, $member_id, $company_id);
        $headers = ['日期', '店家', '點數類型', '點數', '說明', '狀態'];
        $rows = [];

        foreach ($points as $key=>$point) 
        {   
            switch ($point->status)
            {
                case 2:
                    $point->status = '<span class="label label-danger">扣點</span>';
                    break;
                case 3:
                    $point->status = '<span class="label label-warning">逾期</span>';
                    break;
                case 1:
                    $point->status = '<span class="label label-success">加點</span>';
                    break;
            }
            
            $rows[] = [
                $point->created_at,
                $point->store_name,
                $point->point_type,
                $point->point,
                $point->description,
                $point->status
            ];
        }

        $table = new Table($headers, $rows);
        
        $box = new Box("加扣點歷史紀錄 (顯示最近20筆資料)", 
                        '<div class="box box-info">
                            <div class="box-header with-border">'.$table.
                        '</div></div>');
        $box->collapsable();

        return $box;

    }

    /**
     * 取得點數歷程資料
     * @param service_id         = 服務編號
     * @param member_id          = 會員編號
     * @param company_id         = 公司編號
     *
     *
     * return TRUE = 成功 OR FALSE = 失敗
     */
    public function get_point_history($service_id = 0, $member_id = 0, $company_id = 0)
    {
        $point_consume_log = Point_consume_log::join('point_type', 'point_type.id', '=', 'point_consume_log.point_type_id')
            ->where('point_consume_log.service_id', $service_id)
            ->where('point_consume_log.company_id', $company_id)
            ->where('point_consume_log.member_id', $member_id)
            ->where('point_consume_log.exchange_src_id', 0)  //限後台扣點
            ->select('point_consume_log.store_id',
                'point_type.name as point_type',
                'point_consume_log.point_deducted_total as point',
                'point_consume_log.description',
                'point_consume_log.created_at',
                'point_consume_log.exchange_src_id as order_id',
                DB::raw('2 as status')
            );

        $points = Point_consume_log::join('point_type', 'point_type.id', '=', 'point.point_type_id')
            ->where('point.service_id', $service_id)
            ->where('point.company_id', $company_id)
            ->where('point.member_id', $member_id)
            ->where('point.order_id', 0)  //限後台灌點
            ->select('point.store_id',
                'point_type.name as point_type',
                'point.point_surplus as point',
                'point.description',
                'point.created_at',
                'point.order_id',
                DB::raw('case when point.expired_at >= now() then 1 when point.expired_at < now() then 3 end as status')
            )
            ->union($point_consume_log)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $store = Store::select('id', 'name')->get();

        $result = array();
        if (!$points->isEmpty())
        {
            foreach ($points as $row)
            {
                if (!$store->isEmpty())
                {
                    foreach ($store as $val)
                    {
                        if ($row->store_id == $val->id)
                        {
                            $row->store_name = $val->name;
                            break;
                        }
                    }
                }

                $result[] = $row;
            }
        }

        return $result;
    }


    public function accountSearch(Request $request)
    {
        Admin::script('
            // $(document).ready(function() {
            //     $("table").attr("id", "list");
            //     $("#list").addClass("datatable");
            // });

            // $("#list").DataTable({
            //     "paging"       : true,
            //     "lengthChange" : false,
            //     "searching"    : false,
            //     "ordering"     : true,
            //     "info"         : true,
            //     "autoWidth"    : false
            // });

            $("#search").click(function(){
                var account = $("#account").val();
                var country = $("#country").val();
                window.location = "/admin/store/point/account/search?account="+account+"&country="+country;
            });

            $("#add").click(function(){
                var point_type = $("#point_type_add").val();
                var point = $("#apoint").val();
                var desc = $("#adesc").val();
                var acc = $("#acc").text();
                var country = $("#nation").text();
                var vdate = $("#valid_date").val();
                $.ajax({
                        url: "/admin/store/point/management",
                        type: "POST",
                        dataType : "json",
                        cache: false,
                        data: {account: acc, country: country, point_type: point_type, point: point, description: desc, valid_date: vdate, action: "add"},
                        headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                        success: function(data) {
                            toastr.success(data.message);
                            //window.location.replace("/admin/store/point");
                            window.location = "/admin/store/point/account/search?account="+acc+"&country="+country;
                        },
                        error: function() {
                            alert("加扣點作業失敗 !");
                        }
                });
            });

            $("#reduce").click(function(){
                var point_type = $("#point_type_reduce").val();
                var point = $("#rpoint").val();
                var desc = $("#rdesc").val();
                var acc = $("#acc").text();
                var country = $("#nation").text();
                $.ajax({
                        url: "/admin/store/point/management",
                        type: "POST",
                        dataType : "json",
                        cache: false,
                        data: {account: acc, country: country, point_type: point_type, point: point, description: desc, action: "reduce"},
                        headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                        success: function(data) {
                            toastr.success(data.message);
                            //window.location.replace("/admin/store/point");
                            window.location = "/admin/store/point/account/search?account="+acc+"&country="+country;
                        },
                        error: function() {
                            alert("加扣點作業失敗 !");
                        }
                });
            });'
        );

        $country = "886";
        if (!empty($request) && !empty(trim($request['country'])))
        {
            $country = trim($request['country']);
        }

        $account = "";
        if (!empty($request) && !empty(trim($request['account'])))
        {
            $account = trim($request['account']);
        }

        return Admin::content(function (Content $content) use ($account, $country) {

            $service_id = config('damaiapp.SERVICE_ID');
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');

            $content->header('加扣點');
            $content->description('Points Processing');

            if(empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            }else
            {

                $box1 = new Box('會員查詢', '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />
                    <div class="col-md-4">    
                        <div class="input-group">
                            <span class="input-group-addon">國碼：</span>
                            <input type="text" class="form-control" placeholder="請輸入手機國碼" aria-label="country"
                                aria-describedby="basic-addon1" id="country" value="'.$country.'">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="請輸入會員手機號碼" aria-label="account"
                                aria-describedby="basic-addon2" id="account" value="'.$account.'">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit" id="search">查詢</button>
                                </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br/>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger" data-acc='.(empty($points) ? 0 : $points[0]->account).'>扣點</button>
                        <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal-success" data-acc='.(empty($points) ? 0 : $points[0]->account).'>加點</button>
                    </div>

                    <div class="modal modal-danger fade" id="modal-danger">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">扣點</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="rpoint" class="col-sm-2 control-label">點數</label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="rpoint" placeholder="點數">
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="form-group">
                                <label for="rdesc" class="col-sm-2 control-label">說明</label>

                                <div class="col-sm-10">
                                    <textarea rows="3" class="form-control" id="rdesc" placeholder="說明"></textarea>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">關閉</button>
                            <button type="button" class="btn btn-outline" id="reduce">扣點</button>
                        </div>
                        </div>
                    </div>
                    </div>

                    <div class="modal modal-success fade" id="modal-success">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">加點</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="apoint" class="col-sm-2 control-label">點數</label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="apoint" placeholder="點數">
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="form-group">
                                <label for="adesc" class="col-sm-2 control-label">說明</label>

                                <div class="col-sm-10">
                                    <textarea rows="3" class="form-control" id="adesc" placeholder="說明"></textarea>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="form-group">
                                <label for="valid_date" class="col-sm-2 control-label">有效期限</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="valid_date">
                                        <option>三個月</option>
                                        <option>半年</option>
                                        <option>一年</option>
                                        <option>一年半</option>
                                        <option>二年</option>
                                        <option>二年半</option>
                                        <option>三年</option>
                                    </select>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                        <div class="modal-footer">
                            <p></p>
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">關閉</button>
                            <button type="button" class="btn btn-outline" id="add">加點</button>
                        </div>
                        </div>
                    </div>
                    </div>
                ');
    
                $content->row($box1->collapsable());

                $member = Member::where("country", $country)
                    ->where("account", $account)
                    ->first();

                if (empty($member))
                {
                    admin_toastr('查無會員資料!','warning');
                    return back();
                }

                $member_detail = Member_detail::where("member_id", $member->id)
                    ->where("service_id", $service_id)
                    ->where("company_id", $company_id)
                    ->first();

                if (empty($member_detail))
                {
                    admin_toastr('查無正式會員資料!','warning');
                    return back();
                }

                $points = Point::join("member_detail", function($join) {
                        $join->on("member_detail.service_id", "=", "point.service_id");
                        $join->on("member_detail.company_id", "=", "point.company_id");
                        $join->on("member_detail.id", "=", "point.member_detail_id");
                    })
                    ->join("point_type", "point_type.id", "=", "point.point_type_id")
                    ->where("point.service_id", $service_id)
                    ->where("point.company_id", $company_id)
                    ->where("point.member_detail_id", $member_detail->id)
                    ->where("point.expired_at", ">=", date("Y-m-d H:i:s"))
                    ->select("point_type.name as point_type", "point.point_surplus", "point.expired_at")
                    ->orderBy("point.expired_at", "ASC")
                    ->get();
                //$points = Point::getList($service_id, $company_id, $country, $account);

                if (!$points->isEmpty())
                {
                    $str = "<div class='row'>
                                <div class='col-md-4'>姓名：".(empty($member_detail->name) ? "" : $member_detail->name)."</div>
                                <div class='col-md-4'>國碼：<label id='nation'>".(empty($member->country) ? "" : $member->country)."</label></div>
                                <div class='col-md-4'>帳號：<label id='acc'>".(empty($member->account) ? "" : $member->account)."</label></div>
                                <div class='col-md-4'>點數種類：".(empty($points) ? "" : $points[0]->point_type)."</div>
                                <div class='col-md-4'>總點數：".(empty($points) ? 0 : number_format($points->sum("point_surplus")))."</div>
                                <div class='col-md-4'>點數最近逾期日期：".(empty($points) ? "" : date("Y-m-d", strtotime($points[0]->expired_at)))."</div>
                            </div>";


//                    $str = "<div class='row'>
//                                <div class='col-md-4'>姓名：".(empty($points) ? "" : $points[0]->name)."</div>
//                                <div class='col-md-4'>國碼：<label id='nation'>".(empty($points) ? 0 : $points[0]->country)."</label></div>
//                                <div class='col-md-4'>帳號：<label id='acc'>".(empty($points) ? 0 : $points[0]->account)."</label></div>
//                                <div class='col-md-4'>點數種類：".(empty($points) ? "" : $points[0]->point_type)."</div>
//                                <div class='col-md-4'>總點數：".(empty($points) ? 0 : number_format($points[0]->point_surplus))."</div>
//                                <div class='col-md-4'>點數最近逾期日期：".(empty($points) ? "" : date("Y-m-d", strtotime($points[0]->expired_at)))."</div>
//                            </div>";

                    $box2 = new Box('會員點數', $str);

                    $content->row($box2->collapsable());
                }
                else
                {
                    $error = new MessageBag([
                        'title'   => '錯誤訊息: ',
                        'message' => '查無點數資料, 請重新確認 !',
                    ]);

                    return back()->with(compact('error'));
                }

                $content->row(function(Row $row) use ($account, $country) {
                    $row->column(12, $this->PointHistoryList($account, $country));
                });


            }

        });
    }


    public function pointProcessing(Request $request)
    {
        if (!empty($request))
        {
            $country = "886";
            if (!empty(trim($request['country'])))
            {
                $country = trim($request['country']);
            }
    
            $account = "";
            if (!empty(trim($request['account'])))
            {
                $account = trim($request['account']);
            }
    
            $point = 0;
            if (!empty($request['point']))
            {
                $point = $request['point'];
            }

            $description = "";
            if (!empty($request['description']))
            {
                $description = $request['description'];
            }

            if (!empty($request['action']))
            {
                $service_id = config('damaiapp.SERVICE_ID');
                $company_id = Session::get('company_id');
                $store_id = Session::get('store_id');
                $point_type_id = 1;

                $member = Member::getID($account, $country);
                $member_id = 0;

                if (!empty($member))
                {
                    $member_id = $member->id;
                }

                $member = Member::getDetailID($service_id, $company_id, $member_id);
                $member_detail_id = 0;

                if (!empty($member))
                {
                    $member_detail_id = $member->id;
                }

                if ($request['action'] == 'add')
                {                    
                    if (empty($description))
                    {
                        $description = '後台加點';
                    }

                    if (!empty($request['valid_date']))
                    {   
                        $date = "";
                        switch ($request['valid_date'])
                        {
                            case '三個月':
                                $date = "+3 months";
                                break;
                            case '半年':
                                $date = "+6 months";
                                break;
                            case '一年':
                                $date = "+1 year";
                                break;
                            case '一年半':
                                $date = "+18 months";
                                break;
                            case '二年':
                                $date = "+2 years";
                                break;
                            case '二年半':
                                $date = "+130 months";
                                break;
                            case '三年':
                                $date = "+3 years";
                                break;
                        }

                        $args = array(
                            'service_id'        => $service_id,
                            'company_id'        => $company_id,
                            'store_id'          => $store_id,
                            'member_id'         => $member_id,
                            'member_detail_id'  => $member_detail_id,
                            'operating_role'    => Admin::user()->username,
                            'description'       => $description,
                            'point_type_id'     => $point_type_id,
                            'order_id'          => 0, //後台灌點不會有訂單編號
                            'point'             => $point,
                            'point_surplus'     => $point,
                            'expired_at'        => date('Y-m-d', strtotime($date)),
                            'created_at'        => date("Y-m-d H:i:s")
                        );
                
                        $result = Point::add($args);
                        if ($result === false)
                        {
                            $response = ['status' => 'error', 'message' => '加點失敗 !']; 
                        }
                        else
                        {
                            $response = ['status' => 'success', 'message' => '加點成功 !']; 
                        }
            
                    }
                }

                if ($request['action'] == 'reduce')
                {
                    if (empty($description))
                    {
                        $description = '後台扣點';
                    }

                    $args = array(
                        'service_id'            => $service_id,
                        'company_id'            => $company_id,
                        'store_id'              => $store_id,
                        'member_id'             => $member_id,
                        'member_detail_id'      => $member_detail_id,
                        'operating_role'        => Admin::user()->username,
                        'description'           => $description,
                        'point_type_id'         => $point_type_id,
                        'point_deducted_total'  => $point,
                        'exchange_type'         => 'admin_user',
                        'exchange_src_id'       => 0,
                        'created_at'            => date("Y-m-d H:i:s")
                    );

                    $result = Point::reduce($args);
                    if ($result === false)
                    {
                        $response = ['status' => 'error', 'message' => '扣點失敗 !']; 
                    }
                    else
                    {
                        $response = ['status' => 'success', 'message' => '扣點成功 !']; 
                    }
                }
            }
        }

        echo json_encode($response);
        exit();
    }

}
