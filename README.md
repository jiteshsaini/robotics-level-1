# Robotics Level 1 : Make Robot with Raspberry Pi | Web Controls 

<p align="left">
Read the article: <a href='https://helloworld.co.in/article/basic-robotics-make-robot-raspberry-pi-web-controls' target='_blank'>
   <img src='https://github.com/jiteshsaini/files/blob/master/img/logo3.gif' height='40px'>
</a> Watch the video on Yotube: 
<a href='https://youtu.be/69w6Q40CBWw' target='_blank'>
   <img src='https://github.com/jiteshsaini/files/blob/master/img/btn_youtube.png' height='40px'>
</a>
</p>

In this first level, we will make a basic robotic platform. The hardware and software of this robotic platform will be enchanced progressively to conduct variety of DIY experiments.

<p align="center">
   <img src="https://github.com/jiteshsaini/files/blob/master/img/web-controlled-raspberry-pi-robot.gif">
</p>

## Circuit Diagram of the Robot

<p align="center">
   <img src="https://helloworld.co.in/sites/default/files/inline-images/raspberry-pi-robot-circuit-diagram.jpeg">
</p>

Hardware Components used in the robot are:-
- Raspberry Pi 3A+
- MT 3608 DC-DC up converter
- L293D Motor Driver Module
- DC Motor, 100 RPM, 12 V
- Battery Bank with 2 USB slots

<p align="center">
   <img src="https://helloworld.co.in/sites/default/files/inline-images/raspberry-pi-robot-component-connections.jpeg">
</p>

## Code Files
The code of this robot is placed inside the folder 'earthrover'. You need to install a webserver Apache and PHP on Raspberry Pi to use this code.
<br>Copy the folder 'earthrover' and place it in the public directory of webserver. The path of the folder should now be '/var/www/html/earthrover'

## Raspberry Pi configurations prior running the code

1. Set permissions

- sudo chmod 777 -R /var/www/html/earthrover


2. Modify the sudoer file to allow executing python files from php files
- $ sudo nano /etc/sudoers
- paste following at the end of file and save (as shown in picture below)

pi ALL=(ALL) NOPASSWD: ALL <br>
www-data ALL=(ALL) NOPASSWD: ALL

<p align="center">
   <img src="https://github.com/jiteshsaini/robotics-level-1/blob/master/img/sudoers.png">
</p>
