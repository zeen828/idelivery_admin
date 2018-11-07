$(document).ready(function() {

    var title = $('#title').val() + ' ' + $('#description').val();
    $('#title_lbl').text(title);
    show_time();

    $('#offer_lbl').text('贈送 ' + Math.floor($('#offer_value').val()) + '件');
    $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '件');

    $('#title').on('input',function(e){
        title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
    });

    $('#description').on('input',function(e){
        title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
    });

    $('#offer_value').focus(function(e){
        $('#offer_value').select();
    });

    $('#offer_value').on('input',function(e){
        var offer_val = $('#offer_value').val();
        if (offer_val)
        {
            $('#offer_lbl').text('打折 ' + Math.floor($('#offer_value').val()) + '％');
        }
    });

    $('#cond_value').focus(function(e){
        var cond_val = $('#cond_value').val();
        if (cond_val)
        {
            $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
        }
    });

    $('#cond_value').on('input',function(e){
        $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '件');
    });

    $('.start_at').on('dp.change', function (e) {
        show_time();
    });

    $('.end_at').on('dp.change', function (e) {
        show_time();
    });

    $('#max_qty').focus(function(e){
        $('#max_qty').select();
    });

    $('#user_use_count').focus(function(e){
        $('#user_use_count').select();
    });

    function show_time()
    {
        var start_at = $('.start_at').val();
        var end_at = $('.end_at').val();


        if (!start_at && !end_at)
        {
            $('#duration_lbl').text('不限');
        }
        else
        {
            if (!start_at)
            {
                start_at = '';
            }
            else {
                start_at = formatDate(start_at);
            }

            if (!end_at)
            {
                end_at = '';
            }
            else {
                end_at = formatDate(end_at);
            }

            $('#duration_lbl').text(start_at + ' ～ ' + end_at);
        }
    }


    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    function formatDateTime(date) {
        now = new Date(date);
        year = "" + now.getFullYear();
        month = "" + (now.getMonth() + 1); if (month.length == 1) { month = "0" + month; }
        day = "" + now.getDate(); if (day.length == 1) { day = "0" + day; }
        hour = "" + now.getHours(); if (hour.length == 1) { hour = "0" + hour; }
        minute = "" + now.getMinutes(); if (minute.length == 1) { minute = "0" + minute; }
        second = "" + now.getSeconds(); if (second.length == 1) { second = "0" + second; }
        return year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
    }

    function show_offer()
    {
        $('#offer_lbl').text('贈送 ' + Math.floor($('#offer_value').val()) + '件');
    }
});
