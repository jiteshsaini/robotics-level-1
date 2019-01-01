<?php

global $m1_1,$m1_2,$m2_1,$m2_2;

 $m1_1 = 8; //motor 1
 $m1_2 = 11; //motor 1
 $m2_1 = 14; //motor 2
 $m2_2 = 15; //motor 2  

//pwm pins are 20 & 21 (seperately handled in python). 
//Rpi's Hardware PWM interferes with audio port.

function gpio_initialise(){
	//echo"init<br>";
	global $m1_1,$m1_2,$m2_1,$m2_2;

	system("gpio -g mode $m1_1 out");
	system("gpio -g mode $m1_2 out");
	system("gpio -g mode $m2_1 out");
	system("gpio -g mode $m2_2 out");
	system("gpio -g write $m1_1 0");
	system("gpio -g write $m1_2 0");
	system("gpio -g write $m2_1 0");
	system("gpio -g write $m2_2 0");
}

function move($dir){
	switch ($dir) {	
		case 'f': forward();	break;
		case 'b': back();		break;
		case 'r': right();		break;
		case 'l': left();		break;
		case 's': stop();		break;	
	}
}

function right(){
	global $m1_1,$m1_2,$m2_1,$m2_2; 
	system("gpio -g write $m1_1 1");
	system("gpio -g write $m1_2 0");
	system("gpio -g write $m2_1 1");
	system("gpio -g write $m2_2 0");
}
function left(){
	global $m1_1,$m1_2,$m2_1,$m2_2; 
	system("gpio -g write $m1_1 0");
	system("gpio -g write $m1_2 1");
	system("gpio -g write $m2_1 0");
	system("gpio -g write $m2_2 1");
}
function forward(){
	global $m1_1,$m1_2,$m2_1,$m2_2; 
	system("gpio -g write $m1_1 1");
	system("gpio -g write $m1_2 0");
	system("gpio -g write $m2_1 0");
	system("gpio -g write $m2_2 1");
}
function back(){
	global $m1_1,$m1_2,$m2_1,$m2_2; 
	system("gpio -g write $m1_1 0");
	system("gpio -g write $m1_2 1");
	system("gpio -g write $m2_1 1");
	system("gpio -g write $m2_2 0");
}
function stop(){
	global $m1_1,$m1_2,$m2_1,$m2_2; 
	system("gpio -g write $m1_1 1");
	system("gpio -g write $m1_2 1");
	system("gpio -g write $m2_1 1");
	system("gpio -g write $m2_2 1");
}
?>

