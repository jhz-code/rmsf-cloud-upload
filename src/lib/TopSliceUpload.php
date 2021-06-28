<?php


namespace RmTop\RmUpload\lib;

/**
 * 分片上传操作
 * Class TopSliceUpload
 * @package RmTop\RmUpload\lib
 */

class TopSliceUpload
{

    private $filepath = ''; //上传目录
    private $tmpPath; //PHP文件临时目录
    private $blobNum; //第几个文件块
    private $totalBlobNum; //文件块总数
    private $fileName; //文件名


    /**
     * TopSliceUpload constructor.
     * @param $tmpPath
     * @param $blobNum
     * @param $totalBlobNum
     * @param $fileName
     * @param $md5FileName
     */
    public function __construct($tmpPath,$blobNum,$totalBlobNum,$fileName, $md5FileName ="")
    {
        $this->filepath = dirname(__DIR__)."/temp/upload";
        $this->tmpPath = $tmpPath;
        $this->blobNum = $blobNum;
        $this->totalBlobNum = $totalBlobNum;
        $this->fileName = $this->createName($fileName, $md5FileName);
        $this->moveFile();
        $this->fileMerge();
    }


    //返回数据
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
            return mkdir($this->filepath);
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




}