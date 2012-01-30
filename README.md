Introduction
============

I use Rdio a lot and I saw a lot of playlists like "iTunes Top 100" or anything related to iTunes, where people put this stuff in manually. Why not just automate this process?

### Playlists using this script

- [iTunes Top 100 (US)](http://www.rdio.com/#/people/sluzorz/playlists/526420/iTunes_Top_100)
- [iTunes Top 100 - Hip-Hop/Rap (US)] (http://www.rdio.com/#/people/sluzorz/playlists/526666/iTunes_Top_100_-_Hip-Hop/Rap_)
- [iTunes Top 100 - Pop (US)](http://www.rdio.com/#/people/sluzorz/playlists/526674/iTunes_Top_100_-_Pop_)
- [iTunes Top 100 - Alternative (US)] (http://www.rdio.com/#/people/sluzorz/playlists/526679/iTunes_Top_100_-_Alternative_)
- [iTunes Top 100 - Rock (US)](http://www.rdio.com/#/people/sluzorz/playlists/526683/iTunes_Top_100_-_Rock_)

Currently, one of my personal servers keeps this in sync. You're free to adopt this script on your own systems. If you want me to maintain a list that isn't listed here, feel free to message me!

Instructions
==========

### Requirements

Make sure you have PHP > 5 installed, I believe it requries php-curl to be installed also. That's it!

### How to use

- You need to edit config.php.sample to include your Rdio Developer Key:

    `http://developer.rdio.com/member/register`

- Edit the invoke script `invoke.php` with the proper Rdio Playlist IDs and iTunes URLs.

    - You must already have created the playlist.
    - You can find iTunes RSS feeds here:

        `http://itunes.apple.com/rss/generator/`

- You can then execute the invoke script by:

    `php invoke.php`

- Or you can run the script as a one time use:

    `php itunes-to-rdio.php <Rdio Playlist ID> <iTunes RSS URL>`


### Example iTunes URL:

`http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/sf=143441/limit=100/json`

### Example Rdio Playlist URL:

`http://www.rdio.com/#/people/sluzorz/playlists/294061/Mixed_(Rock__Pop)/`

Note from the above URL, that `294061` is the ID that the script requires.

How it works
============

Actually, its a pretty simple script. I'll explain below. For now, this will serve as a design specification.

- We want to ask the user for two things, the iTunes URL and the Rdio Playlist name

- It takes in a URL from iTunes web service such as:
`http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/sf=143441/limit=100/json`
- Because the above data is in JSON, we take the data and put into a associative array, where we can easily iterate through this data.
- From there, we ask the Rdio API for the current Rdio playlist. Because the Rdio playlists are social, we don't want to remove it because we still want the subscribers there. Instead, we will remove every song from the list.
- After removing the songs and preserving the playlist, we will now take our iTunes associative array, search for the songs on Rdio and then place it into the playlist.