define('User/js/teacherReg.es6', function(require, exports, module) {

'use strict';

var _pri = {
    bindUI: function bindUI() {
        $('.js-submit').on('click', function () {
            _pri.util.submitTeacher();
        });
    },
    util: {
        submitTeacher: function submitTeacher() {
            var btn = $('.js-submit');
            if (btn.hasClass('weui_btn_disabled')) {
                return;
            }
            btn.addClass('weui_btn_disabled');
            var username = $('input[name="username"]').val();
            var phone = $('input[name="phone"]').val();
            var openid = $('input[name="openid"]').val();
            if (!username) {
                alert('请填写姓名');
                btn.removeClass('weui_btn_disabled');
                return;
            }
            if (!phone) {
                alert('请填写手机号');
                btn.removeClass('weui_btn_disabled');
                return;
            }
            if (!openid) {
                alert('出现错误！请联系管理员');
                btn.removeClass('weui_btn_disabled');
                return;
            }

            var data = {
                username: username,
                phone: phone,
                openid: openid
            };

            $.ajax({
                url: '/?c=User&a=registerTeacher',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function success(resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        btn.removeClass('weui_btn_disabled');
                        return;
                    }
                    location.href = "/?c=User&a=bindSuccess";
                },
                error: function error() {
                    btn.removeClass('weui_btn_disabled');
                    alert('服务器错误，请重试');
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
