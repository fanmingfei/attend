define('Call/js/callDetail.es6', function(require, exports, module) {

'use strict';

var _pri = {
    bindUI: function bindUI() {
        $('.js-status').on('click', function () {
            var $this = $(this);

            var sid = $this.closest('tr').attr('data-id');
            var callid = $this.closest('table').attr('data-id');
            var state = $this.attr('data-state');
            if (!sid || !callid || !state) {
                alert('出现问题');
            }
            _pri.util.statusClick(callid, sid, state);
        });
    },
    util: {
        statusClick: function statusClick(callid, sid, state) {
            $.ajax({
                url: '/?c=Call&a=setStatus',
                data: {
                    callid: callid,
                    sid: sid,
                    status: state
                },
                dataType: 'json',
                type: 'post',
                success: function success(resp) {
                    if (resp.status !== 0) {
                        alert(resp.msg);
                        return;
                    }
                    location.reload();
                },
                error: function error() {
                    alert('服务器错误');
                },
                complete: function complete() {}
            });
        }
    },
    init: function init() {
        this.bindUI();
    }
};

_pri.init();

});
