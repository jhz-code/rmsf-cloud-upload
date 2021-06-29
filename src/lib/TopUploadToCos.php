<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/28
 * Time: 10:38 下午
 */


namespace RmTop\RmUpload\lib;

use Qcloud\Cos\Client;
use think\Exception;

/**
 * 将本地文件上传至腾讯云cos
 * Class TopUploadToCos
 * @package RmTop\RmUpload\lib
 *
 */
class TopUploadToCos
{

    protected string $COS_SECRETID;
    protected string $COS_SECRETKEY;
    protected string $COS_REGION;
    protected Client $cosClient;


    /**
     * 初始化配置文件
     * TopUploadToCos constructor.
     * @param string $COS_SECRETID "云 API 密钥 SecretId";
     * @param string $COS_SECRETKEY "云 API 密钥 SecretKey";
     * @param string $COS_REGION 设置一个默认的存储桶地域
     */
    public function __construct(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $schema = 'http')
    {
        $this->COS_SECRETID = $COS_SECRETID;
        $this->COS_SECRETKEY = $COS_SECRETKEY;
        $this->COS_REGION = $COS_REGION;
        $this->cosClient = new Client(array(
            'region' => $COS_REGION,
            'schema' => $schema, //协议头部，默认为http
            'credentials'=> array(
                'secretId'  => $COS_SECRETID,
                'secretKey' => $COS_SECRETKEY,
            )));
    }


    /**
     * cos 创建静态文件桶
     * @param string $backName //存储桶名称 格式：BucketName-APPID
     * @throws Exception
     */
    function topCreateCos(string $backName): string
    {
        try {
            $bucket = $backName;
            $result = $this->cosClient->createBucket(array('Bucket' => $backName));
            //请求成功
            return $backName;
        } catch (\Exception $e) {
            //请求失败
            throw  new Exception($e->getMessage());
        }
    }


    /**
     * @return object
     * 获取静态储存桶列表
     * @throws Exception
     */
    function topListBuckets()
    {
        try {
            //请求成功
            return $this->cosClient->listBuckets();
        } catch (\Exception $e) {
            //请求失败
            throw  new Exception($e->getMessage());
        }
    }


    //------------------------  文件上传 ------------------

    ### 上传文件流
    ### putObject(上传接口，最大支持上传5G文件)
    /**
     * @param string $buckName //存储桶名称 格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @param string $LocalPath //本地文件绝对路径
     * @throws Exception
     */
    function putObjectForFile(string $buckName,string $key,string $LocalPath )
    {
        try {
            $file = fopen($LocalPath, "rb");
            if ($file) {
                return $this->cosClient->putObject(array('Bucket' => $buckName,'Key' => $key,'Body' => $file));
            }else{
                throw  new Exception("LocalPath nof fund");
            }
        } catch (\Exception $e) {
            throw  new Exception($e->getMessage());
        }
    }




    /**
     *
     *     ## Upload(高级上传接口，默认使用分块上传最大支持50T)
     * @param string $buckName
     * @param string $key
     * @param string $LocalPath
     * @return object
     * @throws Exception
     */
        function UploadForFile(string $buckName,string $key,string $LocalPath )
        {
            try {
                $file = fopen($LocalPath, 'rb');
                if ($file) {
                    $result = $this->cosClient->Upload($buckName,$key,$file);
                }else{
                    throw  new Exception("LocalPath nof fund");
                }
                return $result;
            } catch (\Exception $e) {
                throw  new Exception($e->getMessage());

            }
    }



    // ---------------------------- 内存中的字符串上传 ------------------------


    /**
     *  putObject(上传接口，最大支持上传5G文件)
     * @param string $buckName //存储桶名称 格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @param string $content
     * @return object
     * @throws Exception
     */
    function putObjectForString(string $buckName,string $key,string $content)
    {
        try {
            return $this->cosClient->putObject(array(
                'Bucket' => $buckName,
                'Key' => $key,
                'Body' => $content));
        } catch (\Exception $e) {
            throw  new Exception($e->getMessage());
        }
    }


    /**
     *  ## Upload(高级上传接口，默认使用分块上传最大支持50T)
     * @param string $buckName //存储桶名称 格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @param string $content
     * @return object
     * @throws Exception
     */
    function UploadForString(string $buckName,string $key,string $content)
    {
        try {
            return $this->cosClient->Upload($buckName, $key, $content);
        } catch (\Exception $e) {
            throw  new Exception($e->getMessage());
        }
    }



    //------------------------------  下载文件 -------------------------------


    /**
     * 将文件保存到服务器本地
     * @param string $buckName /存储桶，格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @param string $content //下载到本地指定路径
     * @throws Exception
     */
    function topDownFileToLocal(string $buckName,string $key,string $localPath){
        try {
            $result = $this->cosClient->getObject(array(
                'Bucket' => $buckName,
                'Key' => $key,
                'SaveAs' => $localPath));
        } catch (\Exception $e) {
            // 请求失败
            throw  new Exception($e->getMessage());
        }
    }


    /**
     * 下载文件到内存 然后执行输出下载
     * @param string $buckName //存储桶，格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @return mixed
     * @throws Exception
     */
    function topDownFileToMemory(string $buckName,string $key){
        ### 下载到内存
        try {
            $result = $this->cosClient->getObject(array(
                'Bucket' => $buckName,
                'Key' => $key));
            // 请求成功
            return $result['Body'];
        } catch (\Exception $e) {
            // 请求失败
            throw  new Exception($e->getMessage());
        }
    }


    /**
     * getObjectUrl(获取文件 UrL)
     * @param string $buckName //存储桶，格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @throws Exception
     */
    function topGetFileUrl(string $buckName,string $key,string $times){
        try {
            // 请求成功 返回文件签名地址
            return $this->cosClient->getObjectUrl($buckName, $key, '+10 minutes');
        } catch (\Exception $e) {
            // 请求失败
            throw  new Exception($e->getMessage());
        }
    }


    /**
     * # 删除 object
    ## deleteObject
     * @param string $buckName //存储桶，格式：BucketName-APPID
     * @param string $key //此处的 key 为对象键，对象键是对象在存储桶中的唯一标识
     * @throws Exception
     */
    function topDeleteFile(string $buckName,string $key)
    {
        try {
            // 请求成功
            return $this->cosClient->deleteObject(array(
                'Bucket' => $buckName,
                'Key' => $key,
                'VersionId' => 'string'
            ));
        } catch (\Exception $e) {
            // 请求失败
            throw  new Exception($e->getMessage());
        }
    }


    /**
     * # 删除多个 object  deleteObjects
     * @param string $buckName
     * @param array $keys array(array('Key'=>$key1),array('Key'=>$key1))
     * @return object
     * @throws Exception
     */
    function topDeleteManyFile(string $buckName,array $keys)
    {
        try {
            // 请求成功
            return $this->cosClient->deleteObjects(array(
                'Bucket' => $buckName,
                'Objects' => $keys,
            ));
        } catch (\Exception $e) {
            // 请求失败
            throw  new Exception($e->getMessage());
        }
    }


 }