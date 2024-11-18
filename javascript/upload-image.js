const dropArea = document.getElementById("drop-area");
const inputFile = document.getElementById("input-file");
const imageView = document.getElementById("img-view");

// Store the original content of the img-view
const originalContent = imageView.innerHTML;

inputFile.addEventListener("change", function() {
  const files = inputFile.files;
  
  if (files.length > 4) {
    alert("You can only upload up to 4 images.");
    
    // Reset to the original content
    imageView.innerHTML = originalContent;
    
    inputFile.value = ""; // Clear the selected files
  } else {
    uploadImages();
  }
});

function uploadImages() {
  // Clear the container for new images
  imageView.innerHTML = "";

  // Loop through all the selected files
  Array.from(inputFile.files).forEach(file => {
  const imgLink = URL.createObjectURL(file);

  // Create a new img element for each image
  const img = document.createElement("img");
  img.src = imgLink;
  img.classList.add("upload-pic");
  
  // Append each image to the img-view container
  imageView.appendChild(img);
  });
}

dropArea.addEventListener("dragover", function(e) {
  e.preventDefault();
});

dropArea.addEventListener("drop", function(e) {
  e.preventDefault();

  const files = e.dataTransfer.files;

  if(files.length > 4){
     alert("You can only upload up to 4 images.");

     imageView.innerHTML = originalContent;

     inputFile.value = ""; 
  }else{
    inputFile.files = e.dataTransfer.files;

    uploadImages();
  }


  
});