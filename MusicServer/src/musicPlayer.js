var audio = document.getElementById("MusicPlayer");

var playlistDiv = document.getElementById("QueueInner");
var playlist = [];
var currentSong = -1;
var currentSongP;
var playMode = 0; //0 = play in order; 1 = play random; 2 = repeat

var queueEmpty = document.getElementById("QueueEmpty");

//Sets in the audio the song sent as a source and plays it
function setSong (src, type) {
    var audio = document.getElementById("MusicPlayer");
    audio.src = src;
    audio.load();
    audio.play();
}

//Adds a song to the playlist
function addToPlaylist (src, type) {
    for (var i = 0; i < playlist.length; i++) {
        if (playlist[i] == src) {
            return;
        }
    }

    //Adds the song to the playlist array
    playlist.push(src);

    showList();
}

//Sets the queue to be all the songs currently displaying and starts playing the first one
function playAll () {
    playlist = [];
    currentSong = -1;

    var songs = document.getElementsByClassName("songdiv");

    for (var i = 0; i < songs.length; i++) {
        playlist.push(songs[i].id.substr(0, songs[i].id.length - 4));
    }

    showList();
    playerEnded();
}

//Changes the play mode of the queue
function changeMode () {
    //Sets the next play mode
    playMode++;
    if (playMode == 3) {
        playMode = 0;
    }

    //Updates the icon on the button
    var modeButtonIcon = document.getElementById("ModeButton");
    switch (playMode) {
        case 0:
            //List mode
            modeButtonIcon.className = "fa fa-list";
            break;
        case 1:
            //Random mode
            modeButtonIcon.className = "fa fa-random";
            break;
        case 2:
            //Repeat mode
            modeButtonIcon.className = "fa fa-redo";
            break;
    }
}

//Removes a given song from the queue
function removeFromPlaylist (src) {
    //Searchs for the given song in the playlist
    for (var i = 0; i < playlist.length; i++) {
        if (playlist[i] == src) {
            //Removes the song and its text from the queue
            playlist.splice(i, 1);
            var text = document.getElementById(src);
            text.parentNode.removeChild(text);
            
            //Shows empty queue text in case we've deleted the only remaining song
            if (playlist.length == 0) {
                showEmpty();
            }

            return;
        }
    }
}

//Clears the queue and stop playing current song
function clearQueue () {
    //Empties the playlist
    playlist = [];

    //Stops the audio
    audio.pause();
    audio.src = "";
    
    //Resets song index
    currentSong = -1;

    //Updates the queue div
    showList();
    showEmpty();
}

//Moves a given song up in the playlist
function moveUp (src) {
    for (var i = 0; i < playlist.length; i++) {
        if (playlist[i] == src) {
            //Performs a swap (if "i" is 0, then it swaps the first element with the last one)
            if (i == 0) {
                var temp = playlist[playlist.length - 1];
                playlist[playlist.length - 1] = playlist[0];
                playlist[0] = temp;
            }
            else {
                var temp = playlist[i - 1];
                playlist[i - 1] = playlist[i];
                playlist[i] = temp;
            }

            //Updates the list
            showList();
            
            //If we moved the song we're currently playing adjusts
            if (currentSong == i) {
                currentSong = (i - 1) == -1 ? playlist.length - 1 : i - 1;
            }
            else if (currentSong == i - 1) {
                currentSong = i;
            }
            
            highlightSong(playlist[currentSong]);
            return;
        }
    }
}

//Moves a given song down in the playlist
function moveDown (src) {
    for (var i = 0; i < playlist.length; i++) {
        if (playlist[i] == src) {
            //Performs a swap (if "i" is the last index, then it swaps the last element with the first one)
            if (i == playlist.length - 1) {
                var temp = playlist[playlist.length - 1];
                playlist[playlist.length - 1] = playlist[0];
                playlist[0] = temp;
            }
            else {
                var temp = playlist[i + 1];
                playlist[i + 1] = playlist[i];
                playlist[i] = temp;
            }
            
            //Updates the list
            showList();
            
            //If we moved the song we're currently playing adjusts
            if (currentSong == i) {
                currentSong = (i + 1) == playlist.length ? 0 : i + 1;
            }
            else if (currentSong == i + 1) {
                currentSong = i;
            }
            
            highlightSong(playlist[currentSong]);
            return;
        }
    }
}

//Jumps to a song in the playlist
function jumpToSong (src) {
    for (var i = 0; i < playlist.length; i++) {
        if (playlist[i] == src) {
            currentSong = i - 1;
            playerEnded();
        }
    }
}

//Play button pressed event
function playerPlay () {
    //Plays first playlist song if there is no source set
    if (audio.src == "") {
        if (playlist.length >= 1) {
            currentSong = 0;
            setSong(playlist[0]);
            highlightSong(playlist[0]);
        }
    }
}

//Player ended event
function playerEnded () {
    if (playlist.length > 0) {

        //Sets next song based on playMode
        switch (playMode) {
            case 0:
                currentSong++;
                break;
            case 1:
                currentSong = Math.floor(Math.random() * playlist.length);
                break;
            // -------------------------------------------------------------------------
            // We don't need case 2 even though playMode can take that value.
            // playMode=2 means repeat mode so currentSong would be the same as before,
            // therefore we don't need to change it
            // -------------------------------------------------------------------------
        }

        //Goes to the first song if currentSong is out of the list range
        if (currentSong >= playlist.length) {
            currentSong = 0;
        }

        //Starts the song
        setSong(playlist[currentSong]);
        highlightSong(playlist[currentSong]);
    }
}

//Changes the color of the current playing song
function highlightSong (song) {
    if (currentSongP != null) {
        currentSongP.style.color = "#FFFFFF";
    }

    currentSongP = document.getElementById(song);
    currentSongP.style.color = "#0099BB";
}

//Toggles the empty queue text
function showEmpty () {
    playlistDiv.innerHTML = "<p>No songs on queue</p>";
}

//Displays the playlist on the queue div
function showList () {
    //Clears the div
    playlistDiv.innerHTML = "";

    //Loops through all the songs in the playlist
    for (var i = 0; i < playlist.length; i++) {
        src = playlist[i];

        //Edits the string to get part we want to display from the source
        song = src.split("/")[src.split("/").length - 1];
        song = song.replace(".mp3", "");
    
        //Builds an html block for the song
        playlistDiv.innerHTML += 
        "<p style=\"padding-right:20px;\" id=\"" + src + "\">"
        + song
        + "<button onclick=\"removeFromPlaylist(" + "'" + src + "'" + ")\" class=\"playlistcontrol\"><i class=\"fas fa-times\"></i></button>"
        + "<button onclick=\"moveUp(" + "'" + src + "'" + ")\"             class=\"playlistcontrol\"><i class=\"fas fa-angle-up\"></i></button>"
        + "<button onclick=\"moveDown(" + "'" + src + "'" + ")\"           class=\"playlistcontrol\"><i class=\"fas fa-angle-down\"></i></button>"
        + "<button onclick=\"jumpToSong(" + "'" + src + "'" + ")\"         class=\"playlistcontrol\"><i class=\"fas fa-play\"></i></button>"
        + "</p>";
    }

    //Highlights the current song (I really shouldn't put that here but it's bug fixing)
    if (currentSong != -1) { 
        highlightSong(playlist[currentSong]);
    }
}