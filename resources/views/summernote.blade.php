<!-- <!DOCTYPE html>
<html> -->
<head>
	<!-- <title>品牌設定</title> -->
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>

    {{ Html::script('/js/summernote-zh-TW.js') }}


</head>

<!-- <body> -->


	<div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <textarea class="form-control summernote" name="detail">{{ (isset($detail) ? $detail : "") }}</textarea>
        </div>
    </div>


    <script>

        $(document).ready(function() {
            
            $('.summernote').summernote({
                height: 300,
                lang: "zh-TW"
            });

            $(".btn-info").click(function(){
            //$(document).on('click', '.btn-info :submit',function(){
                var text_content = $('.summernote').val();
                
                $.ajax({
                    url: "/admin/setting/aboutus",
                    type:'POST',
                    dataType: 'json',
                    data: {store_id: 1, type: 1, content: text_content},
                    success: function(data) {
                        if($.isEmptyObject(data.error)){
                            alert(data.success);
                        }
                        else
                        {
                            alert(data.error);
                        }
                    }
                });

            }); 

        });

    </script>

