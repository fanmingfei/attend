
// require('../../Public/js/weixin');

var _pri = {
    bindUI: function () {
        $('.js-add-class').on('click', () => {
            _pri.util.addClass();
        });
        $('.js-call-btn').on('click', () => {
            _pri.util.submitCall();
        });
        $('.js-search-teacher').on('keyup', function () {
            _pri.util.searchTeacher($(this).val());
        });
    },
    conf: {
        teachers: []
    },
    util: {
        addClass: () => {
            var $classBox = $('.js-class-item').eq(0).clone();
            $classBox.appendTo('.js-classes-box');
        },
        submitCall: () => {
            if ($('.js-call-btn').hasClass('disabled')){
                return;
            }

            $('.js-call-btn').addClass('disabled');
            var title = $('input[name="title"]').val();
            if (!title) {
                alert('请填写标题');
                $('.js-call-btn').removeClass('disabled');
                return;
            }
            var tcid = $('select[name="tcid"]').val();
            if (tcid == 0) {
                alert('请选择班主任');
                $('.js-call-btn').removeClass('disabled');
                return;
            }

            var tid = $('select[name="tid"]').val();
            if (tid == 0 || !tid) {
                var c = confirm('确定不选择任课老师吗？');
                if (!c) {
                    $('.js-call-btn').removeClass('disabled');
                    return;
                }
            }


            var $classesDom = $('select[name="classid"]');
            var arr = [];
            $.each($classesDom, (i, elem) => {
                var val = elem.value;
                arr.push(val);
            });
            var newArr = _pri.util.unique(arr);
            newArr.sort();

            var classesid = newArr.join(',');

            if (classesid == '0') {
                alert('请选择班级');
                $('.js-call-btn').removeClass('disabled');
                return;
            }

            // wx.getLocation({
            //     type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            //     success: function (res) {
            //         var latitude = res.latitude;
            //         var longitude = res.longitude;
            //         if (res.res) {
            //             latitude = res.res.latitude;
            //             longitude = res.res.longitude;
            //         }

            //     },
            //     fail: function () {
            //         alert('获取地理位置失败，请重试');
            //         $('.js-call-btn').removeClass('disabled');
            //         return;
            //     }
    // });
            var data = {
                title: title,
                cid: classesid,
                tcid: tcid,
                longitude: 0,
                latitude: 0,
                tid: tid
            };

            $.ajax({
                url: '/?c=Call&a=postCall',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (resp) {
                    if (resp.status == -2) {
                        alert(resp.data.join('，')+'，已在点名列表中，请勿选择');
                        return;
                    }
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        return;
                    }
                    location.href = "/?c=Call&a=postCallSuccess&id=" + resp.data;
                },
                error: function () {
                    alert('服务器错误，重试');
                },
                complete: function () {
                    $('.js-call-btn').removeClass('disabled');
                }
            });

        },
        unique: (arr) => {
            var n = [arr[0]];
            for(var i = 1; i < arr.length; i++)
            {
                if (arr.indexOf(arr[i]) == i) n.push(arr[i]);
            }
            return n;
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
    },
    init: function () {
        _pri.util.getTeachers();
        this.bindUI();
    }
};

_pri.init();