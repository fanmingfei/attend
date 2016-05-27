var _pri = {
    bindUI: function () {
        $('.js-submit-leave').on('click', function () {
            _pri.util.leaveSubmit();
        });
        $('.js-add-teacher').on('click', function () {
            _pri.conf.teacherSelectDom.clone().appendTo('.js-teacher-select-box');
        });
        $(document).on('input',  '.js-search-teacher', function () {
            var $select = $(this).closest('.js-teacher-select-item').find('select');
            _pri.util.searchTeacher($select, $(this).val());
        });
    },
    regExp: {
        date: /^\d{4}-\d{2}-\d{2}$/,
        time: /^\d{2}:\d{2}$/
    },
    conf: {
        start: '',
        end: '',
        teacherSelectDom: null,
        teachers: [],
    },
    util: {

        getTeacherDom: function () {
            _pri.conf.teacherSelectDom = $('.js-teacher-select-item').eq(0).clone();
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

            var description = $('textarea[name="description"]').val();
            if (!description) {
                alert('请填写请假原因');
                return;
            }

            var start = startDate + ' ' + startTime;
            var end = endDate + ' ' + endTime;

            _pri.conf.start = start;
            _pri.conf.end = end;


            var $item = $('.js-teacher-select-box').find('select');
            var arr = [];
            $.each($item, function (i, elem) {
                if ($(elem).val() !== '0') {
                    arr.push($(elem).val());
                }
            });
            if (arr.length == 0) {
                alert('请选择老师！');
                return;
            }
            arr = _pri.util.unique(arr);

            $('input[name="teachers"]').val(JSON.stringify(arr));
            $('input[name="starttime"]').val(_pri.conf.start);
            $('input[name="endtime"]').val(_pri.conf.end);
            $('#leaveForm').submit();
        },
        getTeachers: function () {
            $.each($('.js-teachers-box').find('option'), function (i, item) {
                var $item = $(item);
                _pri.conf.teachers.push({
                    id: $item.val(),
                    username: $(item).text()
                });
            });
        },
        searchTeacher: function ($select, val) {
            var list = _pri.conf.teachers.filter(function (item) {
                if (item.username.indexOf(val) >= 0) {
                    return true;
                }
                return false;
            });
            if(!val) {
                _pri.util.createTeacherItem($select, _pri.conf.teachers);
            } else {
                _pri.util.createTeacherItem($select, list);
            }

        },
        createTeacherItem: function ($select, list) {
            $select.empty();
            var str = '';
            list.forEach(function (item) {
                str += '<option value="'+item.id+'">'+item.username+'</option>';
            });
            $(str).appendTo($select);
        },
        unique: (arr) => {
            var n = [arr[0]];
            for(var i = 1; i < arr.length; i++)
            {
                if (arr.indexOf(arr[i]) == i) n.push(arr[i]);
            }
            return n;
        },
    },
    init: function () {
        this.bindUI();
        _pri.util.getTeachers();
        _pri.util.getTeacherDom();
    }
};

_pri.init();