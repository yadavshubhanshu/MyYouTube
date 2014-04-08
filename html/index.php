<!DOCTYPE html>

<html>
<head>
  <script type="text/javascript" src="/jwplayer/jwplayer.js"></script>
  <title>My Youtube</title>
</head>

<body>

<?php
    		$conn = mysql_connect('cloudassignment2.cgrln3rjnuzm.us-east-1.rds.amazonaws.com:3306', 'vaibhav', 'jagannathan');
			$db_connect = mysql_select_db('cloudassignment'); 
			//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
			
			//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', 'XXXXXXXXXXXXXXXXXXXX');
			if (!defined('awsSecretKey')) define('awsSecretKey', 'XXXXXXXXXXXXXXXXXXXXXXX');

			//instantiate the class
			$s3 = new S3(awsAccessKey, awsSecretKey);
			
			
			if (isset($_GET['delete'])) {
				$myid = $_GET['delete']; 
					if($s3->deleteObject("vaibhavjagannathanscloudassignment", $myid)) {
						$sql="DELETE FROM videos WHERE name='{$myid}'";
						$retval=mysql_query($sql,$conn);
						if(!$retval){
							die('Could not delete data: '.mysql_error());
						}
						echo "<script>alert('Video Deleted.');</script>";
					}
					else{
						echo "<script>alert('Something went wrong while deleting the video... sorry.');</script>";
						}
			}


			if(isset($_POST['Rate'])){
				//retreive post variables
				$newRate = $_POST['newRating'];
				$oldRate = $_POST['oldRating'];
				$current_file = $_POST['currentFile'];
				if($oldRate != 0){
					$newRate = ($newRate+$oldRate)/2;
				}
				$sql= "UPDATE videos SET rating={$newRate} WHERE name='{$current_file}'";
				$retval=mysql_query($sql,$conn);
				if(!$retval){
					die('Could not update data: '.mysql_error());
				}

			}


			//check whether a form was submitted
			if(isset($_POST['Upload'])){
				//retreive post variables
				$fileName = $_FILES['theFile']['name'];
				$fileTempName = $_FILES['theFile']['tmp_name'];
				$extension = substr($fileName,-4);
				if($extension == ".mp4" or $extension == ".flv") {
					//move the file to the bucket
					if ($s3->putObjectFile($fileTempName, "vaibhavjagannathanscloudassignment", $fileName, S3::ACL_PUBLIC_READ)) {
						$sql="INSERT INTO videos (name,rating,times) VALUES ('{$fileName}',0,now())";
						$retval=mysql_query($sql,$conn);
						echo "<script>alert('Video Uploaded Successfully.');</script>";
					}
					else{
						echo "<script>alert('Something went wrong while uploading the video... sorry.');</script>";
						}
				}
				else{
					echo "<script>alert('Invalid File. Only Videos can be uploaded. Please choose a .mp4 or .flv file. You tried to upload a {$extension} file.');</script>";
				}
			}


echo <<<EOD
  <div id="main">
  <h1>My Youtube</h1>
  <p></p>
  <p><a href="/stream.php">Live Streaming</a></p>
  </div>

<h2>Upload a Video</h2>
<p>Browse files and select a video to upload. (Upload files with extensions .mp4 or .flv only)</p>
   	<form action="index.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input name="theFile" type="file" />
      <input name="Upload" type="submit" value="Upload">
	</form>


<h2>List of Videos</h2>
EOD;
	
	$sql = "SELECT * FROM videos ORDER BY rating DESC";
	$retval=mysql_query($sql,$conn);
	if(!$retval){
		die('Could not get data: '.mysql_error());
	}
	while($row=mysql_fetch_array($retval,MYSQL_ASSOC)){
			$file_rating = $row['rating'];
			$fname = $row['name'];
			$furl = "http://vaibhavjagannathanscloudassignment.s3.amazonaws.com/".$fname;
			print("$fname &nbsp &nbsp &nbsp Rating: {$file_rating} <br> 
			<a href=\"{$furl}\">Download Video</a> &nbsp &nbsp 
			<a href=\"/index.php?delete={$fname}\">Delete</a> &nbsp &nbsp 
			<a href=\"/play.php?{$fname}&st&{$file_rating}\">Play (Flash)</a>  &nbsp &nbsp 
			<a href=\"/play.php?{$fname}&dl\">Play (HTML5)</a>  &nbsp &nbsp 
			<br>");
		}
?>
</body>
</html>
