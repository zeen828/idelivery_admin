<?php

    namespace App\Admin\Extensions;

    use Encore\Admin\Form\Field;

    class Accordion extends Field
    {
        protected $view = 'admin.accordion';

        protected static $css = [
            '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        ];

        protected static $js = [
            '//code.jquery.com/ui/1.12.1/jquery-ui.js',
        ];

        public function render()
        {
            $this->script = "$(document).ready(function() {
                                var active = false,
                                sorting = false;
            
                                $('#accordion')
                                .accordion({
                                    header: '> div > h3',
                                    collapsible: true,
                                    activate: function( event, ui){
                                        if(sorting)
                                            $(this).sortable('refresh');   
                                    }
                                })
                                .sortable({
                                    handle: 'h3',
                                    placeholder: 'ui-state-highlight',
                                    start: function( event, ui ){
                                        sorting=true;                                       
                                        active = $(this).accordion('option', 'active'); 
                                        
                                        $(this).accordion('option', 'animate', { easing: 'swing', duration: 0 } );
                                        
                                        $(this).accordion({ active:false });
                                    },
                                    stop: function( event, ui ) {
                                        ui.item.children('h3').triggerHandler('focusout');
                                        
                                        $(this).accordion('option', 'animate', { } );
                                        
                                        $(this).accordion('option', 'active', active);
                                        
                                        sorting=false;
                                    }
                                });
                        
                            });";

            return parent::render();
        }
    }

?>