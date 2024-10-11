Hatena server - by pbsds
======

Thing added by mt

-functional website (host with wamp or something you just need apache and php)

-very hacky way of adding comments on the dsi

-very hacky profiles on the dsi (fully functional on the website tho)



THIS IS VERY BROKEN AND INSECURE 

problems from more important to less important

-the server refuses to update and server generated files inside of the hatenadir until a server restart(reason why this code is abandoned)

-i made the stupid choise of making all of the movie.py variables that it sends be global variables wich means that multiple clients can interfer with each other requests wich is horrible(should be a easy fix)

-there is nothing preventing users to change their console name and inpersoante other users 

- no auth of any kind

This is a replacement for the Flipnote Hatena service for the DSi which has ended.
It's written in Python 2.7 and requires Twisted also requieres php and numpy.
Future versions could need PIL aswell.
This project uses Hatenatools, which is also written by me. It can be found here: http://pbsds.net/projects/hatenatools 
This mt fork uses Sudomemoutils for some very hacky execution(https://github.com/Sudomemo/sudomemo-utils)
also uses some code by nitrogen (code not on github) with some minimum changes(just made it compatilbe with newer php by flooring some values nothing much) the code convers ppms to npf for comments
To use it, simply run server.py. (you will need php on your path)
On the DSi, set the proxy settings to point to this server on port 8080, then access Flipnote Studio as usual. A more detailed guide is on the wiki section.

Documentation on the formats Flipnote Studio uses can be found in the wiki section of this git.
