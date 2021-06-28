<?php

namespace RmTop\RmUpload\core;

use RmTop\RmUpload\lib\TopSliceUpload;

class TopUpload
{


     static   function TopSliceUpload(){
        $upload = new TopSliceUpload('','','','');
        return $upload->execute();
    }



}