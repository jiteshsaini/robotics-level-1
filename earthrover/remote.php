<!--
Project: Earthrover
Created by: Jitesh Saini
-->
<html>
<head>        
   <title>Remote</title>  
   <link href="css/remote.css" rel="stylesheet" type="text/css">  
   <script src="js/jquery.min.js"></script>  
   <script src="js/earthrover.js"></script>              
</head> 

<body onload="set_speed(50)">
<?php
include_once 'vars.php';

gpio_initialise(); // initialising the GPIO pins

?>
<div align="center" id='box_outer'>
	<!-- =================Direction Buttons=================================================== -->
	<div class='box_row'>
		<input  class="button"  type="submit" onclick="button_direction('f');" value="FWD"/>
	</div>
	<br />
	<div class='box_row'>
		<input class="button" style="float:left" type="submit" onclick="button_direction('l');" value="LEFT"/>
		<input class="button" type="submit" onclick="button_direction('s');" value="STOP"/>
		<input  class="button" style="float:right" type="submit" onclick="button_direction('r');" value="RIGHT"/>
	</div>
	<br />
	<div class='box_row'>
			<input  class="button" type="submit" onclick="button_direction('b');" value="BACK"/>
	</div>
	<!-- ================================================================================= -->
	
	<br />
	<!-- =================Speed Buttons=================================================== -->
	
	<div class='box_row'>
		<div class='box_1'>
			<txt>Speed</txt>
			<input class="button_1" style="float:left" type="submit" onclick="button_speed('-');" value="-"/>
			<b id= 'disp_speed' style='color:blue'>50</b>					
			<input  class="button_1" style="float:right" type="submit" onclick="button_speed('+');" value="+"/>
		</div>
		<div style='float:right' class='box_1'>
			<input id= 'txt_send' style='width:40%;font-size:20px' type='text' name='t2'>			
			<input id="cmd_send" type="submit" value="Set Speed Value" onclick="button_send();"/>
		</div>
	</div>
	<!-- ================================================================================== -->
	
</div>

<!-- ====================================================================================== -->
<div >
		<div class='box_1'>
			<b  style='color:green;display:none' id='disp_local_dir'>dir sent to server</b>
			<br />			
			<b  style='color:green;display:none' id='disp_local_sp'>sp sent to server</b>
				
		</div>
		
		<div class='box_1'>
			<b  style='color:red;display:none' id='disp_server'>Response from server</b>
		</div>
</div>
<!-- ========================================================================================== -->

</body>
</html>
