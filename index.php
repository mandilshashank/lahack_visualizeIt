<html>
<head>
	<title>Visualize It</title>
</head>

<script src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1","packages":["corechart","table"]}]}'></script>	
<script type='text/javascript'>
	var xmlDoc;
    function visualize_file	()
    {
        //First we need to detect the extension type of file .xml,.json,.csv
        var filename = document.getElementById('inp_file');
        var ext = filename.value.split('.').pop();

        //Check if the the file extension is in the supported format
        if(ext!='xml')
        {
              alert('File of this extension is not supported');
              filename.value = "";
        }
    }

    function check_file_present()
    {
        var filename = document.getElementById('inp_file');
        if(filename.value==''){alert("Please select a file of supported format(xml)");return false;}
        return true;
    }
	
	function createChartFromXml()
	{
		var checkObj = new Array();
		var check = document.getElementsByTagName('input');
		j=0
		for(i=0;i<check.length;i++)
		{
			if(check[i].type=="checkbox")
			{
				if(check[i].checked)
				{
					checkObj[j] = check[i].name;
					j++;
				}
			}
		}
		
		var chartType="Line";
		for(i=0;i<check.length;i++)
		{
			if(check[i].type=="radio")
			{
				if(check[i].checked)
				{
					chartType=check[i].id;
				}
			}
		}
		
		if(chartType=='Pie' && checkObj.length>1)
		{
			alert("Please only check one attribute to see its pie distribution");
			return;
		}
			
		array = "[[";
		for(i=0;i<j;i++)
		{
			array+="'"+checkObj[i]+"'";
			if(chartType=='Pie')
			{
				array+=",'value'";
			}
			if(i==j-1)
			{array+="]";}
			else
			{array+=",";}
		}
		
		len=j;
		
		for(i=0;i<xmlDoc.childNodes.length;i++)
		{
			if(xmlDoc.childNodes.item(i).nodeName=='#text'){continue;}
			//alert(xmlDoc.childNodes.item(i).nodeName);			
			count=0;
			for(j=0;j<xmlDoc.childNodes.item(i).childNodes.length;j++)
			{
				if(xmlDoc.childNodes.item(i).childNodes.item(j).nodeName=='#text'){continue;}
				array+=",[";
				//alert(xmlDoc.childNodes.item(i).childNodes.item(j).nodeName);
				current=0;
				for(k=0;k<xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.length;k++)
				{
					if(xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).nodeName==checkObj[current])
					{
						if(chartType=='Pie')
							{array+="'Instance"+count+"',";}
						array+=xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).textContent;
						if(current==len-1){array+="]"}
						else{array+=","}
						count+=1;
						current+=1;
					}
				}
			}
		}
		array+="]";
		
		drawLineChart(array,chartType);
	}
	
	function drawLineChart(dataArray,chartType)
	{
		var data1 = eval(dataArray);
		var data = google.visualization.arrayToDataTable(eval(dataArray))

		var options
		var chart
		if(chartType=="Line")
		{
			options = {title: 'Line Chart'};
			chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		}
		else if(chartType=="Pie")	
		{
			options = {title: 'Pie Chart'};
			chart = new google.visualization.PieChart(document.getElementById('chart_div'));
		}
		else
		{
			options = {title: 'Bar Chart'};
			chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		}
		
		chart.draw(data, options);
	}
        
	
	function ParseXml()
	{
		html ="";
		html = "<form id='create_chart' name='create_chart'><table><tr><td>Line Chart : <input type='radio' checked name='ChartType' id='Line'></td></tr><tr><td>Bar Chart  : <input type='radio' name='ChartType' id='Bar'></td></tr><tr><td>Pie Chart  : <input type='radio' name='ChartType' id='Pie'></td></tr><tr><td>&nbsp;</tr></td>"
		for(i=0;i<xmlDoc.childNodes.length;i++)
		{
			if(xmlDoc.childNodes.item(i).nodeName=='#text'){continue;}
			//alert(xmlDoc.childNodes.item(i).nodeName);			
			for(j=0;j<xmlDoc.childNodes.item(i).childNodes.length;j++)
			{
				if(xmlDoc.childNodes.item(i).childNodes.item(j).nodeName=='#text'){continue;}
				//alert(xmlDoc.childNodes.item(i).childNodes.item(j).nodeName);
				for(k=0;k<xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.length;k++)
				{
					if(xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).nodeName=='#text'){continue;}
					html+="<tr><td>"+xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).nodeName+" : <input type='checkbox' id='"+xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).nodeName+"' name='"+xmlDoc.childNodes.item(i).childNodes.item(j).childNodes.item(k).nodeName+"'/></tr></td>"
				}
				break;
			}
		}
		html+="<tr><td><input type='button' id='submit_chart' name='submit_chart' value='Create Chart' onclick='createChartFromXml();'/></td></tr></table></form>" 
		
		document.getElementById("graphInputs").innerHTML = html;
	}
	
    function getXML(filename)
    {
        var xhr;
		
        if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
			
        xhr.onreadystatechange = function()
		{
			if(xhr.readyState==4 && xhr.status==200)
			{
				//XML recieved here. //Parse XML
				xmlDoc=xhr.responseXML
				ParseXml();
			}
		};
        xhr.open("GET",filename); //assuming kgr.bss is plaintext
        xhr.send();
    }
	
    function getFileAndRead(filename)
    {
          //First we need to detect the extension type of file .xml,.json,.csv
		  //alert(filename);
          var ext = filename.split('.').pop();
		  //alert(ext);
          //Check if the the file extension is in the supported format
          if(ext!='xml')
          {
                alert('File of this extension is not supported');
                filename.value = "";
          }
          else if(ext=='xml')
          {
                getXML(filename);
          }
    }
</script>

<body style="background-image:url('sch1.jpg')">
	<form id='data_form' name='data_form' method='post' action='upload.php' enctype="multipart/form-data">
		<div id="HeadTitle" align="center">
			<h1 align='center'>Visualize It</h1>
		</div>
	
        <div id='input_datafile' align='left'>
        <table align='center' style='border:0 ;border-color:FF4500'>
            <tr><td style="text-align: center"><input type='text' hidden="true" id='flag' value='0'></td></tr>
            <tr><td style="text-align: center"><input type='file' id='inp_file' name='inp_file' onchange="visualize_file();"/></td></tr>
            <tr><td style="text-align: center"><input id='submit_form' name='submit_form' type="submit" onclick='return check_file_present();'/></td></tr>
        </table>
        </div>
		
		<div>&nbsp;</div>
		
		<center>
		<div id='graphInputs' name='graphInputs' align="left" style='border:solid;width:200px;display:inline-block;border-color:FF4500'>
				
		</div>
		
		<div id="chart_div" name="chart_div" align="left" style="border:solid;width:auto;display:inline-block;border-color:FF4500">
		
		</div>
		</center>
	</form>
</body>

<?php
	if(isset($_GET['uploaded']))
    {
           //Now get the file in javascript using ajax call and read xml file
           echo "<script>getFileAndRead('".$_GET['uploaded']."');</script>";
    }
    ?>
</html>