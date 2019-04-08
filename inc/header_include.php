<title> <?php echo SITENAME; ?> </title>
<link href="<?php echo BASEURL; ?>dashboard/assets/bootstrap.min.css" rel="stylesheet">

<link href="<?php echo BASEURL; ?>inc/vb_styles.css" rel="stylesheet" type="text/css" media="screen">
<link href="<?php echo BASEURL; ?>inc/onprint.css" rel="stylesheet" type="text/css" media="print">
<link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico" />
<script src="<?php echo BASEURL; ?>dashboard/assets/js/jquery-1.12.4.min.js"></script>
		<script src="<?php echo BASEURL; ?>dashboard/assets/js/bootstrap.min.js"></script>

<script src="<?php echo BASEURL; ?>SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="<?php echo BASEURL; ?>SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
<script src="<?php echo BASEURL; ?>SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="<?php echo BASEURL; ?>SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
<script src="<?php echo BASEURL; ?>SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="<?php echo BASEURL; ?>SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css">

<script language="javascript">
function openPopWind(URI,Wdth,Hght){
	var features = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=yes, width="+Wdth+", height="+Hght;
	window.open(URI,"newPop",features);
}

function showHide(rowID,divID){
	var y = document.getElementById(divID);
	var x = document.getElementById(rowID);
	if(x.style.display == "none") {x.style.display = ""; y.className = "divHeader";}
	else {x.style.display = "none"; y.className = "divHeader2";}
}
function showHideField(fieldID,dropID){
	var x2 = document.getElementById(fieldID);
	var y2 = document.getElementById(dropID);
	if(y2.options[y2.selectedIndex].value == "other"){
	x2.style.visibility = "visible";
	x2.focus();
	}
	else x2.style.visibility = "hidden";
}


function sendListItems(listID,inpID){
	var x = document.getElementById(listID);
	var y = document.getElementById(inpID);
	for(var i = 0 ; i < x.length ; i++){
		if(x.options[i].selected == true){
		if(y.value == "") y.value +=  x.options[i].value;
		else y.value += "#"+x.options[i].value;
		} 
	}
}


function  carryOut(whatWanted,record){
	var x = document.getElementById("actionName");
	var y = document.getElementById("recordsForm");
	var z = document.getElementById("recordNo");
	if(whatWanted == "delete"){
		var conf = confirm("Are you sure that you want to delete this!");
		if(conf){
			x.value = whatWanted;
			z.value = record;
			y.submit();
		}
	}
	else{
		x.value = whatWanted;
		z.value = record;
		y.submit();
	}
}

function confirmAct(mesg,specForm,action){
	var y = document.getElementById(specForm);
	var conf = confirm(mesg);
	if(conf){
		y.innerHTML += "<input type=\"submit\" name=\""+action+"\" id=\""+action+"\" value=\" Do The Action \" style=\"display:none\" />";
		document.getElementById(action).click();
		//y.submit();
	}
}

function selectRow(rowID){
		var x = document.getElementById(rowID);
		x.checked="checked";
	}
	
	function CallPrint(strid,title)
	{
		var prtContent = document.getElementById(strid);
		var WinPrint =
		window.open('','','left=0,top=0,width=1,height=1,t oolbar=0,scrollbars=0,status=0');
		WinPrint.document.write("<h3>"+title+"</h3>");
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
		prtContent.innerHTML=strOldOne;
	}
	
function chngpos(pageno,url,formname){
	var sx = document.getElementById(formname);
	sx.action=url+"?page="+pageno;
	sx.submit();
}
function showHideTabs(showTabId,hideTabId){
	var x1 = document.getElementById(showTabId+"_tab");
	var x2 = document.getElementById(showTabId+"_tb");
	var y1 = document.getElementById(hideTabId+"_tab");
	var y2 = document.getElementById(hideTabId+"_tb");
	if(x1.className == "tab_inact" && x2.style.display == "none"){
		x1.className = "tab_active"; x2.style.display = "block";
		y1.className = "tab_inact"; y2.style.display = "none";
	}
}

function chckbox(chckid,txtInput){
	var selChck = document.getElementById(chckid);
	var chcktext = document.getElementById(txtInput);
	var str = selChck.value;
	if(selChck.checked == true){
		 chcktext.value += "#"+str;
	}
	else{
		chcktext.value = chcktext.value.replace("#"+str,"");
	}
}

//Function to check that total number desn't exceed
// Created by Abdel-Kawy Abdel-Hady
function chckTotal(current,othrInputs,alertContainer){
	var elemArr = new Array();
	elemArr = othrInputs.split("#");
	var allValues = 0;
	for(var i=0 ; i<elemArr.length ; i++){
		allValues = allValues + parseInt(document.getElementById(elemArr[i]).value);
	}
	var curInput = document.getElementById(current);
	var total = parseInt(document.getElementById("totHrs").value);
	var subTot = allValues+parseInt(curInput.value);
	if(subTot > total){
		document.getElementById(alertContainer).innerHTML = '<div id="popDiv" class="errorDiv"><div style="text-align:right"><a href="javascript:closeAd(\'popDiv\')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Total Number of Credit Hours ('+total+') is Exceeded!<!-- Message End --></div>';
		adCount=0;adTime=10;showAd();
		curInput.value = 0;
	}
	else document.getElementById(alertContainer).innerHTML = '';
}

// Function to add item to textarea and ul list
// Created by Abdel-Kawy Abdel-Hady
function addItem(txtInput,numInput,txtArea,parentUl){
	var xInput = document.getElementById(txtInput);
	var strInput = "";
	if(numInput != "_"){
		strNum = new Array();
		strNum = numInput.split("#");
		var numStr = " {"+document.getElementById(strNum[0]).value+" "+strNum[1]+"}";
	} else var numStr = "";
	var inpuTexts = new Array();
	inpuTexts = xInput.value.split(" ");
	for(var j=0 ; j<inpuTexts.length ; j++){
		if(inpuTexts[j].search(/\S/) != -1){
			inpuTexts[j] = inpuTexts[j].slice(inpuTexts[j].search(/\S/));
			strInput +=" "+inpuTexts[j];
		}
		else continue;
	}
	var xArea = document.getElementById(txtArea);
	var xUl = document.getElementById(parentUl);
	var prevInp = new Array();
	var exist = 'no';
	prevInp = xArea.innerHTML.split("#");
	for(var i=1 ; i<prevInp.length ; i++){
		if(prevInp[i] == strInput){ exist = 'yes'; break;}
		else continue;	
	}
	if(exist != 'yes' && strInput.search(/\S/) != -1){
		finalStr = strInput.slice(strInput.search(/\S/))+numStr;
		xArea.innerHTML +="#"+finalStr;
		var d = new Date();
		var specId = d.getTime();
		xUl.innerHTML += "<li id=\"li_"+specId+"\"><pre>"+finalStr+"&nbsp;&nbsp;<a href=\"javascript:remItem('"+txtArea+"','"+parentUl+"','"+specId+"','"+finalStr+"')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>";
		xInput.value = '';
		xInput.style.border = '1px solid #BBB';
	}
	else xInput.style.border = '1px solid #F00';
}

// Function to remove item from textarea and ul list
// Created by Abdel-Kawy Abdel-Hady
function remItem(txtArea,parentUl,remObj,remObjTxt){
	var xArea = document.getElementById(txtArea);
	var xUl = document.getElementById(parentUl);
	xArea.innerHTML = xArea.innerHTML.replace("#"+remObjTxt,"");
	var xli = document.getElementById("li_"+remObj);
 	xUl.removeChild(xli);
}


/******************************************
* DHTML Ad Box (By Matt Gabbert at http://www.nolag.com)
* Visit http://www.dynamicdrive.com/ for full script
* This notice must stay intact for use
******************************************/

var ns=(document.layers);
var ie=(document.all);
var w3=(document.getElementById && !ie);
var calunit=ns? "" : "px";


function showAd(){

	if(ie)		adDiv=document.all.popDiv;
	else if(ns)	adDiv=document.layers["popDiv"];
	else if(w3)	adDiv=document.getElementById("popDiv");
	if(adCount<adTime*10){
		adCount+=1;
		if (ie){
			documentWidth  =truebody().offsetWidth/2+truebody().scrollLeft-20;
			documentHeight =truebody().offsetHeight/2+truebody().scrollTop-20;
		}	
		else if (ns){
			documentWidth=window.innerWidth/2+window.pageXOffset-20;
			documentHeight=window.innerHeight/2+window.pageYOffset-20;
		} 
		else if (w3){
			documentWidth=self.innerWidth/2+window.pageXOffset-20;
			documentHeight=self.innerHeight/2+window.pageYOffset-20;
		} 
		if (ie||w3) {adDiv.style.left=documentWidth-300+calunit; adDiv.style.top =documentHeight-100+calunit;} 
		else {adDiv.left=documentWidth-300+calunit; adDiv.top =documentHeight-100+calunit;}
		setTimeout("showAd()",100);
	}else{
		if (ie||w3)
		adDiv.parentNode.removeChild(adDiv);
		//adDiv.style.display="none";
		else
		//adDiv.visibility ="hide";
		adDiv.parentNode.removeChild(adDiv);
		}
}
function closeAd(divv){
	if(ie)		closedDiv=eval('document.all.'+divv);
	else if(ns)	closedDiv=eval('document.layers["'+divv+'"]');
	else if(w3)	closedDiv=eval('document.getElementById("'+divv+'")');
	if (ie||w3)
	closedDiv.parentNode.removeChild(closedDiv);
	//adDiv.style.display="none";
	else
	//adDiv.visibility ="hide";
	closedDiv.parentNode.removeChild(closedDiv);
}

function truebody(){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}
</script>