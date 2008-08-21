//<![CDATA[
var XMLHttp = { 
    _objPool: [], 
  
    _getInstance: function (){ 
        for (var i = 0; i < this._objPool.length; i ++){ 
            if (this._objPool[i].readyState == 0 || this._objPool[i].readyState == 4){ 
                return this._objPool[i]; 
            } 
        } 
        this._objPool[this._objPool.length] = this._createObj(); 
        return this._objPool[this._objPool.length - 1]; 
    }, 
  
    _createObj: function (){ 
        if (window.XMLHttpRequest){ 
            var objXMLHttp = new XMLHttpRequest(); 
        }else{ 
            var MSXML = ['MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP']; 
            for(var n=0; n<MSXML.length; n++){ 
                try{ 
                    var objXMLHttp = new ActiveXObject(MSXML[n]); 
                    break; 
                }catch(e){} 
            } 
        }          
        if (objXMLHttp.readyState == null){ 
            objXMLHttp.readyState = 0; 
            objXMLHttp.addEventListener("load", function (){ 
                objXMLHttp.readyState = 4; 
                if(typeof objXMLHttp.onreadystatechange == "function"){objXMLHttp.onreadystatechange();} 
            },  false); 
        } 
        return objXMLHttp; 
    }, 
  
    sendReq: function (method, url, data, callback){ 
        var objXMLHttp = this._getInstance(); 
        with(objXMLHttp){ 
            try{ 
                if (url.indexOf("?") > 0){url += "&randnum=" + Math.random();} 
                else{url += "?randnum=" + Math.random();} 
                open(method, url, true);
                setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                send(data);
                onreadystatechange = function (){ 
                    if (objXMLHttp.readyState == 4 && (objXMLHttp.status == 200 || objXMLHttp.status == 304)){ 
                        callback(objXMLHttp); 
                    }
                } 
            }catch(e){alert(e);} 
        } 
    } 
};

//
String.prototype.toQuery = function() { return encodeURIComponent(this); }

function $(id) { return document.getElementById(id); }

function startQuery() {
	if ($('url').value == "") {
		alert('Please Enter URL');
		$('url').focus();
		return false;
	}

	if (!/^http:\/\/mymedia\.yam\.com\/m\/([0-9]+)$/.test($('url').value)) {
		alert('URL Format Invalid');
		$('url').focus();
		return false;
	}
	
	$('infoForm').style.display = 'block';
	$('submitButton').disabled = true;
	$('infoForm').innerHTML = '<div align="center">Loading...</div>';

	queryString = "url="+$('url').value.toQuery();
	XMLHttp.sendReq('POST', '/get-mp3', queryString, function(obj) {
		$('submitButton').disabled = false;
		displayInfo(obj)
	});
}

function displayInfo(obj) {
	$('infoForm').style.display = 'block';

	if (obj.responseText == 'Error') {
		$('infoForm').innerHTML = '<span style="color:red">Error!!Can Not Found Real URL</span>';
	}else{
		$('infoForm').innerHTML = 'Download : <a href="'+obj.responseText+'">Click Here</a>';
	}

	$('loading').style.display = 'block';
	$('loading').innerHTML = 'Renew List....';
	$('dataForm').style.display = 'none';

	setTimeout("getMP3List();", 1000);
}

function displayMP3List(obj) {
	obj = eval("obj=" + obj.responseText);
	len = obj.myData.length;
	temp = '';

	for (var i=0; i<len; i++) {
		x = obj.myData[i];
		temp += '<ul>';
		temp += '	<li><a href="' + x.fake_url + '" target="' + x.fake_ids + '">' + x.fake_ids + '</a></li>';
		temp += '	<li><a href="' + x.real_url + '">[Download]</a></li>';
		temp += '	<li>' + x.date.ftune + '</li>';
		temp += '</ul>';
	}

	$('loading').style.display = 'none';
	$('dataForm').style.display = 'block';
	$('submitSearch').disabled = false;

	$('dataForm').innerHTML = temp;

	obj = temp = len = null;
}

function getMP3List() {
	XMLHttp.sendReq('POST', '/get-mp3-list', "", function(obj) {
		$('submitSearch').disabled = true;
		displayMP3List(obj)
	});
}

function startSearch() {
	if ($('keyword').value == '') {
		alert('Please Error Keyword (Page Id)');
		$('keyword').focus();
		return false;
	}

	if (!/\d+/.test($('keyword').value)) {
		alert('Please Enter A Integer Number');
		$('keyword').focus();
		return false;
	}

	XMLHttp.sendReq('POST', '/searh-mp3-id', "keyword="+$('keyword').value, function(obj) {
		$('submitSearch').disabled = true;
		displayMP3List(obj)
	});
}

window.onload = function() {
	setTimeout("getMP3List()", 2000)
}
//]]>
