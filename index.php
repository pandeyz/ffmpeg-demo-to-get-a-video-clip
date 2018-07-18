
<?php	

//include('function.php');
	
	$input_dir = dirname(__FILE__). "/input/";
	$output_dir = dirname(__FILE__). "/output/";
	
	if(isset($_POST["submit"])) {		
		if(file_exists($_FILES["user_video"]["tmp_name"])){
			 $temp_file = $_FILES["user_video"]["tmp_name"]; 
			
			$fileType = mime_content_type($temp_file);	
            
			if(!preg_match('/video\/*/', $fileType)) {	
				echo "Please upload a video";
				return;
			}
			
			// file name with extension
			$file = $_FILES["user_video"]["name"];	
			
			// name without extension
			$filename = pathinfo($file, PATHINFO_FILENAME);
			
			// Default extension
			$default = pathinfo($file, PATHINFO_EXTENSION);
			
			// create special string from date to ensure filename is unique
			$date = date("Y-m-d H:i:s");
			$uploadtime = strtotime($date);
			
			// upload path
			$video_file = $input_dir . "/" . $uploadtime ."_". $file;
			
			// check the specified extension
			if(!isset($_POST["extension"]) || $_POST["extension"] == ""){
				echo "Please set the output extension.";
				return;
			}
			$ext = $_POST["extension"]; // output extension	
			if($ext == "none") {
				$ext = $default;
			}			
			
			// put file to input directory to make it easier to be processed with ffmpeg
			$moved = move_uploaded_file($temp_file, $video_file);
			if($moved) {
				// change php working directory to where ffmpeg binary file reside
				chdir('C:\ffmpeg\bin');
				
				$start_from = "00:00:00";				
				// check the specified starting time
				if(isset($_POST["start_from"]) && $_POST["start_from"] != ""){
					$start_from = $_POST["start_from"];
				}				
				
				$length = 10;
				// check the specified duration
				if(isset($_POST["length"]) && $_POST["length"] != ""){
					$length = $_POST["length"];
				}
				
				$output = "$output_dir/$uploadtime"."_$filename.$ext";

				// shell_exec('ffmpeg -i C:\xampp\htdocs\videoCutting\small.mp4 -ss 00:00:00 -t 00:00:05 -async 1 C:\xampp\htdocs\videoCutting\smallNew.mp4');

				// shell_exec("ffmpeg -ss $length -i $video_file -t $start_from -vcodec copy -acodec copy -y $output");

				shell_exec("ffmpeg -i $video_file -ss $start_from -t $length -async 1 $output");
				
				// delete uploaded file from input folder to reserve disk space
				unlink($video_file);
				
				echo "<span>Edit Finished:</span>";
				
				echo "<a href='http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"."output/$uploadtime"."_$filename.$ext'>Download</a>";
			}
			
		} else {
			echo "<h3>No file was uploaded!</h3>";
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <title>Cutting Video clip</title>
 <meta name="keyword" content="video to gif, video shortener">
 <meta name="description" content="Convert video to gif or cut it out to shorter length">
 <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<style>
#main-area {
	width: 27%;
	margin: 0 auto;
	paddig: 20px;
	font-family: Arial;
}

#form-contents {
	font-size: 14px;
}

label, input, select {
	margin-bottom: 10px;
}
label {
	font-weight: bold;
}
input[type="submit"] {
	width: 125px;
}
a, a:hover {
	display: inline-block;
	text-decoration: none;	
}
a, input[type="submit"] {
	background: black;
	padding: 5px;
	border-radius: 2px;
	box-shadow: 0 2px 0 2px #333333;
	color: #fff;
	cursor: pointer;
}
span {
	display: block;
}
</style>
<div id="main-area">
	<div id="header">
		<h1>Video Splitting</h1>
		
	</div>

		<form method="post" action="" enctype="multipart/form-data" autocomplete="off">
			<div id="form-contents">

				<label for="video_file">Attach Video:</label> </br>
				<input id="video_file" type="file" name="user_video" value=""/></br>
				
				<label for="extension">Convert to:</label></br>
				<select name="extension" id="extension">
					<!-- <option value="none">Default</option>
					<option value="gif">gif</option> -->
					<option value="mp4" selected>mp4</option>
					<!-- You can add other format here -->
				</select></br>
				<label for="start_from">Start From:</label></br>
				<input type="text" name="start_from" id="start_from" value="" placeholder="example: 00:00:05"/>
				</br>
				<label for="length">End:</label></br>
				<input type="text" name="length" id="length" value="" placeholder="example: 08"/> seconds
				</br>
				<input type="submit" name="submit" value="Edit">
			</div>				
		</form>
</div>
</body>
</html>