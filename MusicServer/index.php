<html>

  <head>
    <title>Spotify DIY</title>
    <meta charset = "UTF-8"></meta>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <script src="src/jQuerry.js" type="text/javascript"></script>
    <script src="src/php.php" type="text/php"></script>
  </head>

  <body>
    <?php
      $images = glob("Headers/*");
      $i = rand(0, count($images) - 1);

      echo "<img class=\"topimage\" src=\"$images[$i]\"></img>";
    ?>

    <div class="topnav">
      <button class="tabbutton" onclick="openTab(event, 'AllSongs')" id="defaultTab">All Songs</button>
      <button class="tabbutton" onclick="openTab(event, 'Artists')">Artists</button>

      <div class="search">
          <input id="searchBar" type="text" name="search" placeholder="Search..."></input>
          <button id= "searchButton" onclick="searchButton()"><i class="fa fa-search"></i></button>
      </div>
    </div>

    <div id="AllSongs" class="tabcontent">
      <button onclick="setRandomSong()" class="listcontrol">Random Song</button>
    </div>

    <div id="Artists" class="tabcontent">
    </div>

    <div id="ArtistsBack" class="tabcontent">
      <button onclick="artistsBack()" class="artistsBackButton"><i class="fa fa-chevron-left"></i></button>
      <button onclick="playAll()" class="playAllButton">Play All</button>
    </div>

    <div id="SongList" class="songlist">
    </div>

    <div id="Queue" class="queue" style="display:none">

      <div class="queuetop">
        <button onclick="clearQueue()" class="listcontrol">Clear Queue</button>
        <button onclick="changeMode()" class="listcontrol"><i id ="ModeButton" class="fa fa-list"></i></button>
      </div>

      <div id="QueueInner" style="padding-left: 20px;">
        <p id="QueueEmpty">No songs on queue</p>
      </div>
    </div>
    <div id="QueueShow" class="queueshow">
        <button onclick="toggleQueue()" class="queueshowbutton"><i id="queueButtonIcon" class="fa fa-chevron-up"></i></button>
        <script>
          function toggleQueue () {
            var display = document.getElementById("Queue").style.display;
            document.getElementById("Queue").style.display = display == "none" ? "block" : "none";
            document.getElementById("queueButtonIcon").className = display == "none" ? "fa fa-chevron-down" : "fa fa-chevron-up";
          }
        </script>

        <audio controls id="MusicPlayer" class="musicplayer"
          onended="playerEnded()"
          onplay="playerPlay()"
        ></audio>
    </div>

    <script type="text/javascript">
      var search = "";
      var artist = "";

      //Calls the showList function in php.php and outputs to the SongList div
      function showListAJAX () {
          search_ = "";

          if (search != "") {
            search_ += search;
          }
          if (artist != "") {
            if (search != "") {
              search_ += " ";
            }
            search_ += artist;
          }


          //Artist search, I'm basically putting
          if (document.getElementById("Artists").style.display == "block") {
            $.ajax({
              url: "php.php",
              type: "POST",
              data: { action:"showArtist", search: search_ },
              success: function(output) {
                  document.getElementById("Artists").innerHTML = output;
              }
            });
          }

          //Calls ajax
          $.ajax({
            url: "php.php",
            type: "POST",
            data: { action:"showList", search: search_ },
            success: function(output) {
                document.getElementById("SongList").innerHTML = output;
            }
          });
      }
    </script>

    <div id="AllSongs" class="tabcontent">
      <script type="text/javascript">
        document.getElementById("searchBar").addEventListener("keyup", function (event) {
          event.preventDefault();

          //Simulates click on search button if we press enter
          if (event.keyCode == 13) {
            document.getElementById("searchButton").click();
          }

        });

        showListAJAX("");

        function searchButton () {
          search = document.getElementById("searchBar").value;

          showListAJAX();
        }
      </script>
    </div>


    <script type="text/javascript">
      //------TAB-MANAGER------\\

      //Opens a tab
      function openTab(evt, tab) {
          var i, tabcontent, tabbuttons;

          document.getElementById("SongList").style.display = "none";

          //Hides all the tabs
          tabcontent = document.getElementsByClassName("tabcontent");
          for (i = 0; i < tabcontent.length; i++) {
              tabcontent[i].style.display = "none";
          }

          tabbuttons = document.getElementsByClassName("tabbutton");
          for (i = 0; i < tabbuttons.length; i++) {
              tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
          }

          //Stops the function if we requested to hide all tabs
          if (tab == "none") { return; }

          //Shows the tab we clicked on
          document.getElementById(tab).style.display = "block";
          evt.currentTarget.className += " active";

          if (tab == "Artists") {
            searchButton();
          }

          if (tab == "AllSongs") {
            artist = "";
            searchButton();
            document.getElementById("SongList").style.display = "block";
          }

      }

      //Selects the default tab
      document.getElementById("defaultTab").click();

      //----------------------------------------------\\

      //Opens an artist's folder
      function openArtist (evt, artist_) {
          document.getElementById("Artists").style.display = "none";
          document.getElementById("ArtistsBack").style.display = "block";
          document.getElementById("SongList").style.display = "block";

          artist = artist_;
          showListAJAX();
      }

      //Exits an artist's folder
      function artistsBack () {
          document.getElementById("Artists").style.display = "block";
          document.getElementById("ArtistsBack").style.display = "none";
          document.getElementById("SongList").style.display = "none";

          artist = "";
          searchButton();
      }

      //Calls the php download file
      function download (file_) {
        window.open(document.URL + "download.php?file=" + file_);
      }

      //Starts playing a random song
      function setRandomSong () {
        var songs = document.getElementsByClassName("songdiv");
        var i = Math.floor(Math.random() * songs.length);

        setSong(songs[i].id.substr(0, songs[i].id.length - 4));
      }

    </script>
    <script src="src/musicPlayer.js" type="text/javascript"></script>

  </body>

</html>
