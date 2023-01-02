# gecko-minigb (aka minigb)

![minigb button](https://github.com/ThatRoboticFish/minigb/blob/main/img/minigb.gif?raw=true)

Simple, minimalistic and buggy (lol) retro-styled PHP7 guestbook script with basic ""multi-user"" support and without mySQL. Based in the [Flat-File Guestbook Script](https://github.com/taufik-nurrohman/flat-file-guestbook) code and on IglooGB. Ideal to be placed in small iframes as I have done in my personal website.

NOTE: This script is currently in alpha and is not recommended for use on large websites.

![minigb example screenshot // GeckoF's gb](https://geckof.dimension.sh/img/misc/2022-12-27_181517.png)

# Installation

## Pre-requirements

* PHP 7.x
* A webserver w/ PHP support (e.g. [Apache](https://httpd.apache.org/), [nginx](https://nginx.org/), [lighttpd](https://lighttpd.net/))

Download this script from the Releases page (or you can just clone/download this entire repository if you wish) and upload it to your web server in a directory called "gb" or similar.

After you have done the above, you will need to change your default username in the "cfg.php" file and rename the "style_example.css" and "entries_example.txt" files to your new current minigb default username (e.g. "entries_penguin.txt" and "style_penguin.css").

When you have done the above, you will be able to visit your guestbook in your web browser by going to the IP address/domain of your web server and on the directory where you have installed your guestbook (example: http://192.168.1.7/gb/).

To add a new user you will need to create a new file called "entries_newusername.txt" and "style_newusername.css" (where newusername will be the username of the new user). You will be able to visit them by adding a (?usr=newusername) in the URL of their guestbook (example: http://192.168.1.7/gb/index.php?usr=newusername).

If you are on UNIX, you must make the entries file writeable and readable for everyone (0666), otherwise PHP will not be able to write new entries and you will get errors.
