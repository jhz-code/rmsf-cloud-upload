<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/7/4
 * Time: 9:34 上午
 */


namespace RmTop\RmUpload\model;

use think\Model;

class TopStaticUploadModel   extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'rm_static_upload';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

}