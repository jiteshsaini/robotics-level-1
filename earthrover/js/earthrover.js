$(document).keydown(function(e){
    if (e.keyCode == 37)  
    	 button_direction('l');
    if (e.keyCode == 38) 
        button_direction('f');
    if (e.keyCode == 39) 
    	  button_direction('r');
    if (e.keyCode == 40)
    	 button_direction('b');
    if (e.keyCode == 32)
    	 button_direction('s');
});

//---------DIRECTION---------------------------------
function button_direction(val)
{
	document.getElementById("disp_local_dir").innerHTML=val;
   set_direction(val);
}

function set_direction(dir)
{
	$.post("ajax_direction.php",
    {
      direction: dir
      //speed:sp
    },
    function(data,status){
   	 document.getElementById("disp_server").innerHTML = data;
    });

}
//-----------------------------------------------

//---------SPEED---------------------------------
function button_speed(val)
{
	var sp=document.getElementById("disp_speed").innerHTML;
   
   if(val=='+' && Number(sp)<100)
	{
		document.getElementById("disp_speed").innerHTML = Number(sp) + 10;
	}
		
	if(val=='-' && Number(sp)>10)
	{
		document.getElementById("disp_speed").innerHTML = Number(sp) - 10;
	}
	
	sp=document.getElementById("disp_speed").innerHTML;
	document.getElementById("disp_local_sp").innerHTML=sp;
	
	
	set_speed(sp);
}

function set_speed(sp)
{
	$.post("ajax_speed.php",
    {
     // direction: dir,
      speed:sp
    },
    function(data,status){
   	 //document.getElementById("disp_server").innerHTML = data;
    });

}
//-----------------------------------------------

function button_send()
{
	$.post("ajax_speed.php",
    {
      speed:document.getElementById("txt_send").value
    }
    );
}

