<?php

$playlist_sync = array();

// YOU CAN EDIT THIS SECTION
$playlist_sync[] = array('rdio_playlist_id' => 526420, 'itunes_url' => 'http://itunes.apple.com/us/rss/topsongs/limit=100/explicit=true/json'); // top songs us
$playlist_sync[] = array('rdio_playlist_id' => 526666, 'itunes_url' => 'http://itunes.apple.com/us/rss/topsongs/limit=100/genre=18/explicit=true/json'); // top hip-hop/rap us
$playlist_sync[] = array('rdio_playlist_id' => 526674, 'itunes_url' => 'http://itunes.apple.com/us/rss/topsongs/limit=100/genre=14/explicit=true/json'); // pop
$playlist_sync[] = array('rdio_playlist_id' => 526679, 'itunes_url' => 'http://itunes.apple.com/us/rss/topsongs/limit=100/genre=20/explicit=true/json'); // alternative
$playlist_sync[] = array('rdio_playlist_id' => 526683, 'itunes_url' => 'http://itunes.apple.com/us/rss/topsongs/limit=100/genre=21/explicit=true/json'); // rock

// DO NOT EDIT PAST HERE
require_once 'config.php';
require_once 'rdio/php/rdio.php';

$rdio = new Rdio(array(RDIO_KEY, RDIO_SECRET));

if (file_exists('.session.json'))
{
   $session = json_decode(file_get_contents('.session.json'), true);
   $rdio->token = array($session['token'], $session['secret']);
}

if (empty($session))
{
   $url = $rdio->begin_authentication('oob');
   print "Please go to the following URL.\n";
   print "$url\n\n";
   print "Please enter the code it provides: ";
   $verifier = trim(fgets(STDIN));
   $rdio->complete_authentication($verifier);

   $session['token'] = $rdio->token[0];
   $session['secret'] = $rdio->token[1];

   $file = fopen('.session.json', 'w');
   fwrite($file, json_encode($session));
   fclose($file);
}

foreach ($playlist_sync as $playlist)
   exec('php ' . dirname(__FILE__) . '/itunes-to-rdio.php ' . $playlist['rdio_playlist_id'] . ' ' . $playlist['itunes_url'] . ' &');

?>
