var _pri = {
    bindUI: function () {
        $('.js-search-teacher').on('keyup', function () {
            _pri.util.searchTeacher($(this).val());
        });
        $('.js-week-full').on('click', function () {
            $('.js-week-btn').removeClass('btn-default').addClass('btn-success');
        });
        $('.js-week-odd').on('click', function () {
            $('.js-week-btn').removeClass('btn-success').addClass('btn-default');
            $('.js-week-btn:even').removeClass('btn-default').addClass('btn-success');
        });
        $('.js-week-even').on('click', function () {
            $('.js-week-btn').removeClass('btn-success').addClass('btn-default');
            $('.js-week-btn:odd').removeClass('btn-default').addClass('btn-success');
        });
        $('.js-submit-course').on('click', function () {
            _pri.util.submitCourse();
        });
        $('.js-lessionnum, .js-week').on('click', '.btn-default', function () {
            $(this).removeClass('btn-default').addClass('btn-success');
        });
        $('.js-lessionnum, .js-week').on('click', '.btn-success', function () {
            $(this).removeClass('btn-success').addClass('btn-default');
        });
    },
    conf: {
        teachers: []
    },
    util: {
        getTeachers: function () {
            $.each($('.js-teachers-box').find('option'), function (i, item) {
                var $item = $(item);
                _pri.conf.teachers.push({
                    id: $item.val(),
                    username: $(item).text()
                });
            });
        },
        searchTeacher: function (val) {
            var list = _pri.conf.teachers.filter(function (item) {
                if (item.username.indexOf(val) >= 0) {
                    return true;
                }
                return false;
            });
            if(!val) {
                _pri.util.createTeacherItem(_pri.conf.teachers);
            } else {
                _pri.util.createTeacherItem(list);
            }

        },
        createTeacherItem: function (list) {
            $('.js-teachers-box').empty();
            var str = '';
            list.forEach(function (item) {
                str += '<option value="'+item.id+'">'+item.username+'</option>';
            });
            $(str).appendTo('.js-teachers-box');
        },
        submitCourse: function () {
            if ($('.js-submit-course').hasClass('disabled')) {
                return;
            }
            $('.js-submit-course').addClass('disabled');
            var name = $('input[name="name"]').val();
            var day = $('select[name="day"]').val();
            var teacherid = $('select[name="teacherid"]').val();
            var classid = $('select[name="classid"]').val();
            var id = $('input[name="id"]').val();

            var lessionnum = $('.js-lessionnum').find('.btn-success');
            var lessions = [];
            $.each(lessionnum, function (i, item) {
                lessions.push($(item).text());
            });

            var week = $('.js-week').find('.btn-success');
            var weeks = [];
            $.each(week, function (i, item) {
                weeks.push($(item).text());
            });


            if (!name || !day || !teacherid || !classid || !lessions || !weeks) {
                alert('信息填写不完整!');
                $('.js-submit-course').removeClass('disabled');
                return;
            }
            if (teacherid.length > 1) {
                $('.js-submit-course').removeClass('disabled');
                alert('老师只能选择一个');
                return;
            }


            var data = {
                id: id,
                name: name,
                day: day,
                teacherid: teacherid[0],
                classid: classid,
                lessionnums: lessions.join(','),
                weeks: weeks.join(',')
            };

            $.ajax({
                url: '/?c=Schedule&a=postSchedule',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        $('.js-submit-course').removeClass('disabled');
                        return;
                    }
                    alert('成功！');
                    location.reload();
                },
                error: function () {
                    $('.js-submit-course').removeClass('disabled');
                    alert('服务器错误');
                }
            });
        },
        renderSelect: function () {
            var lessionBox = $('.js-lessionnum');
            var weekBox = $('.js-week');

            var lessions = lessionBox.attr('data-ids').split(',');
            var weeks = weekBox.attr('data-ids').split(',');

            lessions.forEach(function (n) {
                if (n) {
                    lessionBox.find('[type="button"]').eq(n - 1).removeClass('btn-default').addClass('btn-success');
                }
            });

            weeks.forEach(function (n) {
                if (n) {
                    weekBox.find('[type="button"]').eq(n - 1).removeClass('btn-default').addClass('btn-success');
                }
            });


        }
    },
    init: function () {
        this.util.getTeachers();
        this.util.renderSelect();
        this.bindUI();
    }

};
_pri.init();
