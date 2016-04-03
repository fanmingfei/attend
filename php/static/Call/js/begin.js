define('Call/js/begin.es6', function(require, exports, module) {

'use strict';

require('Public/js/weixin.es6');

var _pri = {
    bindUI: function bindUI() {
        $('.js-add-class').on('click', function () {
            _pri.util.addClass();
        });
        $('.js-call-btn').on('click', function () {
            _pri.util.submitCall();
        });
    },
    util: {
        addClass: function addClass() {
            var $classBox = $('.js-class-item').eq(0).clone();
            $classBox.appendTo('.js-classes-box');
        },
        submitCall: function submitCall() {
            if ($('.js-call-btn').hasClass('disabled')) {
                return;
            }

            $('.js-call-btn').addClass('disabled');
            var title = $('input[name="title"]').val();
            if (!title) {
                alert('请填写标题');
                $('.js-call-btn').removeClass('disabled');
                return;
            }
            var $classesDom = $('select[name="classid"]');
            var arr = [];
            $.each($classesDom, function (i, elem) {
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

            var promise = new Promise(function (resolve, reject) {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function success(res) {
                        resolve(res);
                    },
                    error: function error() {
                        alert('获取地理位置失败，请重试');
                        $('.js-call-btn').removeClass('disabled');
                        return;
                    }
                });
            });

            promise.then(function (res) {
                console.log(res.res);
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
                    success: function success(resp) {
                        if (resp.status !== 0) {
                            alert(resp.msg);
                            return;
                        }
                        location.href = "/?c=Call&a=postCallSuccess&id=" + resp.data;
                    },
                    error: function error() {
                        alert('服务器错误，重试');
                    },
                    complete: function complete() {
                        $('.js-call-btn').removeClass('disabled');
                    }
                });
            });
        },
        unique: function unique(arr) {
            var n = [arr[0]];
            for (var i = 1; i < arr.length; i++) {
                if (arr.indexOf(arr[i]) == i) n.push(arr[i]);
            }
            return n;
        }
    },
    init: function init() {
        this.bindUI();
    }
};

_pri.init();

});
