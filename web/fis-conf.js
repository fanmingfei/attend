fis.match('*.es6', {
    rExt: '.js',
    parser: fis.plugin('es6-babel'),
    release: '/static/$0'
});


// 开启模块化开发
fis.hook('module');
fis.match('*.es6', {
    isMod: true
});


fis.match('::package', {
    postpackager: fis.plugin('loader')
});

fis.media('dev')

.match('*.less', {
    parser: fis.plugin('less'),
    rExt: '.css',
    release: '/static/$0'
})

.match('*.js', {
    release: '/static/$0'
})
.match('*.css', {
    release: '/static/$0'
})
.match('*.{eot,svg,ttf,woff,woff2}', {
    release: '/static/$0'
})


.match('*.html', {
    release: '/Application/Home/View/$0'
})

.match('*', {
    deploy: fis.plugin('local-deliver', {
        to: '../php'
    }),
    domain: 'http://www.dianming.com/'
});






// fis.media('ceshi')
//     .match('*', {
//       deploy: fis.plugin('http-push', {
//         receiver: 'http://www.ldxdxx.com/receiver.php',
//         to: '/data/www/edire/ldxdxx/aaa' // 注意这个是指的是测试机器的路径，而非本地机器
//       })
//     })

// fis.set('project.md5Length', 8);


// fis.media('product')
//     .match('*.js', {
//         optimizer: fis.plugin('uglify-js'),
//         packTo: '/js/pkg.js',
//     }).match('*.css', {
//         optimizer: fis.plugin('clean-css'),
//         packTo: '/css/pkg.css',
//          // relative: true
//     }).match('*.less', {
//         parser: fis.plugin('less'),
//         rExt: '.css',
//         optimizer: fis.plugin('clean-css'),
//         packTo: '/css/pkg.css'
//     }).match('/css/pkg.css', {
//         useHash: true,
//         release: '/static/css/pkg.css', // fis.set('namespace', 'home'),
//         url: '/Public/css/pkg.css',
//         domain: 'http://www.jikexueyuan.com'
//     }).match('/js/pkg.js', {
//         useHash: true,
//         release: '/static/js/pkg.js' // fis.set('namespace', 'home'),

//     }).match('/common/**', {
//         release: false
//     }).match('/config/**', {
//         release: false
//     }).match('/test/**', {
//         release: false
//     }).match('server.conf', {
//         release: false
//     }).match('::packager', {
//         postpackager: fis.plugin('loader', {  // 打包后，讲html等链接到相关文件的href src 等替换成打包以后的地址
//             allInOne: true
//         })
//     })

//     .match('**', {
//         deploy: [
//             fis.plugin('skip-packed'),   // 打包后 把打包前的 单个文件删掉
//             fis.plugin('local-deliver', {
//                 to: '../out'
//             })
//         ]
//     })