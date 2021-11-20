<?php

namespace RmTop\RmUpload\lib;

class TopNoSliceUpload
{
    private string $filepath = ''; //上传目录
    private string $tmpPath; //文件临时目录
    private string  $fileName; //文件名


    /**
     * TopSliceUpload constructor.
     * @param $tmpPath
     * @param $blobNum
     * @param $totalBlobNum
     * @param $fileName
     * @param string $md5FileName
     */
    public function __construct($tmpPath,$fileName, string $md5FileName ="")
    {
        $this->filepath = dirname(__DIR__)."/temp/upload/".md5($fileName)."/";
        $this->tmpPath = $tmpPath;
        $this->fileName = $this->createName($fileName, $md5FileName);
        $this->moveFile();
    }


    //执行成功 返回文件地址
    public function execute(){
            if(file_exists($this->filepath.'/'. $this->fileName)){
                return $this->filepath.'/'. $this->fileName;
            }else{
                return  false;
            }
    }

    //本地上传执行
    public function execute_local(){
            if(file_exists($this->filepath.'/'. $this->fileName)){
                return $file;
            }else{
                return  false;
            }
    }


    /**
     * 文件归档
     */
    private function moveFile(){
        $this->top_mkdir();
        $filename = $this->filepath.'/'. $this->fileName;
        move_uploaded_file($this->tmpPath,$filename);
    }


    /**
     * 创建文件夹
     * @return bool
     */
    private function top_mkdir(){
        if(!file_exists($this->filepath)){
            return mkdir($this->filepath,0777,true);
        }
    }


    /**
     * 创建新的文件名
     * @param $fileName
     * @param $md5FileName
     * @return string
     */
    private function createName($fileName, $md5FileName){
        if($md5FileName){
            return $md5FileName . '.' . pathinfo($fileName)['extension'];
        }else{
            return "rTop_".md5($fileName.'rTop').'.' . pathinfo($fileName)['extension'];
        }
    }



    /**
     * 上传成功后，删除本地对应目录文件
     * 删除目录文件
     */
    function deleteFile(){
        //如果是目录则继续
        if(is_dir($this->filepath)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($this->filepath);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($this->filepath.$val)){
                        //子目录中操作删除文件夹和文件
                        $this->deleteFile();
                        //目录清空后删除空文件夹
                        @rmdir($this->filepath.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($this->filepath.$val);
                    }
                }
            }
            @rmdir($this->filepath);
        }
    }

}