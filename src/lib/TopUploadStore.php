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
     * @return \think\Paginator
     * @throws DbException
     */
  static function getFileList($where = 1, int $limit = 15){
      return  TopStaticUploadModel::where($where)->paginate($limit)->each(function (&$item){
          if($item['type'] == 'cos'){
              return $item['type_name'] = "腾讯cos";
          }else{
              return $item['type_name'] = "本地";
          }
      });
    }


    /**
     *
     * @param string $type
     * @param string $fileKey
     * @param string $filePath
     * @return TopStaticUploadModel|\think\Model
     */
   static function insertFile(string $type,string $fileKey ,string $filePath){
       return  TopStaticUploadModel::create([
           'type'=>$type,//文件类型
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