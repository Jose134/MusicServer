<?php

    //------AJAX------\\
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

        switch($action) {
            case 'showList' : showList($_POST['search']); break;
            case 'showArtist' : showArtist($_POST['search']); break;
            case 'download' : download($_POST['file']); break;
        }
    }
    //----------------\\

    //Shows a list of songs
    function showList ($search) {
        //Variables
        $formats = Array (".mp3", "audio/mpeg", ".ogg", "audio/ogg", ".wav", "audio/wav");
        $path = "Music/";
        $files = Array();

        //Checks if there is a search
        if (isset($_GET["search"])) {
            $search = $_GET["search"];
        }

        $files = searchFiles($path, $search, $formats);
        displaySongs($files, $formats);
    }

    //Shows a list of all artists
    function showArtist($search) {
        $dirs = array_filter(glob("Music/*"), "is_dir");
        $artists = [];
      
        //Searchs for all directories matching the artist search
        foreach ($dirs as $dir) {
            //Checks if the files match the search and adds them to an array
            if ($search != "") {
                $searchNeedles = explode(" ", $search);
                $count = 0;

                //Checks every search needle
                foreach ($searchNeedles as $needle) {
                    if ($needle == "") { $count++; continue; }

                    if (strpos(strtolower($dir), strtolower($needle)) != false) {
                        $count++;
                    }
                }
                
                //Adds the artist to the array if all needles were found
                if ($count == count($searchNeedles)) {
                    array_push($artists, $dir);
                }
            }
            else {
                array_push($artists, $dir);
            }

            
        }

        //Displays all the artists found
        foreach ($artists as $artist) {
            $name = str_replace("Music/", "", $artist);

            echo "<div class=\"folder\">";
            echo "<button class=\"artistbutton\" onclick=\"openArtist(event, '$name')\">$name</button>";
            echo "</div>";
        }
    }

    //Returns all the files in a directory and its subdirectories matching the given search and formats
    function searchFiles($path, $search, $formats) {
        $files = Array();

        //Runs the code for every format
        for ($i = 0; $i < count($formats); $i+=2) {
            //Searchs for audio files matching the format
            $formatFiles = recursiveGlob($path . "*" . $formats[$i]);

            foreach ($formatFiles as $file) {
                //Checks if the files match the search and adds them to an array
                if ($search != "") {
                    $searchNeedles = explode(" ", $search);
                    $count = 0;

                    //Checks every search needle
                    foreach ($searchNeedles as $needle) {
                        if ($needle == "") { $count++; continue; }

                        if (strpos(strtolower($file), strtolower($needle)) != false) {
                            $count++;
                        }
                    }
                    
                    //Adds the artist to the array if all needles were found
                    if ($count == count($searchNeedles)) {
                        array_push($files, $file);
                    }
                }
                else {
                    array_push($files, $file);
                }
            }
        }

        return $files;
    }

    //Adds a block to the html for each song in the files array
    function displaySongs ($files, $formats) {
        foreach ($files as $file) {
            //Gets the song information string ("artist - songname.format") from the file path
            $explodedPath = explode("/", $file);
            $songInfo = explode(" - ", $explodedPath[count($explodedPath) - 1]);
            $path = str_replace($explodedPath[count($explodedPath) - 1], "", $file);
            $img = file_exists($path . "img.jpg") ? $path . "img.jpg" : "";

            //Gets the file format
            $format = substr($songInfo[1], strlen($songInfo[1]) - 4, 4);

            //Gets the audio type (for the html audio tag)
            $typeIndex = array_search($format, $formats) + 1;
            $type = $formats[$typeIndex];

            //Removes the format from the song name string
            $songInfo[1] = str_replace($format, "", $songInfo[1]);

            //Builds an html block for the song
            echo "<div class=\"songdiv\" id=\"$file\" div\">";

            echo "<p style=\"margin-left:10px;\">";
            echo $songInfo[1];
            echo "</p>";

            echo "<p style=\"font-size:75%; color:#CCCCCC; position:relative; bottom:15px; margin-left: 10px;\">";
            echo  $songInfo[0];
            echo "</p>";

            echo "<button onclick=\"setSong('$file', '$type')\" class=\"songbuttons\"><i class=\"fa fa-play\"></i></button>";
            echo "<button onclick=\"addToPlaylist('$file', '$type')\" class=\"songbuttons\"><i class=\"fa fa-music\"></i></button>";
            echo "<button onclick=\"download('$file')\" class=\"songbuttons\"><i class=\"fa fa-download\"></i></button>";
            
            echo "</div>";
        }

    }

    //Recursively executes glob to find all files in a directory and its subdirectories
    function recursiveGlob ($pattern) {
        $foundFiles = glob($pattern);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $foundFiles = array_merge($foundFiles, recursiveGlob($dir . '/' . basename($pattern)));
        }

        return $foundFiles;
    }
?>