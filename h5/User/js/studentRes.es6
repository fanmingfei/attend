var _pri = {
    bindUI: function () {
        $('.js-submit').on('click', function () {
            _pri.util.submitStudent();
        });
    },
    util: {
        submitStudent: function () {
            
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
                success: function (resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        return;
                    }
                    location.href="/?c=User&a=bindSuccess";
                }
            });
        }
    },

    init: function () {
        this.bindUI();
    }
}

_pri.init();