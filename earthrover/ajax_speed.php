<?php
include_once 'vars.php';

$speed=$_POST["speed"];

$pwm_val=intval($speed);

echo"pwm val: \" $pwm_val \"  <br>";

$myFile = "pwm/pwm1.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $pwm_val);
fclose($fh);

/* No sudo and no /etc/sudoers edit is needed. The PWM script drives the
   pins through RPi.GPIO, which on Raspberry Pi OS works for any member of
   the "gpio" group - so adding the web server to that group once is
   enough:  sudo adduser www-data gpio && sudo systemctl restart apache2

   python3 explicitly: bare "python" only exists if python-is-python3 is
   installed, and when it is missing the PWM silently never starts. */
exec("python3 /var/www/html/earthrover/pwm/pwm_control.py");# launch Python script

?>
