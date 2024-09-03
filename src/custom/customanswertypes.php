<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

/* NUBIS CUSTOM ANSWER TYPES - DO NOT REMOVE */

/* PICTURE */

function takePicture($variablename, $primkey = null) {
    if ($primkey == null) {
        global $engine;
        $primkey = $engine->getPrimaryKey();
    }

    $contentStr = '';
//<!--<td valign=top><photo id="photo"></photo></td>-->

    $contentStr .= "<script type='text/javascript' src='js/mootools-core-1.4.5-nocompat.js'></script>";
//<button id="upload" onclick="upload()">Upload</button>

    $contentStr .= '<canvas id="canvas" style="display:none"></canvas>';
    $contentStr .= '<table>
<tr><th></th><th></th><th>Current picture</th></tr>
<tr>
<td valign=top><video id="video"></video></td>
<td valign=center style="padding:5"><button id="startbutton" class="btn btn-default">(Re)Take Photo --></button></td>
<td valign=top><img id="photo" src="custom/picture/index.php?k=' . encryptC(Config::pictureKey(), Config::smsComponentKey()) . '&id=' . $primkey . '&fieldname=' . $variablename . '&p=show" width="200"></td>
</tr></table>



<script type=\'text/javascript\'>//<![CDATA[ 

(function() {

  var streaming = false,
      video        = document.querySelector(\'#video\'),
      cover        = document.querySelector(\'#cover\'),
      canvas       = document.querySelector(\'#canvas\'),
      photo        = document.querySelector(\'#photo\'),
      startbutton  = document.querySelector(\'#startbutton\'),
      width = 450,
      height = 0;

  navigator.getMedia = ( navigator.getUserMedia || 
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia);

  navigator.getMedia(
    { 
      video: true, 
      audio: false 
    },
    function(stream) {
      if (navigator.mozGetUserMedia) { 
        video.mozSrcObject = stream;
      } else {
        var vendorURL = window.URL || window.webkitURL;
        video.src = vendorURL ? vendorURL.createObjectURL(stream) : stream;
      }
      video.play();
    },
    function(err) {
      console.log("An error occured! " + err);
    }
  );

  video.addEventListener(\'canplay\', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width);
      video.setAttribute(\'width\', width);
      video.setAttribute(\'height\', height);
      canvas.setAttribute(\'width\', width);
      canvas.setAttribute(\'height\', height);
      streaming = true;
    }
  }, false);

  function takepicture() {
    canvas.width = width;
    canvas.height = height;
    canvas.getContext(\'2d\').drawImage(video, 0, 0, width, height);
    var data = canvas.toDataURL(\'image/png\');
    photo.setAttribute(\'src\', data);
    upload();
  }

  startbutton.addEventListener(\'click\', function(ev){
      takepicture();
    ev.preventDefault();
  }, false);



})();



  var API_KEY = \'eb18642b5b220484864483b8e21386c3\';

  function upload() {
  console.log("upload");
    var head = /^data:image\/(png|jpg);base64,/,
        fd = new FormData(),toSend,
        xhr = new XMLHttpRequest(),
        links = \'\',
        data = \'\';

    setstate(\'uploading\');
    data = (\'mozGetAsFile\' in canvas) ?
           canvas.mozGetAsFile(\'webcam.png\') :
           canvas.toDataURL(\'image/png\').replace(head, \'\');
    if (data.length > 20000){ //only upload if pic it bigger than 20000

        fd.append(\'image\', data);
        xhr.open(\'POST\', \'custom/picture/index.php?k=' . encryptC(Config::pictureKey(), Config::smsComponentKey()) . '&id=' . $primkey . '&fieldname=' . $variablename . '\');
        xhr.addEventListener(\'error\', function(ev) {
          console.log(\'Upload Error :\');
        }, false);
        xhr.addEventListener(\'load\', function(ev) {
        }, false);
        xhr.send(fd);
     }
  }

  function setstate(newstate) {
    state = newstate;
    document.body.className = newstate;  
  } 

//]]>  



</script>';



    return $contentStr;
}

function takePicture2($varname, $primkey) {


    $returnStr .= '<video autoplay></video>
	<img src="">
	<canvas style="display:none;"></canvas>';

    $returnStr .= '<script>
  var video = document.querySelector("video");
  var canvas = document.querySelector("canvas");
  var ctx = canvas.getContext("2d");
  var image = document.querySelector("img");
  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

  window.URL = window.URL || window.webkitURL;


  function snapshot() {
      var cw = video.clientWidth;
      var ch = video.clientHeight;
      ctx.drawImage(video, 0, 0, cw, ch, 0, 0, cw / 2, ch / 3);
      image.src = canvas.toDataURL();
      image.height = ch;
      image.width = cw;
  }

  video.addEventListener("click", snapshot, false);

if (navigator.getUserMedia) {
    navigator.getUserMedia({ video: true,audio:true},
      function(stream) {
         video.src = window.URL.createObjectURL(stream);
         video.onloadedmetadata = function(e) {
           video.play();
         };
      },
      function(err) {
         console.log("The following error occured: " + err.name);
      }
   );
} else {
   console.log("getUserMedia not supported");
}
</script>';

    return $returnStr;
}

function takeBarCode() {
    global $engine;
    $contentStr = '';
    $contentStr .= '<script type="text/javascript" src="js/DecoderWorker.js"></script>';

    $contentStr .= '<table><tr><td valign=top>
  <video id="video"></video></td><td valign=middle align=center width="200px">
  <button id="startbuttonq" class="btn btn-default">Start scanning</button><br/>

<p id="textbit" style="font-family:arial;color:red;font-size:18px;">Press "Start scanning"</p>

  </td><td valign=top>
  <canvas id="canvas"></canvas></td></tr>
  </table>
	<script type="text/javascript">
 
$(document).ready(function(){
                           // $("#answer2").val("noscan");
                            $("#answer2").attr("readonly", "readonly"); 
//if empty: dont allow to go back or next
       if ($("#answer2").val() == ""){
          $("#uscic-backbutton").prop("disabled", "disabled");
          $("#uscic-nextbutton").prop("disabled", "disabled");
        }


                         
                        }); 

 (function() {
var scanInterval;
  $("#startbuttonq").click(function(){
//alert("hhhh");
      $("#startbuttonq").prop("value", "Scanning..");
      $("#startbuttonq").prop("disabled", "disabled");
      $("#uscic-backbutton").prop("disabled", "disabled");
      $("#uscic-nextbutton").prop("disabled", "disabled");
      $("#answer2").val("noscan");
     scanInterval = setInterval(function() {  takepicture();}, 3000);
return false;
  });
	

  var streaming = false,
      Result       = document.querySelector("#textbit"),
      video        = document.querySelector("#video"),
      canvas       = document.querySelector("#canvas"),
      photo        = document.querySelector("#photo"),
      startbutton  = document.querySelector("#startbutton"),
      width = 200,
      height = 175;
  
  var scanNumbers = 0;

  navigator.getMedia = ( navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia);

  navigator.getMedia(
    {
      video: true,
      audio: false
    },
    function(stream) {
      if (navigator.mozGetUserMedia) {
        video.mozSrcObject = stream;
      } else {
        var vendorURL = window.URL || window.webkitURL;
        video.src = vendorURL.createObjectURL(stream);
      }
      video.play();
    },
    function(err) {
      console.log("An error occured! " + err);
    }
  );

  video.addEventListener(\'canplay\', function(ev){
    if (!streaming) {
      //height = video.videoHeight / (video.videoWidth/width);
      video.setAttribute(\'width\', width);
      video.setAttribute(\'height\', height);
      canvas.setAttribute(\'width\', width);
      canvas.setAttribute(\'height\', height);
      streaming = true;
    }
  }, false);

  function takepicture() {
    scanNumbers++;
    
    canvas.width = width;
    canvas.height = height;
    canvas.getContext(\'2d\').drawImage(video, 0, 0, width, height);
   	var data = canvas.toDataURL(\'image/png\');
  	var resultArray = [];
			ctx = canvas.getContext(\'2d\');
			var workerCount = 0;
			function receiveMessage(e) {
				if(e.data.success === "log") {
					console.log(e.data.result);
					return;
				}
				workerCount--;
				if(e.data.success){
					var tempArray = e.data.result;
                                        var r1stcode = "";
					for(var i = 0; i < tempArray.length; i++) {
						if(resultArray.indexOf(tempArray[i]) == -1) {
							resultArray.push(tempArray[i]);
                                                    if (r1stcode == ""){
                                                      r1stcode = tempArray[i];
                                                    }
						}
					}

					Result.innerHTML=resultArray.join("<br />") + "<br />Done! Press next";
		                        $("#answer2").val(r1stcode.replace("Code39: ", "").replace("*",""));
clearInterval(scanInterval);
      $("#uscic-backbutton").prop("disabled", false);
      $("#uscic-nextbutton").prop("disabled", false);
//alert("match");
				}else{
					if(resultArray.length === 0 && workerCount === 0) {
						Result.innerHTML= "Try: " + (scanNumbers) + " Decoding failed.";
					}
				}
				if (scanNumbers > 10){
				      clearInterval(scanInterval);
				      Result.innerHTML="Could not scan barcode please enter manually!";
				$("#answer2").val("timeout");
      $("#uscic-backbutton").prop("disabled", false);
      $("#uscic-nextbutton").prop("disabled", false);
                           }


			}
			var DecodeWorker = new Worker("js/DecoderWorker.js");
			var RightWorker = new Worker("js/DecoderWorker.js");
			var LeftWorker = new Worker("js/DecoderWorker.js");
			var FlipWorker = new Worker("js/DecoderWorker.js");
			DecodeWorker.onmessage = receiveMessage;
			RightWorker.onmessage = receiveMessage;
			LeftWorker.onmessage = receiveMessage;
			FlipWorker.onmessage = receiveMessage;   
	        DecodeBar();

  
 			function DecodeBar(){
         processImage();
				
			} 
  function processImage() {
  					resultArray = [];
					workerCount = 4;
					Result.innerHTML="";
					DecodeWorker.postMessage({pixels: ctx.getImageData(0,0,canvas.width,canvas.height).data, width: canvas.width, height: canvas.height, cmd: "normal"});
					RightWorker.postMessage({pixels: ctx.getImageData(0,0,canvas.width,canvas.height).data, width: canvas.width, height: canvas.height, cmd: "right"});
					LeftWorker.postMessage({pixels: ctx.getImageData(0,0,canvas.width,canvas.height).data, width: canvas.width, height: canvas.height, cmd: "left"});
					FlipWorker.postMessage({pixels: ctx.getImageData(0,0,canvas.width,canvas.height).data, width: canvas.width, height: canvas.height, cmd: "flip"});
  }
  
  }


//}

})();

	

	</script>
';


    return $contentStr;
}

/* ADD CUSTOM ANSWER TYPE FUNCTIONS FOR YOUR SURVEY BELOW */
?>
