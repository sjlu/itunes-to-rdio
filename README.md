Introduction
============

I use Rdio a lot and I saw a lot of playlists like "iTunes Top 100" or anything related to iTunes, where people put this stuff in manually. Why not just automate this process?

How to use
==========

- You need to edit config.php.sample to include your Rdio Developer Key:

    `http://developer.rdio.com/member/register`

- Edit the invoke script with the proper Rdio Playlist IDs and iTunes URLs.

    - You must already have created the playlist.
    - You can find iTunes RSS feeds here:

        `http://itunes.apple.com/rss/generator/`

- THEN:

        You can use the invoke script:

        `php invoke.php`

        -- OR --

        You can run the script individually:

        `php itunes-to-rdio.php <Rdio Playlist ID> <iTunes Playlist URL>`

Example iTunes URL:

`http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/sf=143441/limit=100/json`

Example Rdio Playlist URL:

`http://www.rdio.com/#/people/sluzorz/playlists/294061/Mixed_(Rock__Pop)/`

How it works
============

Actually, its a pretty simple script. I'll explain below. For now, this will serve as a design specification.

- We want to ask the user for two things, the iTunes URL and the Rdio Playlist name

- It takes in a URL from iTunes web service such as:
`http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/sf=143441/limit=100/json`
- Because the above data is in JSON, we take the data and put into a associative array, where we can easily iterate through this data.
- From there, we ask the Rdio API for the current Rdio playlist. Because the Rdio playlists are social, we don't want to remove it because we still want the subscribers there. Instead, we will remove every song from the list.
- After removing the songs and preserving the playlist, we will now take our iTunes associative array, search for the songs on Rdio and then place it into the playlist.