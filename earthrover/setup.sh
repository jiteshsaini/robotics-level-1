#!/bin/bash
#
# setup.sh - install this robot on a Raspberry Pi, from scratch.
#
# Download it and run it - see the README for the exact download command.
# Nothing else to copy or configure.
#
# It installs the web server and GPIO support, fetches the code into
# /var/www/html/earthrover, lets the web server drive the pins without root,
# and prints the address to open.
#
# Safe to run again later - it backs up any existing copy first, so re-running
# is also how you update to the newest code.

set -e

REPO="https://github.com/jiteshsaini/robotics-level-1.git"

# The branch to install. It must match the branch this script was downloaded
# FROM, or you will install different code than you expected.
#
# Leave it EMPTY to follow the repository's default branch - which is what you
# want once this work is merged, because then it never needs changing again.
BRANCH="trixie-port"

TARGET="/var/www/html/earthrover"

if [ "$(id -u)" -ne 0 ]; then
    echo "This needs root. Re-running with sudo ..."
    exec sudo -- bash "$0" "$@"
fi

echo "==> Installing the web server and GPIO support ..."
apt-get update
apt-get install -y git apache2 php libapache2-mod-php python3-rpi-lgpio

# python3-rpi-lgpio gives the familiar RPi.GPIO API on top of the kernel's
# gpiochip devices. The older python3-rpi.gpio writes /dev/mem directly, which
# does not work on a Raspberry Pi 5.

echo "==> Fetching the code (branch: ${BRANCH:-default}) ..."
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT
if [ -n "$BRANCH" ]; then
    git clone --depth 1 --branch "$BRANCH" "$REPO" "$TMP/repo"
else
    git clone --depth 1 "$REPO" "$TMP/repo"
fi

# Check what we got before touching anything that already exists on the machine.
# A wrong branch name fails at the clone above; a repo whose shape has changed
# would otherwise be discovered halfway through the move.
if [ ! -d "$TMP/repo/earthrover" ]; then
    echo "ERROR: that branch has no 'earthrover' folder. Nothing has been changed."
    exit 1
fi

if [ -e "$TARGET" ]; then
    BACKUP="${TARGET}.backup_$(date +%Y%m%d_%H%M%S)"
    echo "==> Existing install found. Moving it to:"
    echo "    $BACKUP"
    mv "$TARGET" "$BACKUP"
fi

echo "==> Installing to $TARGET ..."
mkdir -p "$(dirname "$TARGET")"
mv "$TMP/repo/earthrover" "$TARGET"
# The .git folder stays behind in the temp dir, so nothing of it is served
# by the web server.

echo "==> Allowing the web server to use the GPIO pins ..."
# On Raspberry Pi OS anyone in the 'gpio' group can drive the pins, so the web
# server needs no root at all - no sudo, and no editing /etc/sudoers.
adduser www-data gpio >/dev/null 2>&1 || true

echo "==> Allowing the web server to save the speed setting ..."
# The only file this project ever writes: the speed slider stores its value
# here and the PWM script reads it back.
chown www-data "$TARGET/pwm/pwm1.txt"

echo "==> Restarting Apache ..."
# Needed: a process only picks up new group membership when it starts.
systemctl restart apache2

echo
echo "==> Checking ..."
ok=1
say() { printf '    %-22s %s\n' "$1" "$2"; }

# Every test sits inside an `if`: under `set -e` a bare failing command would
# abort the script instead of reporting the failure we are trying to show.
if command -v pinctrl >/dev/null;                           then say "pinctrl installed"   yes; else say "pinctrl installed"   NO; ok=0; fi
if id -nG www-data | grep -qw gpio;                         then say "www-data in gpio"    yes; else say "www-data in gpio"    NO; ok=0; fi
if [ "$(stat -c %U "$TARGET/pwm/pwm1.txt")" = "www-data" ]; then say "speed file writable" yes; else say "speed file writable" NO; ok=0; fi
if [ -f "$TARGET/remote.php" ];                             then say "code in place"       yes; else say "code in place"       NO; ok=0; fi
if systemctl is-active --quiet apache2;                     then say "apache2 running"     yes; else say "apache2 running"     NO; ok=0; fi

echo
if [ "$ok" -eq 1 ]; then
    IP="$(hostname -I | awk '{print $1}')"
    echo "Done. Open this on a phone or laptop on the same network:"
    echo
    echo "    http://${IP}/earthrover/remote.php"
    echo
    echo "Run this script again any time to update to the latest code."
else
    echo "Something above is not right - see the Troubleshooting section of the"
    echo "README at https://github.com/jiteshsaini/robotics-level-1"
    exit 1
fi
