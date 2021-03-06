var fileForm = document.getElementById("file");
var stopBtn = document.getElementById('stop');
var upload = new Upload();

fileForm.onchange = function(){
    upload.addFileAndSend(this);
}

stopBtn.onclick = function(){
    this.value = "停止中";
    upload.stop();
    this.value = "已停止";
}

function Upload(){
    var xhr = new XMLHttpRequest();
    var form_data = new FormData();
    const LENGTH = 1024 * 1024 *2;
    var start = 0;
    var end = start + LENGTH;
    var blob;
    var blob_num = 1;
    var is_stop = 0

    //对外方法，传入文件对象
    this.addFileAndSend = function(that){
        var file = that.files[0];
        blob = cutFile(file);
        sendFile(blob,file);
        blob_num  += 1;
    }

    //停止文件上传
    this.stop = function(){
        xhr.abort();
        is_stop = 1;
    }

    //切割文件
    function cutFile(file){
        var file_blob = file.slice(start,end);
        start = end;
        end = start + LENGTH;
        return file_blob;
    };

    //发送文件
    function sendFile(blob,file){
        var form_data = new FormData();
        var total_blob_num = Math.ceil(file.size / LENGTH);
        form_data.append('file',blob);
        form_data.append('blob_num',blob_num);
        form_data.append('total_blob_num',total_blob_num);
        form_data.append('file_name',file.name);
        xhr.open('POST','/admin/index/upload',false);

        xhr.onreadystatechange  = function () {
            if (xhr.readyState===4 && xhr.status===200)
            {
                console.log(xhr.responseText);
            }

            var progress;
            var progressObj = document.getElementById('finish');
            if(total_blob_num === 1){
                progress = '100%';
            }else{
                progress = Math.min(100,(blob_num/total_blob_num)* 100 ) +'%';
            }
            progressObj.style.width = progress;
            var t = setTimeout(function(){
                if(start < file.size && is_stop === 0){
                    blob = cutFile(file);
                    sendFile(blob,file);
                    blob_num  += 1;
                }else{
                    setTimeout(t);
                }
            },1000);
        }
        xhr.send(form_data);
    }
}
