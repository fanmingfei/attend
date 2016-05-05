require('../../Public/js/weixin');
var _pri = {
    bindUI: function () {
        $('.js-sign-btn').on('click', () => {
            this.util.signClick();
        });
    },
    util: {
        signClick: () => {

            if ($('.js-sign-btn').hasClass('disabled')){
                return;
            }

            $('.js-sign-btn').addClass('disabled');

            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    if (!res.res) {
                        res.res = res;
                    }
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
                        success: function (resp) {
                            if (resp.status !== 0) {
                                alert(resp.msg);
                                $('.js-sign-btn').removeClass('disabled');
                                return;
                            }
                            location.href = "/?c=Sign&a=postSignSuccess";
                        },
                        error: function () {
                            $('.js-sign-btn').removeClass('disabled');
                            alert('服务器错误，重试');
                        },
                    });

                },
                fail: function () {
                    alert('获取地理位置失败，请重试')
                    $('.js-sign-btn').removeClass('disabled');
                    return;
                }
            });


        }
    },
    init: function () {
        this.bindUI();
    }
}
_pri.init();