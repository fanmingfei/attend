
require('../../Public/js/weixin');

var _pri = {
    bindUI: function () {
        $('.js-add-class').on('click', () => {
            _pri.util.addClass();
        });
        $('.js-call-btn').on('click', () => {
            _pri.util.submitCall();
        });
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
                $('.js-call-btn').removeClass('disabled')
                return;
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

            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    _pri.util.postCall(res);
                },
                fail: function () {
                    alert('获取地理位置失败，请重试');
                    $('.js-call-btn').removeClass('disabled');
                    return;
                }
            });

        },
        postCall: function (res) {
            var latitude = res.res.latitude;
            var longitude = res.res.longitude;

            var data = {
                title: title,
                cid: classesid,
                longitude: longitude,
                latitude: latitude
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
        }
    },
    init: function () {
        this.bindUI();
    }
};

_pri.init();