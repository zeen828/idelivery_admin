<?php

namespace App\Admin\Controllers;

use App\Model\idelivery\Admin_users;
use App\Model\idelivery\Admin_roles;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Support\Facades\Session;
use DB;

class TestController extends Controller
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

            $content->header('測試用');
            $content->description('description');

            var_dump(Session::all());

            $users = DB::table('users')->get()->toSql();
            dd($users);

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

            $content->header('測試用');
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

            $content->header('測試用');
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
        return Admin::grid(Admin_users::class, function (Grid $grid) {

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
        return Admin::form(Admin_users::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function child()
    {
        $c = Admin_users::find(3)->child;
        //print_r($c);
        foreach ($c as $v) {
            //
            print_r($v);
        }
        $f = Admin_users::find(3)->father;
        //print_r($f);
        foreach ($f as $v) {
            //
            print_r($v);
        }

//        $c = Admin_roles::find(1)->child;
//        //print_r($c);
//        foreach ($c as $v) {
//            //
//            print_r($v);
//        }
//        $f = Admin_roles::find(1)->father;
//        //print_r($f);
//        foreach ($f as $v) {
//            //
//            print_r($v);
//        }

    }

    public function session()
    {
        return Admin::content(function (Content $content) {

            $content->header('測試用');
            $content->description('SESSION測試');

            $content->body('HI');

            session()->regenerate();
            Session::put('key', 'value');

            session(['key' => 'value']);
            Session::push('user.teams', 'developers');
            $value = Session::get('key');

            $value = session('key');
            $value = Session::get('key', 'default');

            $value = Session::get('key', function() { return 'default'; });
            $s = Session::all();
            var_dump($s);

            $u = Session::has('users');
            var_dump($u);
        });
    }
}
