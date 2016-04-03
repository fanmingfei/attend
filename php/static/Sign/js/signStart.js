define('Sign/js/signStart.es6', function(require, exports, module) {

'use strict';

require('Public/js/weixin.es6');
var _pri = {
    bindUI: function bindUI() {
        var _this = this;

        $('.js-sign-btn').on('click', function () {
            _this.util.signClick();
        });
    },
    util: {
        signClick: function signClick() {

            if ($('.js-sign-btn').hasClass('disabled')) {
                return;
            }

            $('.js-sign-btn').addClass('disabled');

            var promise = new Promise(function (resolve, reject) {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function success(res) {
                        resolve(res);
                    },
                    fail: function fail() {
                        alert('获取地理位置失败，请重试');
                        $('.js-sign-btn').removeClass('disabled');
                        return;
                    }
                });
            });

            promise.then(function (res) {
                var latitude = res.res.latitude;
                var longitude = res.res.longitude;

                var data = {
                    longitude: longitude,
                    latitude: latitude
                };

                $.ajax({
                    url: '/?c=Sign&a=postSign',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function success(resp) {
                        if (resp.status !== 0) {
                            alert(resp.msg);
                            return;
                        }
                        location.href = "/?c=Sign&a=postSignSuccess";
                    },
                    error: function error() {
                        alert('服务器错误，重试');
                    },
                    complete: function complete() {
                        $('.js-sign-btn').removeClass('disabled');
                    }
                });
            });
        }
    },
    init: function init() {
        this.bindUI();
    }
};
_pri.init();

});
