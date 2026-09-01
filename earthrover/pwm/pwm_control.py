##########Project: Earthrover        #####################
##########Created by: Jitesh Saini   #####################

import os, time

os.system("pkill -f generate_pwm.py")   # same user, so no sudo needed
print("stopped !!!")

#time.sleep(0.1)

print("starting pwm")
# Run it from /tmp: the GPIO library writes a small working file in whatever
# folder it starts in, and the web server cannot write to its own.
os.system("cd /tmp && python3 /var/www/html/earthrover/pwm/generate_pwm.py &")
print("started !!!")
