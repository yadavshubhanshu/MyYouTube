<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript" src="/jwplayer/jwplayer.js"></script>
  <title>My Youtube</title>
  <link href="Site.css" rel="stylesheet">
</head>

<body>

<div id="myElement">Loading the player...</div>
<script type="text/javascript">
		var querystring = window.location.search.substring(1).split('&');
		filename = querystring[0];
		mode = querystring[1];
		current_rating = querystring[2];
		if(mode=="st")
			{
			 jwplayer("myElement").setup({file: "rtmp://sv6fcixw0xfin.cloudfront.net/cfx/st/"+filename},primary='flash');
			}
		else if(mode=="dl")
			{
		 	 jwplayer("myElement").setup({file: "http://dp8m7v6byl5li.cloudfront.net/"+filename});
			}
		document.write("<br>");
		document.write(filename+"   Rating: "+current_rating);
		document.write("<br>");
		document.write('<form action="index.php" method="post" name="formrate" id="formrate">'
					      +'<select name="newRating">'
						  +'<option value="1">1</option>'
						  +'<option value="2">2</option>'
						  +'<option value="3">3</option>'
						  +'<option value="4">4</option>'
						  +'<option value="5">5</option>'
						  +'</select>'
						  +'<input name="currentFile" type="hidden" value="'+filename+'">'
						  +'<input name="oldRating" type="hidden" value="'+current_rating+'">'
					      +'<input name="Rate" type="submit" value="Rate">'
						+'</form>');
</script>

<a href="/index.php">Back</a>

</body>
</html>