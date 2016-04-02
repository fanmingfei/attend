define('User/js/bindSuccess.es6', function(require, exports, module) {

'use strict';

require('Public/js/weixin.es6');
var _pri = {
    bindUI: function bindUI() {
        $('.js-close').on('click', function () {
            wx.closeWindow();
        });
    },
    init: function init() {
        this.bindUI();
    }
};
_pri.init();

});
