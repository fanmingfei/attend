<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends BackController {
    
    public function addMessage()
    {
        import("Vendor.httpsqs.httpsqs");

        $httpConfig = C('httpsqs');

        $httpsqs = new \httpsqs($httpConfig['host'], $httpConfig['port'], $httpConfig['charset']);   
        $message = $httpsqs->put('message', 123);
        var_dump($message);

   

    }
}