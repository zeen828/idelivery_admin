function discount_for_amount(cond_enable)
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text('打折 ' + Math.floor($('#offer_value').val()) + '％');
        if (cond_enable !== false) {
            $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
        }

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
            $('#cond_value').select();
        });

        $('#cond_value').on('input',function(e){
            var cond_val = $('#cond_value').val();
            if (cond_val)
            {
                $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
            }
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
        
        $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}

function discount_for_qty()
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text('打折 ' + Math.floor($('#offer_value').val()) + '％');
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
            $('#offer_lbl').text('打折 ' + Math.floor($('#offer_value').val()) + '％');
        });

        $('#cond_value').focus(function(e){
            $('#cond_value').select();
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

        $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}

function qty()
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text(Math.floor($('#offer_value').val()) + '件免費');
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
            $('#offer_lbl').text(Math.floor($('#offer_value').val()) + '件免費');
        });

        $('#cond_value').focus(function(e){
            $('#cond_value').select();
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

        $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}

function qty_for_amount()
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text('折抵 ' + Math.floor($('#offer_value').val()) + '元');
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
            $('#offer_lbl').text('折抵 ' + Math.floor($('#offer_value').val()) + '元');
        });

        $('#cond_value').focus(function(e){
            $('#cond_value').select();
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
        
                    $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}

function amount(cond_enable)
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text('折抵 ' + Math.floor($('#offer_value').val()) + '元');
        if (cond_enable !== false) {
            $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
        }

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
                $('#offer_lbl').text('折抵 ' + Math.floor($('#offer_value').val()) + '元');
            }
        });

        $('#cond_value').focus(function(e){
            $('#cond_value').select();
        });

        $('#cond_value').on('input',function(e){
            var cond_val = $('#cond_value').val();
            if (cond_val)
            {
                $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
            }
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

        $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}

function amount_for_qty(cond_enable)
{
    $(function(){
        if ($('input[type=radio][name=kind]').val() == 1)
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').show();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').hide();
        }
        else
        {
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(0)').hide();
            $('input[type=radio][name=kind]').closest('.form-group').nextAll('.form-group:eq(1)').show();
        }

        var title = $('#title').val() + ' ' + $('#description').val();
        $('#title_lbl').text(title);
        show_time();

        $('#offer_lbl').text(Math.floor($('#offer_value').val()) + '件免費');
        if (cond_enable !== false) {
            $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
        }

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
                $('#offer_lbl').text(Math.floor($('#offer_value').val()) + '件免費');
            }
        });

        $('#cond_value').focus(function(e){
            $('#cond_value').select();
        });

        $('#cond_value').on('input',function(e){
            var cond_val = $('#cond_value').val();
            if (cond_val)
            {
                $('#cond_lbl').text('使用條件　滿 ' + Math.floor($('#cond_value').val()) + '元');
            }
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

        $('input[type=radio][name=kind]').on('ifChecked', function(){
            if ($(this).val() == 1)
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').show();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').hide();
            }
            else
            {
                $(this).closest('.form-group').nextAll('.form-group:eq(0)').hide();
                $(this).closest('.form-group').nextAll('.form-group:eq(1)').show();
            }
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
            year = "\\" + now.getFullYear();
            month = "\\" + (now.getMonth() + 1); if (month.length == 1) { month = "0\\" + month; }
            day = "\\" + now.getDate(); if (day.length == 1) { day = "0\\" + day; }
            hour = "\\" + now.getHours(); if (hour.length == 1) { hour = "0\\" + hour; }
            minute = "\\" + now.getMinutes(); if (minute.length == 1) { minute = "0\\" + minute; }
            second = "\\" + now.getSeconds(); if (second.length == 1) { second = "0\\" + second; }
            return year + "-\\" + month + "-\\" + day + " \\" + hour + ":\\" + minute + ":\\" + second;
        }
    });
}
