<?php

namespace App\Lib;

use Illuminate\Support\Facades\DB;

class District
{
    public static function get()
    {
        $rows = DB::table('district_tw')->get();

        $result = array();
        foreach($rows as $row)
        {
            $result[$row->post_code] = $row->name;
        }

        return $result;
    }


    public static function test()
    {
        return DB::select('exec test(?,?,?)',array('Chester',1,date('now')));
    }
}