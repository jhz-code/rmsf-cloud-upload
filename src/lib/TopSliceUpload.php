<?php


namespace RmTop\RmUpload\lib;

/**
 * 分片上传操作
 * Class TopSliceUpload
 * @package RmTop\RmUpload\lib
 */

class TopSliceUpload
{

    private string $filepath = ''; //上传目录
    private string $tmpPath; //文件临时目录
    private string $blobNum; //第几个文件块
    private string $totalBlobNum; //文件块总数
    private string  $fileName; //文件名


    /**
     * TopSliceUpload constructor.
     * @param $tmpPath
     * @param $blobNum
     * @param $totalBlobNum
     * @param $fileName
     * @param string $md5FileName
     */
    public function __construct($tmpPath, $blobNum, $totalBlobNum, $fileName, string $md5FileName ="")
    {
        $this->filepath = dirname(__DIR__)."/temp/upload/".md5($fileName)."/";
        $this->tmpPath = $tmpPath;
        $this->blobNum = $blobNum;
        $this->totalBlobNum = $totalBlobNum;
        $this->fileName = $this->createName($fileName, $md5FileName);
        $this->moveFile();
        $this->fileMerge();
    }


    //执行成功 返回文件地址
    public function execute(){
        if($this->blobNum == $this->totalBlobNum){
            if(file_exists($this->filepath.'/'. $this->fileName)){
                return $this->filepath.'/'. $this->fileName;
            }else{
                return  false;
            }
        }else{
          return  false;
        }
    }



    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge(){
        if($this->blobNum == $this->totalBlobNum){
            $blob = '';
            for($i=1; $i<= $this->totalBlobNum; $i++){
                $blob .= file_get_contents($this->filepath.'/'. $this->fileName.'__'.$i);
            }
            file_put_contents($this->filepath.'/'. $this->fileName,$blob);//合并文件
            $this->deleteFileBlob();
        }
    }




    //删除文件块
    private function deleteFileBlob(){
        for($i=1; $i<= $this->totalBlobNum; $i++){
            @unlink($this->filepath.'/'. $this->fileName.'__'.$i);
        }
    }


    /**
     * 文件归档
     */
    private function moveFile(){
        $this->top_mkdir();
        $filename = $this->filepath.'/'. $this->fileName.'__'.$this->blobNum;
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
            return "rTop_".md5(time().'rTop').'.' . pathinfo($fileName)['extension'];
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