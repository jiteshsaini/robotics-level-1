<?php
include_once 'vars.php';

$speed=$_POST["speed"];

$pwm_val=intval($speed);

echo"pwm val: \" $pwm_val \"  <br>";

$myFile = "pwm/pwm1.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $pwm_val);
fclose($fh);

/* append following lines in /etc/sudoers file for launching python script from PHP:-
    pi ALL=(ALL) NOPASSWD: ALL
	www-data ALL=(ALL) NOPASSWD: ALL
*/
exec("sudo python /var/www/html/earthrover/pwm/pwm_control.py");# launch Python script

?>
