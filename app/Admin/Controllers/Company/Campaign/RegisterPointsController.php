<?php

namespace App\Admin\Controllers\Company\Campaign;

use App\Models\idelivery\Campaign_setting;
use App\Models\idelivery\Campaign_event;
use App\Models\idelivery\Offer_points;
use App\Models\system_member\Point_type;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class RegisterPointsController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => 1, 'text' => '開啟', 'color' => 'primary'],
        'off' => ['value' => 2, 'text' => '關閉', 'color' => 'default'],
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        Session::forget('event_id');
        $event = Campaign_event::where('keyword', $request->segment(5))->first();
        Session::put('event_id', $event->id);

        return Admin::content(function (Content $content) {
            $content->header(trans('campaign.registed.registed.header'));
            $content->description(trans('campaign.index'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

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

            $content->header(trans('campaign.registed.registed.header'));
            $content->description(trans('campaign.edit'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $campaign_setting = Campaign_setting::find($id);

            if (empty($campaign_setting) || empty($campaign_setting->company_id)
                || $company_id != $campaign_setting->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->row($this->form()->edit($id));

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

            $content->header(trans('campaign.registed.registed.header'));
            $content->description(trans('campaign.create'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $content->row($this->form());

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Campaign_setting::class, function (Grid $grid) {
            $grid->disableRowSelector();
            // $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableFilter();
            // $grid->disableExport();
            // $grid->disableActions();

            $company_id = Session::get('company_id');
            $store_id   = 0;
            $event_id   = Session::get('event_id');

            $grid->model()
                ->where(['company_id' => $company_id, 'store_id' => $store_id])
                ->where(['event_id' => $event_id, 'types' => 1])
                ->where('offer_table', 'Offer_points')
                ->whereNull('condition_table');

            $grid->id(trans('campaign.field.id'))->sortable();
            $grid->title(trans('campaign.field.title'));
            $grid->description(trans('campaign.field.description'));
            $grid->offer_points()->value(trans('campaign.registed.registed.offer_points'));
            $grid->status(trans('campaign.field.status'))->switch($this->states);
            $grid->start_at(trans('campaign.field.start_at'));
            $grid->end_at(trans('campaign.field.end_at'));

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Campaign_setting::class, function (Form $form) {
            $company_id = Session::get('company_id');
            $store_id   = 0;
            $event_id   = Session::get('event_id');

            $form->hidden('company_id')->default($company_id);
            $form->hidden('store_id')->default($store_id);
            $form->hidden('event_id')->default($event_id);
            $form->hidden('condition_table')->default('');
            $form->hidden('offer_table')->default('Offer_points');

            $form->tab(trans('campaign.setting_tab'), function($form) {
                $default_remark = "*營業時間依各店為主\n*全部門市皆可使用\n*本活動不得補差額，亦不得與店內其他行銷活動、優惠合併使用，逾期失效\n*xxx對本活動保留最終變更、解釋及終止之權利";

                $form->text('title', trans('campaign.field.title'))->rules('required');
                $form->text('description', trans('campaign.field.description'));
                $form->textarea('remark', trans('campaign.field.remark'))->default($default_remark)->rules('required');
                $form->switch('status', trans('campaign.field.status'))->states($this->states)->default(1);
            })->tab(trans('campaign.time_tab'), function($form) {
                $datetime = new \DateTime;

                $form->datetimeRange('start_at', 'end_at', trans('campaign.field.duration'))
                    ->default(['start'=>$datetime->format('Y-m-01'), 'end'=>$datetime->modify('+1 months')->format('Y-m-01')]);
            })->tab(trans('campaign.offer_tab'), function($form) use ($company_id) {
                $form->radio('offer_points.point_type_id', "點數類型")->options(Point_type::all()->pluck('name', 'id'))
                    ->default(1)->rules('required');
                $form->number('offer_points.value', "贈送點數")->min(0);
                $form->select('offer_points.expired_at', '有效期限')->options([1 => '半年', 2 => '一年', 3 => '一年半',
                    4 => '二年', 5 => '二年半', 6 => '三年',])->default(2);
            });


        });
    }

    public function destroy($id)
    {
        try
        {
            Offer_points::where("setting_id", $id)->delete();
            Campaign_setting::find($id)->delete();
            return array('status' => true, 'message' => '刪除成功 !');

        } catch (\Exception $e) {
            return array('status' => false, 'message' => '刪除失敗 !');
        }

    }
}
