# 微信直播系统方案
> 利用第三方推流工具向流服务器推流，由Nginx的rtmp模块转为HLS可通过video标签播放的资源在微信公众号播放。从而达到位置直播的目的。


## 分布式数据库搭建
> 应对数据库的并发，配备两台mysql服务
> 一台负责写（master）
> 一台负责读（slave）
> 实现数据读写分离，应对异常时主从切换
> 具体的安装不再赘述 


```
array(
            'dbhost' => '127.0.0.1',
            'dbport' => '3306',
            'dbname' => 'live',
            'dbuser' => 'live',
            'dbpassword' => '1234live!@#$',
            'unix_socket' => '/data/mysql3306/mysql3306.sock',
        ),
        array(
            'dbhost' => '127.0.0.1',
            'dbport' => '3307',
            'dbname' => 'live',
            'dbuser' => 'live',
            'dbpassword' => '1234live!@#$',
            'unix_socket' => '/data/mysql3307/mysql3307.sock',
        ), 
```

## 分布式Redis搭建
> 同mysql服务一台写一台读


```
 array(
            'ip' => "127.0.0.1",
            'port' => '6379',
            'timeout' => 5,
            'auth' => '1234live!@#$',
        ),
        array(
            'ip' => "127.0.0.1",
            'port' => '6380',
            'timeout' => 5,
            'auth' => '1234live!@#$',
        ),
        
```
 

## 分布式Memcached（伪分布式）
> Memcached分布式其实是伪分布式，Memcached本身并不能达到分布式的效果，可以通过程序哈希出一个地址，通过地址将数据落在不同的Memcached服务器。

```
		 array(
            '127.0.0.1',
            '11211',
        ),
        array(
            '127.0.0.1',
            '11212',
        ),
        
```

## 直播流服务
## 消息服务
## 微信公众号
## 前端展示
