Introduction
============

I use Rdio a lot and I saw a lot of playlists like "iTunes Top 100" or anything related to iTunes, where people put this stuff in manually. Why not just automate this process?

How it works
============

Actually, its pretty simple. It takes in a URL from iTunes web service such as: `http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/sf=143441/limit=100/json`
Parses it, then places it into an Rdio playlist using the Rdio API.
