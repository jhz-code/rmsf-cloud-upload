<?php

namespace RmTop\RmUpload\core;

use RmTop\RmUpload\lib\TopNoSliceUpload;
use RmTop\RmUpload\lib\TopSliceUpload;
use RmTop\RmUpload\lib\TopUploadStore;
use RmTop\RmUpload\lib\TopUploadToCos;

class TopUpload
{

    /**
     * @param string $LocalPath
     * @param string $fileName //自定义文件名称
     * @param bool $is_store //是否生产唯一表示 存到数据库 统一管理
     * 文件上传到本地
     * 增加路径验证、不存在路径则创建路径
     * @return array|string
     */
    static  function TopUploadLocal(string $LocalPath,string $fileName = '', bool $is_store = false){
        $upload = new TopSliceUpload($_FILES["file"]["tmp_name"],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
        if($result = $upload->execute_local()){
            //路径不存在，创建路径
            if(!file_exists($LocalPath)){
                mkdir($LocalPath,0777,true);
            }
            //移动文件到目标路径
            $saveFileName = $fileName?$fileName.".".substr(strrchr($result['fileName'], '.'), 1):$result['fileName'];

            if(copy($result['filePath'],$LocalPath.'/'.$saveFileName)){
                if(!$is_store){
                    $upload->deleteFile();//删除缓存库
                    return ['imgPath'=>'/'.$saveFileName,'key'=>""];
                }else{
                    $upload->deleteFile();//删除缓存库
                    TopUploadStore::insertFile('local',TopUploadStore::getFileUniqId(),'/'.$saveFileName);
                    return ['imgPath'=>'/'.$saveFileName,'key'=>TopUploadStore::getFileUniqId()];
                }
            }else{
                return '';
            }
        }else{
            return  '';
        }
    }




    /**
     * 上传文件到腾讯cos
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @return array|string
     * @throws \think\Exception
     */
    static   function TopSliceUploadToCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,bool $is_store = false){
        $upload = new TopSliceUpload($_FILES["file"]["tmp_name"],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
        if($localPath = $upload->execute()){
            $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
            $result = $TopCos->putObjectForFile($buckName,$key,$localPath);
            $upload->deleteFile();
            if($is_store){
                TopUploadStore::insertFile('cos', $result['Key'],'/'.$result['fileName']);
            }
            $info['ETag'] = $result['ETag'];
            $info['RequestId'] = $result['RequestId'];
            $info['Key'] = $result['Key'];
            $info['Bucket'] = $result['Bucket'];
            $info['Location'] = $result['Location'];
            return $info;
        }else{
            return "";
        }
    }


    /**
     * 不分片上传文件
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @param bool $is_store
     * @return array|string
     * @throws \think\Exception
     */
    static   function TopUploadToCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,bool $is_store = false){
        $upload = new TopNoSliceUpload($_FILES["file"]["tmp_name"],$_POST['name']);
        if($localPath = $upload->execute()){
            $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
            $result = $TopCos->putObjectForFile($buckName,$key,$localPath);
            $upload->deleteFile();
            if($is_store){
                TopUploadStore::insertFile('cos', $result['Key'],'/'.$result['fileName']);
            }
            $info['ETag'] = $result['ETag'];
            $info['RequestId'] = $result['RequestId'];
            $info['Key'] = $result['Key'];
            $info['Bucket'] = $result['Bucket'];
            $info['Location'] = $result['Location'];
            return $info;
        }else{
            return "";
        }
    }




    /**
     * 从腾讯TO---下载文件到内存
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @return mixed
     * @throws \think\Exception
     */
    static function TopDownFileForMemoryFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key){
        $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
        return $TopCos->topDownFileToMemory($buckName,$key);
    }


    /**
     * 从腾讯TO---下载文件到本地
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @param string $localPath
     * @return mixed
     * @throws \think\Exception
     */
    static function TopDownFileForLocalFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,string $localPath){
        $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
        $TopCos->topDownFileToLocal($buckName,$key,$localPath);
        return  $localPath;
    }


    /**
     * 获取可访问的URl 并设置可访问的时间
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @param $time
     * @return string
     * @throws \think\Exception
     */
    static function TopGetFileUrlFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,$time): string
    {
        $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
        return $TopCos->topGetFileUrl($buckName,$key,$time??'+5 minutes');
    }


    /**
     * 删除文件
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @return object
     * @throws \think\Exception
     */
    static function TopDeleteFileFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key){
        $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
        TopUploadStore::deleteFile($key);//删除本地库
        return $TopCos->topDeleteFile($buckName,$key);
    }




}