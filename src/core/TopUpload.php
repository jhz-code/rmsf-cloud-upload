<?php

namespace RmTop\RmUpload\core;

use RmTop\RmUpload\lib\TopSliceUpload;
use RmTop\RmUpload\lib\TopUploadToCos;

class TopUpload
{


    /**
     * @param string $LocalPath
     * 文件上传到本地
     */
   static  function TopUploadLocal(string $LocalPath){
        $upload = new TopSliceUpload($_FILES["file"]["tmp_name"],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
        move_uploaded_file($upload->execute(),$LocalPath);//文件移动到目录地区
        $upload->deleteFile();
    }


    /**
     * 上传文件到腾讯cos
     * @param string $COS_SECRETID
     * @param string $COS_SECRETKEY
     * @param string $COS_REGION
     * @param string $buckName
     * @param string $key
     * @return object|string
     * @throws \think\Exception
     */
     static   function TopSliceUploadToCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key){
         $upload = new TopSliceUpload($_FILES["file"]["tmp_name"],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
         if($localPath = $upload->execute()){
             $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
             return $TopCos->putObjectForFile($buckName,$key,$localPath);
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
    static function TopDeleteFileFromCos(string $COS_SECRETID,string $COS_SECRETKEY,string $COS_REGION,string $buckName,string $key,){
        $TopCos = new TopUploadToCos($COS_SECRETID,$COS_SECRETKEY,$COS_REGION);
        return $TopCos->topDeleteFile($buckName,$key);
    }




}