<?php

    namespace App\Admin\Extensions;

    use Encore\Admin\Form\Field;
    
    class Summernote extends Field
    {
        protected $view = 'admin.summernote';

        protected static $css = [
            //'css/summernote.css',
            '//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.css',
        ];

        protected static $js = [
            //'js/summernote.js',
            '//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.js',
            '/js/summernote-zh-TW.js',
            //'/js/jquery_picture_cut/src/jquery.picture.cut.js',
        ];

        public function render()
        {
            $this->script = "$('.summernote').summernote({
                                height: 600,
                                lang: 'zh-TW',
                                airMode: false,
                                focus: true,
                                fontNames: ['新細明體', '標楷體', '微軟正黑體', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New'],
                                codemirror: { 
                                    theme: 'monokai'
                                },
                                maximumImageFileSize: 512000, //500K
                                callbacks: {
                                    onImageUpload: function(image) {
                                        uploadImage(image[0]);
                                    }
                                },
                                toolbar: [
                                    //['style', ['style']],
                                    ['fontname', ['fontname']],
                                    ['fontsize', ['fontsize']],
                                    ['color', ['color']],
                                    ['font', ['bold', 'italic', 'underline', 'clear']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['height', ['height']],
                                    ['table', ['table']],
                                    ['insert', ['link', 'picture', 'table', 'hr']],
                                    ['view', ['fullscreen', 'codeview']],
                                    ['help', ['help']]
                                ]
                            });
                            
                            function uploadImage(image) {
                                var data = new FormData();
                                data.append('file',image);
                                $.ajax ({
                                    data: data,
                                    type: 'POST',
                                    url: '/admin/system/image_upload',
                                    headers: { 'X-CSRF-Token' : $('input[name=csrf_token]').val() },
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function(data) {
                                        var image = data;  
                                        $('.summernote').summernote('insertImage', image);
                                    },
                                    error: function() {
                                        swal({
                                            title: '檔案上傳錯誤',
                                            type: 'warning',
                                            showCancelButton: false,
                                            confirmButtonColor: '#DD6B55',
                                            confirmButtonText: '確認',
                                            closeOnConfirm: true,
                                            cancelButtonText: '取消'
                                            },
                                            function(){
                                            }
                                        );
                                    }
                                });

                            }";

            return parent::render();
        }
    }

?>