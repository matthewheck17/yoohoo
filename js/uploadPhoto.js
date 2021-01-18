var input = document.getElementById('toUpload');
var pasteLocation = document.getElementById('file-name');

var uploadButton = document.getElementById('file-upload');
var submitButton = document.getElementById('file-submit');

input.addEventListener('input', uploadFile);

function uploadFile() {
  pasteLocation.innerHTML = input.value;
  uploadButton.style.display = "none";
  submitButton.style.display = "unset";
}