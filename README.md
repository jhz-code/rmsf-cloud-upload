# rmsf-cloud-upload

#### 安装插件

~~~
composer require rmtop/rmsf-cloud-upload
~~~


#### 上传文件到本地

~~~
$result = TopUpload::TopSliceUpload();
var_dump($result);
~~~

#### 上传文件到腾讯cos
~~~
$result = TopUpload::TopSliceUploadToCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key);
var_dump($result);
~~~


#### 下载文件到内存输出下载
~~~
$result = TopUpload::TopDownFileForMemoryFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key);
var_dump($result);
~~~

#### 下载文件到服务器本地
~~~
$result = TopUpload::TopDownFileForLocalFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,string $localPath);
var_dump($result);
~~~


#### 获取文件URl
~~~
$result = TopUpload::TopGetFileUrlFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,$time);
var_dump($result);
~~~


#### 删除文件
~~~
$result = TopUpload::TopDeleteFileFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key);
var_dump($result);
~~~

