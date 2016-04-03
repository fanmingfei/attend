define('Public/js/weixin.es6', function(require, exports, module) {

'use strict';

var nonceStr = 'stringforys';
var timestamp = parseInt(new Date().getTime() / 1000);
var appId = 'wx150e95df0d2ffdef';
var url = encodeURIComponent(location.href.split('#')[0]);

$.ajax({
    url: '/?c=Wechat&a=getSign',
    type: 'get',
    data: {
        noncestr: nonceStr,
        timestamp: timestamp,
        url: url
    },
    dataType: 'json',
    success: function success(resp) {
        if (resp.status !== 0) {
            alert('获取微信配置出错请重新进入!');
            return;
        }
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: appId, // 必填，公众号的唯一标识
            timestamp: timestamp, // 必填，生成签名的时间戳
            nonceStr: nonceStr, // 必填，生成签名的随机串
            signature: resp.data.signature, // 必填，签名，见附录1
            jsApiList: ['getLocation'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
    }
});
// export {init};

/**
 * 使用方法
 */

// require('../../Public/js/weixin');

// wx.getLocation({
//     type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
//     success: function (res) {
//         var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
//         var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
//         var speed = res.speed; // 速度，以米/每秒计
//         var accuracy = res.accuracy; // 位置精度
//         console.log(res)
//     }
// });

});
