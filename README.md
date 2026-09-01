# Robotics Level 1 : Make Robot with Raspberry Pi | Web Controls

**Updated to work with the latest Raspberry Pi OS (Trixie, Debian 13).**

<p align="left">
Read the article: <a href='https://helloworld.co.in/article/basic-robotics-make-robot-raspberry-pi-web-controls' target='_blank'>
   <img src='https://raw.githubusercontent.com/jiteshsaini/files/main/img/logo3.gif' height='40px'>
</a> Watch the video on Yotube:
<a href='https://youtu.be/69w6Q40CBWw' target='_blank'>
   <img src='https://raw.githubusercontent.com/jiteshsaini/files/main/img/btn_youtube.png' height='40px'>
</a>
</p>

The first level: a basic robotic platform, driven from a web page on your phone.
Later levels add a camera, sensors and on-board machine learning to the same
chassis.

<p align="center">
   <img src="https://raw.githubusercontent.com/jiteshsaini/files/main/img/web-controlled-raspberry-pi-robot.gif">
</p>

> Follow the **Install** section below for setup. The wiring, the pin
> assignments and the ideas are exactly as shown in the article and video.

## Hardware

- Raspberry Pi 3A+
- MT 3608 DC-DC up converter
- L293D Motor Driver Module
- DC Motor, 100 RPM, 12 V
- Battery bank with 2 USB slots

<p align="center">
   <img src="https://helloworld.co.in/1/sites/default/files/inline-images/raspberry-pi-robot-circuit-diagram.jpeg">
</p>

<p align="center">
   <img src="https://helloworld.co.in/1/sites/default/files/inline-images/raspberry-pi-robot-component-connections.jpeg">
</p>

| Function | GPIO (BCM) |
|---|---|
| Motor 1 | 8, 11 |
| Motor 2 | 14, 15 |
| Motor speed (software PWM, 100 Hz) | 20, 21 |

> GPIO 14 is also the UART transmit pin. If `console=serial0,115200` appears in
> `/boot/firmware/cmdline.txt`, kernel boot messages go out of that pin and can
> twitch motor 2 while the Pi starts. Remove it from that line to stop it.

## Install

Two commands on a fresh Raspberry Pi OS. Nothing to copy by hand.

```bash
curl -fsSL https://raw.githubusercontent.com/jiteshsaini/robotics-level-1/master/earthrover/setup.sh -o setup.sh
```

```bash
sudo bash setup.sh
```

Downloading first, rather than piping into `sudo bash`, lets you read the script
before running it as root.

When it finishes it prints an address. Open it on a phone or laptop on the same
network:

```
http://<your-pi-ip>/earthrover/remote.php
```

Run the script again any time to update: it moves your existing copy to
`earthrover.backup_<date>` rather than overwriting it.

Tested on **Raspberry Pi OS Trixie (Debian 13)** on a Raspberry Pi 3A+. The GPIO
tooling used here (`pinctrl` and `rpi-lgpio`) is the portable kind, so a Pi 4 or
Pi 5 should work too.

## What the script did

Worth knowing, both to understand the machine you now have and to do it by hand
if you prefer:

1. Installed the web server (Apache and PHP) and the Python GPIO library.
2. Copied the code to `/var/www/html/earthrover`.
3. Let the web server use the GPIO pins, by adding it to the `gpio` group.
4. Let the web server save the speed setting, by giving it ownership of one
   file, `pwm/pwm1.txt`.
5. Restarted Apache so those changes take effect.

No `chmod 777`, and nothing added to `/etc/sudoers` — the web server never gets
root. The GPIO library is `rpi-lgpio` rather than the older `RPi.GPIO`, because
the older one does not work on a Raspberry Pi 5.

The script also pings helloworld.co.in once when it finishes, so I know the
project is being used. It sends the Pi model and OS name, nothing more. Delete
those lines from `setup.sh` if you would rather it did not.

## How it works

A button press posts to a small PHP file, which sets GPIO pins through
`pinctrl`. Speed is separate: the slider writes a duty cycle to a file, and a
Python script holds pins 20 and 21 at that duty cycle using software PWM.

| File | Role |
|---|---|
| `remote.php` | The control page served to the browser |
| `js/remote.js` | Sends direction and speed to the server over AJAX |
| `ajax_direction.php` | Receives a direction, calls `move()` |
| `ajax_speed.php` | Receives a speed, writes `pwm/pwm1.txt`, restarts the PWM generator |
| `vars.php` | Pin map and movement functions — the only file that touches GPIO |
| `pwm/generate_pwm.py` | Holds pins 20/21 at the requested duty cycle |
| `pwm/pwm_control.py` | Stops any previous generator, starts a fresh one |
| `setup.sh` | The installer above |
