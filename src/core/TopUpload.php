<?php

namespace RmTop\RmUpload\core;

use RmTop\RmUpload\lib\TopSliceUpload;

class TopUpload
{


     static   function TopSliceUpload(){
         $upload = new TopSliceUpload($_FILES["file"]["tmp_name"],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
         return $upload->execute();
    }



}