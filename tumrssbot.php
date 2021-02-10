<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
<input type="text" name="url" placeholder="">
<button type="submit">Verileri Gör</button>
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if (!empty($_POST['url'])) {
		$veriurl = $_POST['url'];
 	 } else {
 	 	$error .= "<br>Verilerin çekileceği url adresi girin.";
 	}
	
	$success .= 'Veriler güncelleniyor...';

}
?>
<?php
if (!empty($error)) {
echo '<p class="text-danger"><strong>Hata(lar):</strong><br/>' . $error . '</p>';
} elseif (!empty($success)) {
echo '




';
}
?>
<?php

function feedMe($feed) {
  // Use cURL to fetch text
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $feed);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $rss = curl_exec($ch);
  curl_close($ch);

  $rss = str_replace("<content:encoded>","<contentEncoded>",$rss);
  $rss = str_replace("</content:encoded>","</contentEncoded>",$rss);
  $rss = simplexml_load_string($rss);

  $siteTitle = $rss->channel->title;


  $cnt = count($rss->channel->item);

  for($i=0; $i<$cnt; $i++) {
    $url = $rss->channel->item[$i]->link;
    $title = $rss->channel->item[$i]->title;
    $desc = $rss->channel->item[$i]->description;
	$mak = $rss->channel->item[$i]->contentEncoded;

echo "<br><br><br>------------------------------------------------------------";
    echo $title;
	echo "<br><br><br>";
    echo $mak;
  }
}

feedMe($veriurl);

?>