##########Project: Earthrover        #####################
##########Created by: Jitesh Saini   #####################

import os, time

os.system("pkill -f generate_pwm.py")   # same user, so no sudo needed
print("stopped !!!")

#time.sleep(0.1)

print("starting pwm")
os.system("python3 /var/www/html/earthrover/pwm/generate_pwm.py &")
print("started !!!")
