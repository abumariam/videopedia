<?php

session_start();

require_once '../../config.php';
if ( empty( $_SESSION[ 'person' ] ) )header( "Location:" . BASEURL );

$crsid = $_REQUEST[ 'crsid' ];
db_connect();
$crsName_r = mysqli_fetch_row( mysqli_query( $db, "SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $crsid" ) );
db_disconnect();


if ( !file_exists( 'vendor/autoload.php' ) ) {
	throw new\ Exception( 'please run "composer require google/apiclient:~2.0" in "' . __DIR__ . '"' );
}

require_once 'vendor/autoload.php';

?>

<!doctype html>
<html>

<head>

	<?php require(INC_DIR."header_include.php"); ?>
	<script type="application/javascript">
		function spoil( id ) {
			var divid = document.getElementById( id );
			divid.style.display = 'block';
			divid.scrollIntoView( true );
			return false;
		}
	</script>
</head>

<body>

	<center>
		<?php require(INC_DIR."banner.php");?>
	</center>
	
	<div id="titleBar">
		<div id="titleText">Search &amp; Add Videos | </div>
		<input type="button" name="cancel" id="cancel" class="btCancel" value="Finish    " onClick="self.location='../videos.php?crsid=<?php echo $crsid; ?>'"/>
		<br/>
	</div>
	<h3>CRS Title: <?php echo $crsName_r[0]; ?></h3>
	<br clear="all"/>
	<?php

	if ( isset( $_POST[ 'save' ] ) ) {
		
		$idname = "funparam-" . $_POST[ 'whichvideo' ];
		$params = explode( "##", $_POST[ $idname ] );
		$vidId = $_POST[ 'whichvideo' ];
		$videoTitle = $params[ 0 ];
		$description = $params[ 1 ];
		$pubDate = $params[ 2 ];
		$authorUsername = $params[ 3 ];
		$crsId = $_POST[ 'courseid' ];

		$videoUrl = "https://www.youtube.com/embed/" . $vidId;
		$videoUploaded = new DateTime( $pubDate );
		$videoUploaded = $videoUploaded->format( 'Y-m-d H:i:s' );
		db_connect();
		
		$foundAuthor = mysqli_num_rows( mysqli_query( $db, "SELECT * FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `author` LIKE '$authorUsername'" ) );
		if ( $foundAuthor != 0 ) {
			$lastOrder = mysqli_fetch_row( mysqli_query( $db, "SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `author` LIKE '$authorUsername' AND `pubdate` < '$videoUploaded'" ) );
			$vidOrder = $lastOrder[ 0 ] + 1;
		} else {
			$lastOrder = mysqli_fetch_row( mysqli_query( $db, "SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId" ) );
			$vidOrder = $lastOrder[ 0 ] + 1;
		}

		$insBefore = mysqli_num_rows( mysqli_query( $db, "SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidurl` LIKE '$videoUrl'" ) );
		if ( $insBefore == 0 ) {
			if ( $foundAuthor != 0 ) {
				mysqli_query( $db, "UPDATE `vb_videos` SET `vidorder` = `vidorder`+1 WHERE `crsid_fk` = $crsId AND `vidorder` >= '$vidOrder';" );
			}
			$insertNewLine = mysqli_query( $db, "INSERT INTO `vb_videos` (`crsid_fk`, `vidtitle`, `vidurl`, `vidorder`, `modify_date`, `author`, `desc`, `lastupdate`, `duration`, `pubdate`) VALUES ('$crsId', '$videoTitle', '$videoUrl', '$vidOrder', '" . date( "Y-m-d H:i:s" ) . "', '$authorUsername', '$description', '', '', '$videoUploaded');" );

			if ( $insertNewLine )echo '<div id="popDiv" class="addSucc"><div style="text-align:right"><a href="javascript:closeAd(\'popDiv\')"><img src="../../images/close.gif" border=0 /></a></div><br />Adding choosed video to course is done successfully!.</div>';
		} else {
			echo '<div id="popDiv" class="addFail"><div style="text-align:right"><a href="javascript:closeAd(\'popDiv\')"><img src="../../images/close.gif" border=0 /></a></div><br />Adding choosed video to course is Failed!, it was added before.</div>';
		}
		db_disconnect();
	}

	$htmlBody = "";

// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
if ( isset( $_POST[ 'q' ] ) && isset( $_POST[ 'maxResults' ] ) ) {
	/*
	 * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
	 * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
	 * Please ensure that you have enabled the YouTube Data API for your project.
	 */
	$DEVELOPER_KEY = 'AIzaSyCVofeHsb9K_GQcqb_NysnPgzerw7_xe0U';

	$client = new Google_Client();
	$client->setDeveloperKey( $DEVELOPER_KEY );

	// Define an object that will be used to make all API requests.
	$youtube = new Google_Service_YouTube( $client );

	$htmlBody = '';
	if ( isset( $_POST[ 'nextPage' ] ) )$token = $_POST[ 'next' ];
	else if ( isset( $_POST[ 'prevPage' ] ) )$token = $_POST[ 'prev' ];
	else $token = "";

	try {


		// Call the search.list method to retrieve results matching the specified
		// query term.
		$searchResponse = $youtube->search->listSearch( 'id,snippet', array(
			'q' => $_POST[ 'q' ],
			'maxResults' => $_POST[ 'maxResults' ], 'order' => 'relevance', 'type' => 'video', 'videoDuration' => 'medium', 'videoEmbeddable' => 'true', 'pageToken' => $token
		) );



		$videos = '';
		$channels = '';
		$playlists = '';

		// Add each result to the appropriate list, and then display the lists of
		// matching videos, channels, and playlists.
		foreach ( $searchResponse[ 'items' ] as $searchResult ) {
			switch ( $searchResult[ 'id' ][ 'kind' ] ) {
				case 'youtube#video':
					$videos .= sprintf( '<tr><td> <h3>%s</h3> <img src="%s" style="margin-right:5px" align="left" > Date: %s<br> Description: %s<br> <br><input type="button" value="   Show  " onclick="document.getElementById(\'videoplayer\').src = \'https://www.youtube.com/embed/%s\'; spoil(\'videoPlayer\'); document.getElementById(\'whichvideo\').value = \'%s\'" class="butStyle"> <input type="hidden" name="funparam-%s" value="%s##%s##%s##%s"></td></tr>',
						$searchResult[ 'snippet' ][ 'title' ], $searchResult[ 'snippet' ][ 'thumbnails' ][ 'default' ][ 'url' ], $searchResult[ 'snippet' ][ 'publishedAt' ], $searchResult[ 'snippet' ][ 'description' ], $searchResult[ 'id' ][ 'videoId' ], $searchResult[ 'id' ][ 'videoId' ], $searchResult[ 'id' ][ 'videoId' ], $searchResult[ 'snippet' ][ 'title' ], $searchResult[ 'snippet' ][ 'description' ], $searchResult[ 'snippet' ][ 'publishedAt' ], $searchResult[ 'snippet' ][ 'channelId' ] );
					break;
				case 'youtube#channel':
					$channels .= sprintf( '<li>%s (%s)</li>',
						$searchResult[ 'snippet' ][ 'title' ], $searchResult[ 'id' ][ 'channelId' ] );
					break;
				case 'youtube#playlist':
					$playlists .= sprintf( '<li>%s (%s)</li>',
						$searchResult[ 'snippet' ][ 'title' ], $searchResult[ 'id' ][ 'playlistId' ] );
					break;
			}
		}
		$tot = $searchResponse[ 'pageInfo' ][ 'totalResults' ];
		$next = $searchResponse[ 'nextPageToken' ];
		$prev = $searchResponse[ 'prevPageToken' ];

		$htmlBody .= <<<END
	<p>Total No. of search results: $tot</p>
	<div style="text-align:right">
      	<input type="submit" value="   Previous   " id="prevPage" name="prevPage" class="butStyle"> | <input type="submit" value="   Next   " id="nextPage" name="nextPage" class="butStyle">
	</div>
    <table width="100%" cellpadding="5" cellspacing="5">$videos</table><br>
	
	<input type="hidden" value="$next" id="next" name="next"><input type="hidden" value="$prev" id="prev" name="prev">
END;
	} catch ( Google_Service_Exception $e ) {
		$htmlBody .= sprintf( '<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars( $e->getMessage() ) );
	} catch ( Google_Exception $e ) {
		$htmlBody .= sprintf( '<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars( $e->getMessage() ) );
	}
}
	?>
	<form id="searchForm" method="POST">
		<div id="mainSearchBox">
			Search Keyword: <input type="search" id="q" name="q" placeholder="Enter Search Keyword" value="<?php if(isset($_POST['q'])) echo $_POST['q']; ?>" size="40" style="font-size:16px; height:25px" required> | Results/Page: <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" value="<?php if(isset($_POST['maxResults'])) echo $_POST['maxResults']; else echo '5'; ?>"> | <input type="submit" value="  Search  " class="butStyle">
			<input id="courseid" name="courseid" type="hidden" value="<?php echo $crsid; ?>">
		</div>
		<br clear="all"/>
		<div id="searchResults">
			<table width="100%" border="0" cellspacing="0" cellpadding="5" style="display: <?php if(isset($_POST['q'])) echo " table "; else echo "none "; ?>">
				<tbody>
					<tr>
						<td width="50%" align="left" valign="top">
							<div id="searchResultsListColumn">
								<h2>Videos List</h2>
								<?=$htmlBody?><br>
							</div>
							<div id="searchResultsVideoColumn">
								<div id="videoPlayer">
									<h2>Video Player</h2>
									<p>Just click on 'Show' button under each video description to play that video here</p>
									<iframe id="videoplayer" width="560" height="315" src="" frameborder="1" allowfullscreen></iframe>
									<br><br>
									<input type="hidden" id="whichvideo" name="whichvideo" value="">
									<input type="submit" id="save" name="save" value="   Add this video to the course   " class="butStyle">
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div><br clear="all"/>
	</form>
	
	<p></p>
	<?php require(INC_DIR."footer.php");?>
</body>

</html>