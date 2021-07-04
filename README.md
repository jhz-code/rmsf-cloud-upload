# rmsf-cloud-upload


#### 简述：

~~~

包含：上传文件到本地、上传文件到腾讯cos、上传文件到阿里云oos

可上传超大文件、不受限制 

加密访问相关文件

~~~

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


### 增加图片库管理需求
~~~
配置开关 ： is_store

···································

为true时：
rm_static_upload 表记录相关上传文件数据

为false时：
只返回上传数据, 不记录数据表

·······································

rm_static_upload 

记录所有上传图片的数据

便于通过该表管理所有上传的文件数据
~~~



#### 前端分片请求参数

~~~

file: (binary)   //文件名称
blob_num: 1     //分块序号
total_blob_num: 284  //总共分块
file_name:   11.zip  //文件名
~~~


#### axios实现

~~~

function upload (file, num) {
    file = "" //此处重点,获取选中的文件
    let formData = new FormData();
    let chunkTotal = Math.ceil(file.size / _this.chunkSize); // 总的chunk数
    let nextSize = Math.min((num + 1) * _this.chunkSize, file.size);
    let fileData = file.slice(num * _this.chunkSize, nextSize);
    formData.append("file", fileData);
    formData.append("name", time);
    formData.append("chunk", num);
    formData.append("chunks", chunkTotal);
    let config = {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }
    axios.post('http://xxx.xxx.com/upload', formData, config).then(res => {
        if (res.data && res.data.code === 0) {
            if (num < chunkTotal - 1) {
                 upload(file, num + 1); //递归调用
            }
        } else {
            alert(res)
        }
    }, err => {
        alert('请求出错')
    })
}

upload(file, 0);//执行第一个文件块

~~~

#### jq实现

~~~
function upload (file, num) {
    file = "" //此处重点,获取选中的文件
    let formData = new FormData();
    let chunkTotal = Math.ceil(file.size / _this.chunkSize); // 总的chunk数
    let nextSize = Math.min((num + 1) * _this.chunkSize, file.size);
    let fileData = file.slice(num * _this.chunkSize, nextSize);
    formData.append("file", fileData);
    formData.append("name", time);
    formData.append("chunk", num);
    formData.append("chunks", chunkTotal);
    $.ajax({
        url: "http://xxx.xxx.com/upload",
        type: "POST",
        data: formData,
        xhrFields: {
             withCredentials: true // 这里设置了withCredentials
        },
    processData: false,
    contentType: false,
    success: function (body) {
        if (body.code === 0) {
            if (num < chunkTotal - 1) {
                 upload(file, num + 1); //递归调用
            }
        } else {
            alert(body)
        }
    }
    });
}
upload(file, 0);

~~~
