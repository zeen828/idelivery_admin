<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;//use Lang;

use App\Models\idelivery\Campaign_setting;

class TestController extends Controller
{
    //
    public function printSession()
    {
        var_dump(Session::all());
    }

    public function modelORM()
    {
        $old_member = OldMember::find(2)->member;
        var_dump($old_member);

        $old_member = Member::find(2);
    }

    public function mylang()
    {
        echo Lang::get('idelivery.company');
    }

    public function test_table()
    {
        $a = Campaign_setting::find(1);
        print_r($a);
    }
}
