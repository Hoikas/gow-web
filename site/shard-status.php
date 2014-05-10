<?php
// Reqs
require './ds-config.ini.php';
require './ds-functions.php';

// Shard messages
define('kShardIsRunning', 'Welcome to the Gehn Shard!');
define('kShardIsDown', 'The Gehn Shard is currently down. Please check back later!');

// JSON Stuff
define('kShardStatusKey', 'status');
define('kShardIconKey', 'icon');
define('kShardMessageKey', 'message');
define('kShardPlayerCountKey', 'explorers');
define('kShardOnlineValue', 'online');
define('kShardOfflineValue', 'offline');
define('kShardIconGreen', 'http://www.guildofwriters.org/images/bullet_green.png');
define('kShardIconRed', 'http://www.guildofwriters.org/images/bullet_red.png');

function echo_online_count()
{
    global $pq; // Hacky...

    $count = count(get_online_players($pq));
    if ($count == 0) {
        echo "There are currently no explorers in the cavern.";
    } else if ($count == 1) {
        echo "There is currently one explorer in the cavern.";
    } else {
         echo "There are currently {$count} explorers in the cavern.";
    }
}

// What kind of shit do they want?
if (isset($_GET['type'])) {
    // Hope you know what you're doing
    $output = strtolower($_GET['type']);
} else {
    // Just output the stupid string for plUruLauncher (that troll)
    $output = 'raw';
}

// Prep a pq connection
// You'll want to specify this stuff in ds-config.ini.php
$pq = pg_connect("host={$dbhost} port={$dbport} dbname={$dbname} user={$dbuser} password={$dbpass}");

// Now do some output
if ($output == 'json') {
    // I love how simple this is!
    $online = ds_running();
    $array = array
    (
            kShardStatusKey         =>      $online ? kShardOnlineValue : kShardOfflineValue,
            kShardIconKey           =>      $online ? kShardIconGreen : kShardIconRed,
            kShardMessageKey        =>      $online ? kShardIsRunning : kShardIsDown,
            kShardPlayerCountKey    =>      $online ? get_online_player_count($pq) : 0,
    );
    echo json_encode($array);
} else if ($output == 'pretty' || $output == 'fancy') {
    $online = ds_running();
    $img = $online ? kShardIconGreen : kShardIconRed;
    $msg = $online ? kShardIsRunning : kShardIsDown;
    echo '<!-- FamFamFam Silk Icons: http://www.famfamfam.com/lab/icons/silk -->';
    echo "<img id=\"gehn-shard-status\" src=\"{$img}\" /> {$msg}";
    echo '<br />';
    echo_online_count();
} else if ($output == 'stats') {
    $avatars = get_avatar_count($pq);
    $online = get_online_players($pq);
    $lakeScore = get_lake_score($pq);
    $turds = get_top_pellet_droppers($pq);
    ?>
    <h2>Gehn Shard Statistics</h2>
    <ul>
        <li>
            <strong>Players</strong>: <?php echo count($online); ?> of <?php echo $avatars; ?> players in the cavern.
            <ul>
                <?php
                foreach ($online as $i)
                {
                    printf('<li><strong>%s</strong> in <em>%s</em></li>', $i[0], $i[1]);
                }
                ?>
            </ul>
        </li>
        <li><strong>Lake Score</strong>: <?php echo $lakeScore; ?></li>
        <li>
            <strong>Top Player Pellet Scores</strong>
            <ol>
                <?php
                foreach ($turds['players'] as $i)
                {
                    printf('<li>%s: %d</li>', $i[0], $i[1]);
                }
                ?>
            </ol>
        </li>
        <li>
            <strong>Top Neighborhood Pellet Scores</strong>
            <ol>
                <?php
                foreach ($turds['hoods'] as $i)
                {
                    printf('<li>%s: %d</li>', $i[0], $i[1]);
                }
                ?>
            </ol>
        </li>
    </ul>
    <?php
} else if ($output == 'help') {
    ?>
    <p>Thanks for your interest in the <strong>Gehn</strong> Shard status utility!</p>
    <p>
        You can get multiple kinds of output from this script by changing the <i>type</i>
        parameter. Following is a list of supported output types.
        <ul>
            <li>
                <strong>fancy</strong><br />
                This outputs a a "fancy" status indicator in HTML that includes a status icon (either green or red)
                and the current status message as shown by the launcher. The HTML is very minimal, and you are free to
                encaspulate it however you need. The icon image will work with <i>img#gehn-shard-status</i> CSS blocks.
            </li>
            
            <li>
                <strong>JSON</strong><br />
                This outputs a JSON representation of the current shard <i>status</i> (<i>online</i> or <i>offline</i>),
                the number of <i>explorers</i> online,  the status <i>icon</i> (as a URL), and the <i>message</i> shown in the launcher.
            </li>
            
            <li>
                <strong>raw</strong><br />
                This outputs the status string shown by the launcher with no additional formatting.
            </li>
        </ul>
    </p>
    <?php
} else {
    if (ds_running()) {
        echo kShardIsRunning;
        echo PHP_EOL;
        echo_online_count();
    } else {
        echo kShardIsDown;
    }
}

// Close down pq
pg_close($pq);
?>