<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends BackController {
    var $httpsqs;

    public function _initialize() {
        parent::_initialize();
        import("Vendor.httpsqs.httpsqs");
        $httpConfig = C('httpsqs');
        $this->httpsqs = new \httpsqs($httpConfig['host'], $httpConfig['port'], $httpConfig['auth'] , $httpConfig['charset']);
    }

    public function getList() {
        $page = I('page', '1');
        $size = I('size', '10');
        $offset = ($page-1) * $size;
        $where = [];
        if(I('title')) {
            $where['title'] = I('title');
        }

        $msgModel = D('Message');
        $total = $msgModel->getList($where, null, null, null, true);
        $msgList = $msgModel->getList($where, $page, $size, 'id desc');
        $data = [
            'data' => $msgList,
            'pageInfo' => [
                'currentPage' => $page,
                'size' => $size,
                'offset' => $offset,
                'total' => $total,
                'pages' => ceil( $total / $size ),
            ]
        ];
        ajax_return($data, 0, '获取成功');
    }

    public function addMessage()
    {
        $httpsqs = $this->httpsqs;
        //$message = $httpsqs->put('message', 123);
        var_dump($httpsqs->get('message'));

    }
}