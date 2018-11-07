<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Model\system_member\SmsLog;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmsLogController extends Controller
{
    use ModelForm;
    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '正常', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商店管理 > 簡訊服務紀錄');
            $content->description(trans('idelivery.admin.index'));

            //幫助
            $box1 = new Box(trans('idelivery.box.help.title'), '內容');
            $content->row($box1->collapsable());

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
        abort(404);
        exit();

        return Admin::content(function (Content $content) use ($id) {

            $content->header('商店管理 > 簡訊服務紀錄');
            $content->description(trans('idelivery.admin.edit'));

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

            $content->header('商店管理 > 簡訊服務紀錄');
            $content->description(trans('idelivery.admin.create'));

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
            $(".search").click(function(){
                var sms_id = $(this).data("id");
                var kmsgid = $(this).data("kmsgid");
                $.ajax({
                        url: "/admin/management/sms/status",
                        type: "GET",
                        dataType : "json",
                        cache: false,
                        data: {id: sms_id, kmsgid: kmsgid},
                        headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                        success: function(data) {
                            toastr.success(data.message);
                            window.location = "/admin/management/sms_log";
                        },
                        error: function() {
                            alert("簡訊發送狀態查詢作業失敗 !");
                        }
                });
            });
        
            $(".send").click(function(){
                var sms_id = $(this).data("id");
                swal({
                    title: "確認重新發送?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "確認",
                    closeOnConfirm: false,
                    cancelButtonText: "取消"
                },
                function(){
                    $.ajax({
                        url: "/admin/management/sms/send",
                        type: "GET",
                        dataType : "json",
                        cache: false,
                        data: {id: sms_id},
                        headers: { "X-CSRF-Token" : $("#csrf_token").val() },
                        success: function(data) {
                            toastr.success(data.message);
                            window.location = "/admin/management/sms_log";
                        },
                        error: function() {
                            alert("簡訊重發送作業失敗 !");
                        }
                    });
                });
            });
        ');

        return Admin::grid(SmsLog::class, function (Grid $grid) {
            // 禁止功能
            $grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            //$grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            $grid->disableActions();//操作

            $grid->model()->getList();
            $grid->id('ID')->sortable();

            // $grid->column('status', '發送狀態')->display(function () {
            //     $status = '';
            //     switch ($this->status)
            //     {
            //         case 0:
            //             $status .= '<span class="label label-primary">未發送</span>';
            //             break;
            //         case 1:
            //             $status .= '<span class="label label-success">成功</span>';
            //             break;
            //         default:
            //             $status .= '<span class="label label-danger">失敗</span>';
            //             break;
            //     }
            //     return $status;
            // });

            //$grid->column('priority', '優先順序');
            $grid->column('dstaddr', '接收門號');
            $grid->column('smbody', '簡訊內文');
            $grid->column('kmsgid', '簡訊發送編號');
            $grid->column('dlvtime', '發出時間');
            //$grid->column('donetime', '回報狀態時間');
            $grid->column('statusstr', '簡訊王發送狀態')->display(function () {
                $statusstr = '';
                switch ($this->statusstr)
                {
                    case 'DELIVERED':
                    case 'DELIVRD':
                    case 'SUCCESSED':
                        $statusstr .= '<span class="label label-success">'.$this->statusstr.'</span>';
                        break;
                    default:
                        $statusstr .= '<span class="label label-danger">'.$this->statusstr.'</span>';
                        break;
                }
                return $statusstr;
            });
            $grid->column('action', '操作')->display(function () {
                return "<a href='' class='search' data-id=".$this->id." data-kmsgid=".$this->kmsgid.">
                        <i class='fa fa-search-plus' aria-hidden='true'></i></a>&nbsp;&nbsp;
                        <a href='' class='send' data-id=".$this->id."><i class='fa fa-paper-plane' aria-hidden='true'></i></a>";
            });

            $grid->filter(function($filter){

                // Remove the default id filter
                //$filter->disableIdFilter();
            
                // Add a column filter
                $filter->like('dstaddr', '接收門號');
            
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
        return Admin::form(SmsLog::class, function (Form $form) {
        });
    }


    //簡訊發送狀態查詢
    public function status(Request $request)
    {
        if (!empty($request))
        {
            if (!empty($request['id']) && !empty($request['kmsgid']))
            {
                $api_method = 'GET';
                $api_url = "https://api.kotsms.com.tw/msgstatus.php";
        
                $curl = curl_init();
                $query_arr = [
                    'username'=> env('SMS_ACCOUNT'),
                    'password'=> env('SMS_PASSWORD'),
                    'kmsgid'=> $request['kmsgid']
                ];
    
                $api_url .= '?'.http_build_query($query_arr);
    
                curl_setopt($curl, CURLOPT_URL, $api_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
        
                $result = curl_exec($curl);

                if (!empty($result))
                {
                    $statusstr_arr = explode('=', $result);

                    if (count($statusstr_arr) == 2)
                    {
                        $statusstr = $statusstr_arr[1];

                        \DB::connection('system_member')->table('sms_log')
                            ->where('id', $request['id'])
                            ->update(['statusstr' => $statusstr, 'updated_at' => Carbon::now()]);
                    }
                }

                curl_close($curl);

                $response = ['status' => 'success', 'message' => '簡訊發送狀態查詢成功 !']; 
            }
            else
            {
                $response = ['status' => 'error', 'message' => '簡訊發送狀態查詢失敗 !']; 
            }

            echo json_encode($response);
            exit();
        }
    }


    //簡訊重新發送
    public function send(Request $request)
    {
        if (!empty($request))
        {
            if (!empty($request['id']))
            {
                $sms = SmsLog::getView($request['id']);
                if (empty($sms))
                {
                    $response = ['status' => 'error', 'message' => '資料錯誤, 簡訊重發送作業失敗 !'];
                    echo json_encode($response);
                    exit(); 
                }

                $args = array(
                    'service_id'    => $sms->service_id,
                    'company_id'    => $sms->company_id,
                    'store_id'      => $sms->store_id,
                    'priority'      => $sms->priority,
                    'dstaddr'       => $sms->dstaddr,
                    'smbody'        => $sms->smbody,
                    'created_at'    => Carbon::now()
                );

                $result = SmsLog::add($args);

                if ($result === false)
                {
                    $response = ['status' => 'error', 'message' => '簡訊重發送作業失敗 !']; 
                }
                else
                {
                    $response = ['status' => 'success', 'message' => '簡訊重發送作業已納入發送排程 !']; 
                }

                // $api_method = 'GET';
                // $api_url = "https://api.kotsms.com.tw/kotsmsapi-1.php";
        
                // $curl = curl_init();
        
                // $query_arr = [
                //     'dstaddr'   => $val->dstaddr,
                //     'smbody'    => mb_convert_encoding($val->smbody, "BIG5", "UTF-8"),
                //     'response'  => env('SYSTEM_MEMBER_API_URL')."/v1/system/sms/response",
                // ];
    
                // $curl_str = http_build_query($query_arr);

                // $query_arr['username']  = env('SMS_ACCOUNT');
                // $query_arr['password']  = env('SMS_PASSWORD');
                // $api_url .= '?'.http_build_query($query_arr);

                // curl_setopt($curl, CURLOPT_URL, $api_url);
                // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($curl, CURLOPT_HEADER, false);
        
                // $result = curl_exec($curl);
    
                // if (!empty($result))
                // {
                //     $kmsgid_arr = explode('=', $result);
    
                //     if (count($kmsgid_arr) == 2)
                //     {
                //         $kmsgid = $kmsgid_arr[1];
    
                //         \DB::connection('system_member')->table('sms_log')
                //             ->where('id', $request['id'])
                //             ->update(['status' => 1, 'kmsgid' => $kmsgid, 'curl_str' => $curl_str, 
                //                 'updated_at' => Carbon::now()]);
                //     }
                // }
        
                // curl_close($curl);
                // $response = ['status' => 'success', 'message' => '簡訊重發送作業已納入發送排程 !']; 
            }
            else
            {
                $response = ['status' => 'error', 'message' => '資料錯誤, 簡訊重發送作業失敗 !']; 
            }

            echo json_encode($response);
            exit();
        }
    }

}
