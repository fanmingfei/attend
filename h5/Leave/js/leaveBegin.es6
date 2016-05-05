var _pri = {
    bindUI: function () {
        $('.js-getTeachers').on('click', function () {
            _pri.util.getTeachers();
        });
        $('.js-time-select').on('input', function (argument) {
            $('.teacherList').hide();
        });
        $('.js-submit-leave').on('click', function () {
            _pri.util.leaveSubmit();
        });
    },
    regExp: {
        date: /^\d{4}-\d{2}-\d{2}$/,
        time: /^\d{2}:\d{2}$/
    },
    conf: {
        start: '',
        end: ''
    },
    util: {
        getTeachers: function () {

            var startDate = $('[name="startDate"]').val();
            var startTime = $('[name="startHour"]').val();
            var endDate = $('[name="endDate"]').val();
            var endTime = $('[name="endHour"]').val();

            var testDate = _pri.regExp.date;
            var testTime = _pri.regExp.time;

            if (!testDate.test(startDate) || !testDate.test(endDate) || !testTime.test(startTime) || !testTime.test(endTime)) {
                alert('时间输入格式错误');
                return;
            }
            var start = startDate + ' ' + startTime;
            var end = endDate + ' ' + endTime;

            _pri.conf.start = start;
            _pri.conf.end = end;

            $.ajax({
                url: '/?c=Leave&a=getLeaveTeachers',
                data: {
                    start: start,
                    end: end
                },
                dataType: 'json',
                success: function (resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        return;
                    }
                    var str = '';
                    resp.data.forEach(function (item) {
                        str += _pri.util.tmpl(item);
                    });
                    $('.lessionList').empty().append($(str));
                    $('.teacherList').show();
                },
                error: function () {
                    alert('服务器错误');
                }
            });
            
        },
        tmpl: function (data) {
            var lession = data.lessionnums.split(',');
            lession.pop();
            lession.shift();
            lession = lession.join(',');
            return  '<div class="weui_cell weui_cell_switch lession-item" data-id="'+data.id+'" data-teacher="'+data.teacher.id+'" data-time="'+data.date+'">' +
                    '<div class="weui_cell_hd weui_cell_primary">' + data.teacher.username+' 《' + data.name + '》 '+data.date+' 星期'+data.day+' 第' + lession + '节</div>' +
                    '<div class="weui_cell_ft">' +
                        '<input class="weui_switch" type="checkbox" checked/>' +
                    '</div>' +
            '</div>';
        },
        leaveSubmit: function () {
            var $item = $('.lessionList').find('input[checked]');
            var arr = [];
            $.each($item, function (i, elem) {
                var $elem = $(elem).closest('.lession-item');
                var item = {};
                item.scheduleid = $elem.attr('data-id');
                item.teacherid = $elem.attr('data-teacher');
                item.scheduletime = $elem.attr('data-time');
                arr.push(item);
            });
            $('input[name="lessions"]').val(JSON.stringify(arr));
            $('input[name="starttime"]').val(_pri.conf.start);
            $('input[name="endtime"]').val(_pri.conf.end);
            $('#leaveForm').submit();
        }
    },
    init: function () {
        this.bindUI();
    }
};

_pri.init();