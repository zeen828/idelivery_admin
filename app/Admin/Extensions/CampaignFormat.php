<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class CampaignFormat extends Field
{
    protected $view = 'admin.campaign';

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