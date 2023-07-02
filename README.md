# ![minigb button](https://github.com/ThatRoboticFish/minigb/blob/main/img/minigb.gif?raw=true) gecko-minigb (aka minigb)

Simple, minimalistic and buggy (lol) retro-styled PHP7 guestbook script with basic multi-user system and without mySQL. Based in the [Flat-File Guestbook](https://github.com/taufik-nurrohman/flat-file-guestbook) code and on [IglooGB](https://gb.igloocafe.space/). Ideal to be placed in small iframes.

NOTE: This script is currently in alpha and is not recommended for use on large websites.

![minigb example screenshot](https://geckof.dimension.sh/img/misc/2023-01-04_102307.png)

# Installation

## Pre-requirements
* PHP 7.x/8.x
* A web server w/ PHP support (e.g. [Apache](https://httpd.apache.org/), [nginx](https://nginx.org/) or [lighttpd](https://lighttpd.net/))

Download this script from the Releases page (or you can just clone/download this entire repository if you wish) and upload it to your web server in a directory called "gb" or similar. After that, you can test by visiting your new gecko-minigb installation in your web browser by going to the IP address/domain of your web server and on the directory where you have installed this (e.g. http://192.168.1.7/gb/ or http://localhost/gb/).

If you are on UNIX, you must make the entries file writeable and readable for everyone (0666), otherwise PHP will not be able to write new entries and you will get errors.

# New users
To create new users, go to the "users" directory and copy those 3 files found there or rename them, replacing "example" with your new user name. After that, visit your gecko-minigb installation from your web browser and your new user should appear in the user listing (as long as it's not disabled from cfg.php) like in the example below.

![User Listing example](https://media.discordapp.net/attachments/972204450456428554/1124868782972751924/image.png)

All users can also be loaded from the URL by using the "usr" query string (e.g. http://localhost/gb/index.php?usr=example or http://localhost/gb/?usr=example if your web server knows what index.php is).

# Replies
To leave a reply to a guest's message, go to your entries text file database and after the guest's comment field you will find an empty one (e.g. <||>Guest Comment<||><||>) where guest comment replies will be stored. Just add some text inside that empty field and save the file. It should look something like this:

![Demo Comment/Reply](https://geckof.dimension.sh/img/misc/minigb_demo_reply.jpg)

An example of an comment reply can be found in the default entries text file database of this script.

# Options
Most of the global settings are in the "cfg.php" file, and the user settings are in the "users" folder with the name "conf_username.ini".

At the moment, only these two options are available for users:

```
[booleans]
disable_entries = 0/1 ; Disable new entries on a single user guestbook
swatch = 0/1 ; Shows Swatch time on comment dates
```
