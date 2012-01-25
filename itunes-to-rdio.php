<?php
require_once 'config.php';
require_once 'rdio/php/rdio.php';

// STEP 1: LOGIN TO RDIO
// We need to authenticate to Rdio before we
// can move on with the rest of our script.
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

// STEP 2: Checking if user has that playlist.
$playlist_id = 'p'.RDIO_PLAYLIST_ID;
$playlists = $rdio->call('getPlaylists')->result->owned;

$has_playlist = false;
foreach ($playlists as $playlist)
{
   if ($playlist->key == $playlist_id)
   {
      $has_playlist = true;
      break;
   }
}

if (!$has_playlist)
   die('You do not own any playlist of that ID.');

// STEP 3: Now we grab the iTunes data!
// And try to find it on Rdio!
$itunes = file_get_contents(ITUNES_URL);
$itunes = json_decode($itunes, true);

$add_songs = array();
foreach ($itunes['feed']['entry'] as $song)
{
   // Removing away junk from title, and search JUST by title.
   $title = $song['im:name']['label'];
   $title = preg_replace(array("/\(.*\)/", "/\[.*\]/", "/-/"), "", $title);
   $artist = $song['im:artist']['label'];  
   echo "Searching Rdio for: " . $title . " - " . $artist ."\n";
 
   $rdio_search = $rdio->call('search', array(
      "query" => $title, 
      "types" => "Track"
   ));
 
   $found_result = false;
   foreach ($rdio_search->result->results as $result)
   {
      if ($result->canStream == 1 &&
         !empty($result->albumArtist) &&
         stristr($artist, $result->albumArtist))
      {
         echo "\t- Selected result: ".$result->name." - ".$result->albumArtist."\n";
         $add_songs[] = $result->key;
         $found_result = true;
         break;
      }
   }

   if (!$found_result)
   {
      echo "\tCouldn't find anything, trying a different search\n";
      $rdio_search = $rdio->call('search', array(
         "query" => $title." ".$artist,
         "types" => "Track"
      ));

      foreach ($rdio_search->result->results as $result)
      {
         if (!empty($result->albumArtist) &&
            stristr($artist, $result->albumArtist))
         {
            echo "\t- Selected result: ".$result->name." - ".$result->albumArtist."\n";
            $add_songs[] = $result->key;
            $found_result = true;
            break;
         }
      }
   }

   echo "\n";
}

echo "\n\nClearing Rdio Playlist: " . $playlist_id;

// STEP 4: Deleting everything in that playlist.
$songs_in_playlist = $rdio->call('get', array(
   'keys' => $playlist_id, 
   'extras' => 'trackKeys'
));
$songs_in_playlist = $songs_in_playlist->result->$playlist_id->trackKeys;
$rdio->call('removeFromPlaylist', array(
   'playlist' => $playlist_id, 
   'index' => 0, 
   'count' => count($songs_in_playlist), 
   'tracks' => implode(",", $songs_in_playlist)
));

echo "\nAdding songs to Rdio Playlist: " . $playlist_id;

// STEP 5: Adding all the songs into the playlist.
$rdio->call('addToPlaylist', array(
   "playlist" => $playlist_id, 
   "tracks" => implode(",", $add_songs)
));

$rdio->call('setPlaylistOrder', array( // for some reason adding does not set order.
   "playlist" => $playlist_id,
   "tracks" => implode(",", $add_songs)
));

echo "\n";
?>
