<?php

namespace App\Admin\Controllers\Store;

use App\Model\idelivery\News;
use App\Models\idelivery\Publish_version_log;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Encore\Admin\Widgets\Box;

class PoshowController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '上架', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '下架', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $content->header('POS子母畫面');
            $content->description(trans('idelivery.admin.index'));
            if(empty($company_id) || empty($store_id)) {
                $box = new Box('提示', '請選擇所屬店家!!');
                $content->row($box->removable()->style('warning'));
            } else {
                $publish_version_log = Publish_version_log::where(['store_id'=>$store_id, 'types'=>3])->orderBy('version', 'desc')->first();
                if (empty($publish_version_log)) {
                    $version = '沒有發佈的版本號!';
                    $url     = '#';
                } else {
                    $version = $publish_version_log->version;
                    $url     = $publish_version_log->file_url;
                }

                $box = $box = new Box('所有moneyPOS的子母畫面均以發佈為準', sprintf("目前發佈的版本號: <span id='current_version'>%s</span>\n<a id='preview' href='%s' target='_blank'>預覽</a>", $version, $url));
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
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $news = News::find($id);
            if (empty($news) || empty($news->company_id) || $company_id != $news->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('POS子母畫面');
            $content->description(trans('idelivery.admin.edit'));
            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form()->edit($id));
            }
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
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $content->header('POS子母畫面');
            $content->description(trans('idelivery.admin.create'));
            if(empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form());
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
            $("#btn_publish").on("click", function () {
                $.ajax({
                    url: "/admin/store/set/publish/poshow",
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

        return Admin::grid(News::class, function (Grid $grid) {

            $grid->model()->where('company_id', '=', Session::get('company_id'))
                            ->where('store_id', '=', Session::get('store_id'))
                            ->where('type', '=', 4);

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作
            $grid->tools(function ($tools) {
                $html = '<input type="hidden" id="csrf_token" name="csrf_token" value="' . csrf_token() . '" />';
                $html .= '<div class="form-inline pull-left"><a class="btn btn-sm btn-warning" id="btn_publish"><i class="fa fa-archive" aria-hidden="true"></i>&nbsp;發佈</a></div>';

                $tools->append($html);
            });

            //$grid->id('ID')->sortable();

            $grid->title('標題');
            $grid->column('image', '圖檔 (建議 400*300px)')->display(function ($image) {
                if (isset($image)) {
                    return "<img src='" . env('ADMIN_UPLOAD_URL', '') . $image . "' width='50'>";
                } else {
                    return "";
                }
            });

            $grid->column('link', '活動連結')->display(function ($link) {
                return "<a href='{$link}'>" . $link . "</a>";
            });

            $grid->start_date('起始日期');
            $grid->end_date('結束日期');
            $grid->status('狀態')->switch($this->status_arr);
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(News::class, function (Form $form) {
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $form->hidden('company_id','公司編號')->default($company_id);
            $form->hidden('store_id','店家編號')->default($store_id);
            // type=4 pos機子母畫面
            $form->hidden('type','類別')->default(4);

            $now = (new \DateTime())->format('Y-m-d H:i:s');
            $start = (new \DateTime())->format('Y-m-d 00:00:00');
            $end = (new \DateTime())->format('Y-m-d 23:59:59');

            $form->hidden('post_date', '發布日期時間')->default($now);
            $form->datetime('start_date', '起始日期時間')->default($start);
            $form->datetime('end_date', '結束日期時間')->default($end);

            $form->text('title', '標題')->rules('required|max:30')->placeholder('限30字');
            $form->image('image', '上傳圖片')->uniqueName()->rules('max:900')
                ->move(env('ADMIN_UPLOAD_PATH', '') . 'store/image/poshow/'.$store_id);
            $form->url('link', '連結網址')->rules('nullable');
            $form->switch('status', '狀態')->states($this->status_arr)->default(2);
        });
    }

    public function publish()
    {
        $store_id = Session::get('store_id');

        if (empty($store_id)) {
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        $news = News::where('company_id', Session::get('company_id'))
                        ->where('store_id', $store_id)
                        ->where(['type'=>4, 'status'=>1])
                        // ->where('post_date', '>=', date('Y-m-d H:i:s'))
                        ->get();

        $news_list = array();
        if ($news->isNotEmpty()) {
            foreach ($news as $value) {
                $obj = new \stdClass();
    
                $obj->title   = $value->title;
                $obj->picture = empty($value->image) ? '' : env('ADMIN_UPLOAD_URL').$value->image;
                $obj->action  = 1;
                $obj->start_date = $value->start_date;
                $obj->end_date   = $value->end_date;
                $obj->post_date  = $value->post_date;
    
                $obj->action_type = new \stdClass();
                $obj->action_type->url = $value->link;
                $obj->action_type->page_type = '';
                $obj->action_type->page_parameter = '';
    
                $news_list[] = $obj;
            }
        }

        $publish_version = new Publish_version_log;
        $publish_version->store_id = $store_id;
        $publish_version->types    = 3;
        $publish_version->save();

        $publish_version_log_id = $publish_version->id;

        unset($publish_version);

        $version = date('YmdHis').'_'.$publish_version_log_id;
        $data = ['version' => $version, 'data' => $news_list];

        if ( ! is_dir(sprintf("tmp/version/poshow/%s", $store_id))) {
            mkdir(sprintf("tmp/version/poshow/%s", $store_id));
        }

        $file_tmp_path = sprintf("tmp/version/poshow/%s/%s.json", $store_id, $version);
        $result = file_put_contents($file_tmp_path, json_encode($data));
        if (empty($result)) {
            Publish_version_log::destroy($publish_version_log_id);
            return response(json_encode(['status' => 'fail', 'message' => '發佈失敗']), 400)->header('Content-Type', 'application/json');
        }

        unset($result);

        $file_s3_path = sprintf("version/poshow/%s/%s.json", $store_id, $version);
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