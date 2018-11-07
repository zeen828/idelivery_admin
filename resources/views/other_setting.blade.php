<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>其他設定</title>
  <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

  <style>
    /* table {
    border-collapse: collapse;
    }

    td, th {
        background: #fff;
    border-width: 0;
        border-bottom: 1px solid #B8B8B8;
        font-weight: normal !important;
    padding: 15px;
        text-align: left;
        vertical-align: middle;
    }

    tr.even {
        td, th {
            background: #f1f1f1;
        }
    }

    thead, tfoot {
    text-transform: uppercase;
        th {
            background: #ccc;
        }
    }

    body {
        color: #111;
        font-size: 16px;
        font-family: sans-serif;
    } */
  </style>
  <script>
    $(document).ready(function() {
      
        // Sortable rows
        $( "table tbody" ).sortable( {
            update: function( event, ui ) {
            $(this).children().each(function(index) {
                    $(this).find('td').last().html(index + 1)
            });
          }
        });

    });
  </script>
</head>
<body>

  {{ csrf_field() }}

	<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <table class='table table-striped table-bordered sorted_table'>
            <thead>
                <tr class="ui-state-default">
                    <th>編號</th>
                    <th>問題</th>
                    <th>操作</th>
                    <th>順序</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($other_list))
                    @foreach ($other_list as $item)
                        <tr class="ui-state-default">
                            <td>{{$item->serial_number}}</td>
                            <td>{{$item->question}}</td>
                            <td>
                                <a href=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href=""><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
  </div>
 
</body>
</html>