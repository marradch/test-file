<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <form>
                <div class="form-group">
                    <label><b>Select File:</b></label>
                    <input type="file" class="form-control" id="fileInput">
                </div>

                <div id="fileList"></div>

                <div class="form-group mt-3">
                    <a id="uploadBtn" href="javascript:;" class="btn btn-success">Upload</a>
                </div>
            </form>

            <div class="progress"></div>

            <div id="status"></div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/plupload.full.min.js"></script>
    </body>
<html>

<script>
    var uploader = new plupload.Uploader({
        runtimes : 'html5',
        browse_button : 'fileInput',
        url : 'upload',
        chunk_size: "10mb",
        multi_selection: false,
        multipart: true,
        multipart_params: {
            "_token": '{{ csrf_token() }}'
        },
        init: {
            PostInit: function() {
                document.getElementById('fileList').innerHTML = '';

                document.getElementById('uploadBtn').onclick = function() {
                    if (uploader.files.length < 1) {
                        document.getElementById('status').innerHTML = '<div class="alert alert-danger mt-3">Please select a file to upload.</p>';
                        return false;
                    }else{
                        uploader.start();
                        return false;
                    }
                };
            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    document.getElementById('fileList').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function(up, file) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                document.querySelector(".progress").innerHTML = '<div class="progress-bar" style="width: '+file.percent+'%;">'+file.percent+'%</div>';
            },

            FileUploaded: function(up, file, result) {
                var objResponse = JSON.parse(result.response);
                document.getElementById('status').innerHTML = '<div class="alert alert-success mt-3">' + objResponse.message + '</div>';
            },

            Error: function(up, err) {
                document.getElementById('status').innerHTML = '<div class="alert alert-danger mt-3">Error #' + err.code + ': ' + err.message + '</div>';
            }
        }
    });

    uploader.init();
</script>
