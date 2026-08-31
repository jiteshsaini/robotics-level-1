# Robotics Level 1 : Make Robot with Raspberry Pi | Web Controls 

<p align="left">
Read the article: <a href='https://helloworld.co.in/article/basic-robotics-make-robot-raspberry-pi-web-controls' target='_blank'>
   <img src='https://github.com/jiteshsaini/files/blob/main/img/logo3.gif' height='40px'>
</a> Watch the video on Yotube: 
<a href='https://youtu.be/69w6Q40CBWw' target='_blank'>
   <img src='https://github.com/jiteshsaini/files/blob/main/img/btn_youtube.png' height='40px'>
</a>
</p>

In this first level, we will make a basic robotic platform. The hardware and software of this robotic platform will be enchanced progressively to conduct variety of DIY experiments.

<p align="center">
   <img src="https://github.com/jiteshsaini/files/blob/main/img/web-controlled-raspberry-pi-robot.gif">
</p>

> **Note on the article.** The article and video predate Raspberry Pi OS Bullseye.
> The setup steps below supersede the ones shown there: the `raspi-gpio` command
> the original code used has since been removed from Raspberry Pi OS, and the
> `/etc/sudoers` edit it asked for is no longer necessary. The wiring, the pin
> assignments and the ideas are unchanged.

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

## GPIO pins used

| Function | GPIO (BCM) |
|---|---|
| Motor 1 | 8, 11 |
| Motor 2 | 14, 15 |
| Motor speed (software PWM, 100 Hz) | 20, 21 |

> GPIO 14 is also the UART transmit pin. If `console=serial0,115200` is present
> in `/boot/firmware/cmdline.txt`, kernel boot messages go out that pin and can
> twitch motor 2 while the Pi starts up. Removing it from that line stops it.

## Software setup

Tested on **Raspberry Pi OS Trixie (Debian 13)** on a Raspberry Pi 3A+. The GPIO
tooling used here (`pinctrl` and `rpi-lgpio`) is the portable kind, so a Pi 4 or
Pi 5 should work as well — but neither has been tested.

### 1. Install the web server and GPIO support

```bash
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php python3-rpi-lgpio
```

`python3-rpi-lgpio` provides the familiar `RPi.GPIO` API on top of the kernel's
gpiochip devices. Use it rather than the classic `python3-rpi.gpio`, which drives
pins by writing `/dev/mem` directly and does not work on a Pi 5.

### 2. Copy the code

Copy the `earthrover` folder into the web server's public directory, so that it
ends up at `/var/www/html/earthrover`.

### 3. Give the web server access to the GPIO pins

```bash
sudo adduser www-data gpio
sudo systemctl restart apache2
```

On Raspberry Pi OS, any member of the `gpio` group can drive the pins, so the
web server needs **no root and no `sudo`**. Apache must be restarted, because a
process only picks up group membership when it starts.

> Earlier versions of this project told you to add
> `www-data ALL=(ALL) NOPASSWD: ALL` to `/etc/sudoers`. Don't. That gives the web
> server unrestricted root over the whole machine, and a typo in that file locks
> you out of `sudo` entirely. The `gpio` group does the same job with none of
> that risk.

### 4. Set ownership

```bash
sudo chown -R www-data:www-data /var/www/html/earthrover
sudo find /var/www/html/earthrover -type d -exec chmod 2775 {} +
sudo find /var/www/html/earthrover -type f -exec chmod 664 {} +
sudo adduser "$USER" www-data          # so you can edit the files too
```

Not `chmod 777`. Group ownership plus the setgid bit (`2775`) achieves the same
thing, keeps new files group-correct automatically, and leaves permission
problems visible instead of hiding them.

### 5. Drive it

Open this on a phone or laptop on the same network:

```
http://<your-pi-ip>/earthrover/remote.php
```

## Troubleshooting

**Buttons respond but nothing moves.** Check whether the pins are being driven:

```bash
pinctrl get 8 11 14 15
```

Press *FWD* and run it again. Pin 8 should read `dh` (driving high) and pin 15
`dh`, with 11 and 14 low.

- `pinctrl: command not found` — you are on Buster or older. `pinctrl` arrived
  with Bullseye; on Buster the equivalent was `raspi-gpio`.
- Pins never change — `www-data` is probably not in the `gpio` group. Check with
  `id www-data`, and remember to restart Apache after adding it.

**Direction works but the speed slider does nothing.** The PWM generator is a
separate process:

```bash
pgrep -af generate_pwm.py
```

Exactly one should be running after you move the slider. Pins 20 and 21 read
`lo` even when PWM is active — software PWM toggles at 100 Hz, so a single
sample catches one half of the cycle. That is not a fault.

**The robot resets or behaves erratically under load.** Check for undervoltage:

```bash
vcgencmd get_throttled
```

`0x0` is clean; anything else means the supply has sagged. Motor stall current
on a shared 5 V rail is the usual cause — keep motor power off the Pi's rail.

## Code Files

| File | Role |
|---|---|
| `remote.php` | The control page served to the browser |
| `js/remote.js` | Sends direction and speed to the server over AJAX |
| `ajax_direction.php` | Receives a direction, calls `move()` |
| `ajax_speed.php` | Receives a speed, writes `pwm/pwm1.txt`, restarts the PWM generator |
| `vars.php` | Pin map and the movement functions; the only file that touches GPIO |
| `pwm/generate_pwm.py` | Holds pins 20/21 at the requested duty cycle |
| `pwm/pwm_control.py` | Stops any previous generator and starts a fresh one |
