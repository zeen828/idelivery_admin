<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class CouponFormat extends Field
{
    protected $view = 'admin.coupon';

    protected static $css = [
    ];

    protected static $js = [
    ];

    public function render()
    {
        $this->script = "
        ";

        return parent::render();
    }
}

?>