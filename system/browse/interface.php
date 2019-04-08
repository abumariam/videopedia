<!---
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
-->
<?php 

$crsid = $_REQUEST['crsid']; 
db_connect();
$crsName_r = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $crsid"));
db_disconnect();
?>
<html>
<head>

<?php require(INC_DIR."header_include.php"); ?>
<script src="video_browser.js" type="text/javascript"></script>


</head>
<body>
<center><?php require(INC_DIR."banner.php");?></center>
<div id="main" style="margin:0px 7px">
  <div id="titleBar">
    <div id="titleText">Search &amp; Add Videos | </div>
    <div id="searchBox" style="display: none;">
      <form id="searchForm" onSubmit="ytvbp.listVideos(this.queryType.value, this.searchTerm.value, 1); return false;">
        
        <input name="queryType" type="hidden" value="all">
        <input name="searchTerm" type="text" value="" size="40" style="font-size:16px; height:25px">
        <input id="courseid" name="courseid" type="hidden" value="<?php echo $crsid; ?>">
        <input type="submit" value=" Search " class="butStyle">
      </form>
    </div>
    <input type="button" name="cancel" id="cancel" class="btCancel" value="Finish    " onClick="self.location='../videos.php?crsid=<?php echo $crsid; ?>'" />
    <br />
  </div>
  <h3>CRS Title: <?php echo $crsName_r[0]; ?></h3>
  <br clear="all" />
  <div id="mainSearchBox">
    <h3>Search Keyword:</h3>
    <form id="mainSearchForm" onSubmit="ytvbp.listVideos(this.queryType.value, this.searchTerm.value, 1); document.forms.searchForm.searchTerm.value=this.searchTerm.value; ytvbp.hideMainSearch(); document.forms.searchForm.queryType.selectedIndex=this.queryType.selectedIndex; return false;">
      
      <input name="queryType" type="hidden" value="all">
      <input name="searchTerm" type="text" value="" size="40" style="font-size:16px; height:25px">
      <input id="courseid" name="courseid" type="hidden" value="<?php echo $crsid; ?>">
      <input type="submit" value=" Search " class="butStyle">
    </form>
  </div>
  <br clear="all" />
  <div id="searchResults">
  <div id="addVideoDiv" style="width:50%"></div>
    <div id="searchResultsListColumn">
    <br clear="all" />
      <div id="searchResultsVideoList"></div><br>
      <div id="searchResultsNavigation" style="text-align:center; padding-bottom:20px">
        <form id="navigationForm">
          
          <input type="button" id="previousPageButton" onClick="ytvbp.listVideos(ytvbp.previousQueryType, ytvbp.previousSearchTerm, ytvbp.previousPage);" value="  Back  " class="butStyle" style="display:none"></input>
          <input type="button" id="nextPageButton" onClick="ytvbp.listVideos(ytvbp.previousQueryType, ytvbp.previousSearchTerm, ytvbp.nextPage);" value="  Next  " class="butStyle" style="display:none"></input>
        </form>
      </div>
    </div>
    <div id="searchResultsVideoColumn">
      <div id="videoPlayer"></div>
    </div> 
  </div>
</div><br clear="all" />
<?php require(INC_DIR."footer.php");?>
</body>
</html>
