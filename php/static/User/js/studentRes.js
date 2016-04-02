define('User/js/studentRes.es6', function(require, exports, module) {

'use strict';

var _pri = {
    bindUI: function bindUI() {
        $('.js-submit').on('click', function () {
            _pri.util.submitStudent();
        });
    },
    util: {
        submitStudent: function submitStudent() {

            var username = $('input[name="username"]').val();
            var classid = $('select[name="classid"]').val();
            var wename = $('input[name="wename"]').val();
            var openid = $('input[name="openid"]').val();
            if (!username) {
                alert('请填写姓名');
                return;
            }
            if (!classid) {
                alert('请选择班级');
                return;
            }
            if (!wename) {
                alert('请填写微信号');
                return;
            }
            if (!openid) {
                alert('出现错误！请联系管理员');
                return;
            }
            var data = {
                username: username,
                classid: classid,
                wename: wename,
                openid: openid
            };

            $.ajax({
                url: '/?c=User&a=registerStudent',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function success(resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        return;
                    }
                    location.href = "/?c=User&a=bindSuccess";
                }
            });
        }
    },

    init: function init() {
        this.bindUI();
    }
};

_pri.init();

});
