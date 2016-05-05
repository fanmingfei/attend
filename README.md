# 微信考勤系统

ThinkPHP + FIS3

1、新建一个本地虚拟主机，虚拟主机根目录为php文件夹，主机域名为：www.dianming.com,配置host
2、在h5文件夹下执行 fis3 release
3、进入微信web开发者工具，登陆

/?c=Wechat&a=wxLogin  可绑定学生（绑定学生需要在数据库里有这名学生信息）
/?c=Wechat&a=wxLogin&type=t  可绑定老师

/?c=index&a=sign        识别学生还是老师进入点名


/?c=Call&a=callList     我的点名列表

/?c=Sign&signList       我的签到列表



/?c=Classes&a=addClass
/?c=Classes&a=addStudent