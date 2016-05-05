
        $('.js-clear-all').on('click', function () {
            var con1 = confirm('你确定要删除全部课程吗？这个在更换学期时使用');
            var con2;
            if (con1){
                con2 = confirm('再次确定删除全部课程！删除后无法恢复');
            } else {
                return;
            }
            if (con1 && con2) {
                $.ajax({
                    url: '/?c=Schedule&a=clearAll',
                    type: 'get',
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.status == 0) {
                            location.reload();
                        } else {
                            alert(resp.msg);
                        }
                    },
                    error: function () {
                        alert('服务器错误');
                    }
                });
            }
        });


        $('.js-search-lession-input').on('keyup', function (e) {
            if(e.keyCode==13){
                $('.js-search-lession').trigger('click');
            }
        });
        $('.js-search-lession').on('click', function () {
            var keyword = $('.js-search-lession-input').val();
            location.href = '?c=Admin&a=course&keyword=' + keyword;
        });