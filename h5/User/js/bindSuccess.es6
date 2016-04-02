require('../../Public/js/weixin');
var _pri = {
    bindUI: function () {
        $('.js-close').on('click', function () {
            wx.closeWindow();
        });
    },
    init: function () {
        this.bindUI();
    }
};
_pri.init();