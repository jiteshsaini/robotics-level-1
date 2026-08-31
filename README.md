# Robotics Level 1 : Make Robot with Raspberry Pi | Web Controls

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

> **The article and video predate Raspberry Pi OS Bullseye.** Their setup steps
> no longer work: the `raspi-gpio` command the original code used was removed
> from Raspberry Pi OS, and the `/etc/sudoers` edit they describe is no longer
> needed. **Use the Install section below instead.** The wiring, the pin
> assignments and the ideas are unchanged.

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
curl -fsSL https://raw.githubusercontent.com/jiteshsaini/robotics-level-1/trixie-port/earthrover/setup.sh -o setup.sh
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
Pi 5 should work too — neither has been tested.

## What the script did

Worth knowing, both to understand the machine you now have and to do it by hand
if you prefer:

1. **Installed** `git`, `apache2`, `php`, `libapache2-mod-php` and
   `python3-rpi-lgpio`. The last one provides the familiar `RPi.GPIO` API over
   the kernel's gpiochip devices; the older `python3-rpi.gpio` writes `/dev/mem`
   directly and does not work on a Pi 5.
2. **Copied the code** to `/var/www/html/earthrover`. That exact path matters —
   the Python scripts refer to it directly. The `.git` folder is left behind, so
   nothing of it is served by the web server.
3. **Added `www-data` to the `gpio` group.** On Raspberry Pi OS that is enough
   to drive the pins, so the web server needs no root — no `sudo`, and no
   editing `/etc/sudoers`.
4. **Gave `www-data` ownership of `pwm/pwm1.txt`** — the only file this project
   writes, where the speed slider stores its value. Everything else stays
   read-only, so no `chmod 777`.
5. **Restarted Apache**, because a process only picks up new group membership
   when it starts.

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

## Troubleshooting

**Buttons respond but nothing moves.**

```bash
pinctrl get 8 11 14 15
```

Press *FWD* and run it again: pins 8 and 15 should read `dh`, with 11 and 14
low.

- `pinctrl: command not found` — you are on Buster or older. `pinctrl` arrived
  with Bullseye.
- Pins never change — `www-data` is probably not in the `gpio` group. Check with
  `id www-data`; re-running `setup.sh` fixes it.

**Direction works but the speed slider does nothing.**

```bash
pgrep -af generate_pwm.py
```

Exactly one should be running after you move the slider. If none is, check that
the web server can write the speed file:

```bash
ls -l /var/www/html/earthrover/pwm/pwm1.txt      # owner should be www-data
```

When that file is not writable, `ajax_speed.php` stops at its `can't open file`
guard and the page gives no visible sign of it.

Note that pins 20 and 21 read `lo` even when PWM is active — software PWM
toggles at 100 Hz, so a single sample catches one half of the cycle. That is not
a fault.

**The robot resets or behaves erratically under load.**

```bash
vcgencmd get_throttled
```

`0x0` is clean; anything else means the supply has sagged. Motor stall current
on a shared 5 V rail is the usual cause — keep motor power off the Pi's rail.
