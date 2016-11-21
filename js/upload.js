var pendingChanges = false;
var saving = false;
var fileCaptionData;
var batchCaptionData;

function fileSelected() {
    var file = document.getElementById('fileToUpload').files[0];

    if (file) {
        showFileDetails(file, 'fileName', 'fileSize', 'fileType');
    }
}

function imageSelected() {
    var file = document.getElementById('imageToUpload').files[0];

    if (file) {
        showFileDetails(file, 'imageName', 'imageSize', 'imageType');
    }
}

function showFileDetails(file, nameElement, sizeElement, typeElement) {
    var fileSize = 0;

    if (file.size > 1024 * 1024) {
            fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
    } else {
            fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
    }

    document.getElementById(nameElement).innerHTML = 'Name: ' + file.name;
    document.getElementById(sizeElement).innerHTML = 'Size: ' + fileSize;
    document.getElementById(typeElement).innerHTML = 'Type: ' + file.type;
}

function uploadFile() {
    var fd = new FormData();
    fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
    startUpload(fd);
}

function uploadImage() {
    var fd = new FormData();
    fd.append('imageToUpload', document.getElementById('imageToUpload').files[0]);
    startUpload(fd);
}
    
function startUpload(fd) {
    
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadProgress, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
    xhr.open("POST", "../admin/editproduct.php");
    xhr.send(fd);
    
    document.getElementById('upload-progress').style.display='block';
}

function uploadProgress(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
        document.getElementById('upload-progress-bar').style.width = percentComplete.toString() + '%';
    } else {
        document.getElementById('progressNumber').innerHTML = 'unable to compute';
    }
}

function uploadComplete(evt) {
    document.location = "../admin/editproduct.php";
}

function uploadFailed(evt) {
    alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
    alert("The upload has been canceled by the user or the browser dropped the connection.");
}

function onDetailsChange() {
    if (!pendingChanges) {
        document.getElementById('pendingChangesLabel').innerHTML = "Pending Changes";
        pendingChanges = true;
        if (!saving) {
            setTimeout(savePendingChanges, 5000);
        }
    }
}

function savePendingChanges() {
    pendingChanges = false;
    saving = true;
    
    document.getElementById('pendingChangesLabel').innerHTML = "Saving";
    
    var productTitle = document.getElementById("title").value;
    var productDescription = document.getElementById("description").value;
    var orderLink = document.getElementById("orderlink").value;
    var redeemLink = document.getElementById("redeemlink").value;
    
    var fd = new FormData();
    
    fd.append('title', productTitle);
    fd.append('description', productDescription);
    fd.append('orderlink', orderLink);
    fd.append('redeemlink', redeemLink);
    
    fd.append('numfilecaptions', fileCaptionData.length);
    
    for (var i = 0; i < fileCaptionData.length; i++) {
        fd.append('fileid' + i.toString(), fileCaptionData[i].fileId);
        fd.append('filecaption' + i.toString(), document.getElementById(fileCaptionData[i].inputId).value);
    }

    fd.append('numbatchcaptions', batchCaptionData.length);
    
    for (var i = 0; i < batchCaptionData.length; i++) {
        fd.append('batchid' + i.toString(), batchCaptionData[i].batchId);
        fd.append('batchcaption' + i.toString(), document.getElementById(batchCaptionData[i].inputId).value);
    }
    
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadProgress, false);
    xhr.addEventListener("load", onSaveComplete, false);
    xhr.open("POST", "../admin/savedetailchanges.php");
    xhr.send(fd);
}

function onSaveComplete() {
    saving = false;
    
    if (pendingChanges) {
        document.getElementById('pendingChangesLabel').innerHTML = "Pending Changes";
        setTimeout(savePendingChanges, 5000);
    } else {
        document.getElementById('pendingChangesLabel').innerHTML = "All Changes Saved";
    }
}

function setupCaptionInputs(fileInputs, batchInputs) {
    fileCaptionData = fileInputs;
    batchCaptionData = batchInputs;
}

function onCreateCodesClick() {
    document.getElementById('create-codes-modal').style.display='block';
}