<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/7/4
 * Time: 9:29 上午
 */


namespace RmTop\RmUpload\lib;

use RmTop\RmUpload\model\TopStaticUploadModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class TopUploadStore
 * @package RmTop\RmUpload\lib
 * 文件储存库
 */

class TopUploadStore
{


    /**
     * 获取文件列表
     * @param string $where
     * @param int $limit
     * @throws DbException
     */
  static function getFileList(string $where = "1", int $limit = 15){
       TopStaticUploadModel::where($where)->paginate($limit);
    }


    /**
     * @param string $fileKey
     * @param string $filePath
     */
   static function insertFile(string $fileKey ,string $filePath){
       TopStaticUploadModel::create([
           'key'=>$fileKey, //文件key
           'img_path'=>$filePath //文件地址
       ]);
    }


    /**
     * 删除文件
     * @param string $fileKey
     */
   static  function deleteFile(string $fileKey){
       $result =  TopStaticUploadModel::where([ 'key'=>$fileKey ])->find();
       if('local' == $result['type']){
           if(unlink($result['img_path'])){
              return $result->delete();
           }else{
               return false;
           }
       }else if('cos' == $result['type']){
             return $result->delete();
       }else{
           return false;
       }
    }


    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
  static function getIFileInfo(string $fileKey){
      return TopStaticUploadModel::where([
            'key'=>$fileKey, //文件key
        ])->find();
    }


    /**
     * 获取文件唯一ID
     */
   static  function getFileUniqId(): string
    {
        $data = 'rMtop_'.$_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'].time().rand();
        return sha1($data);
    }


}