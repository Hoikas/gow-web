<?php
// Node types
define('kNodePlayer', 2);
define('kNodePlayerInfo', 23);
define('kNodeAgeInfo', 33);

function ds_running()
{
    $output = exec('ps -C dirtsand');
    return strpos($output, 'dirtsand') !== false;
}

function get_avatar_count($pq)
{
    $res = pg_query($pq, 'SELECT COUNT(*) FROM auth."Players"');
    $count = pg_fetch_result($res, 0, 0);
    pg_free_result($res);
    return $count;
}

function get_avatar_name($pq, $ki)
{
    $res = pg_query($pq, "SELECT \"IString64_1\" FROM vault.\"Nodes\" WHERE idx={$ki} AND \"NodeType\"=".kNodePlayer);
    if (pg_num_rows($res) > 0) {
        $name = pg_fetch_result($res, 0, 0);
    } else {
        $name = false;
    }
    pg_free_result($res);
    return $name;
}

function get_lake_score($pq)
{
    $res = pg_query($pq, 'SELECT "Points" FROM auth."Scores" WHERE "Name"=\'LakeScore\'');
    $score = pg_fetch_result($res, 0, 0);
    pg_free_result($res);
    return $score;
}

function get_top_pellet_droppers($pq)
{
    $res = pg_query($pq, 'SELECT "Name","OwnerIdx","Points" FROM auth."Scores" WHERE "Name"=\'PelletTotal\' OR "Name"=\'PelletDrop\' ORDER BY "Points" DESC');
    $results = array('hoods' => array(), 'players' => array());
    $numPlayers = $numHoods = 0;
    for ($i = 0; $i < pg_num_rows($res); $i++)
    {
        // hardcoded max for now
        if ($numPlayers == 10 && $numHoods == 10)
        {
            break;
        }
        $idx = pg_fetch_result($res, $i, '"OwnerIdx"');
        $name = pg_fetch_result($res, $i, '"Name"');
        $points = pg_fetch_result($res, $i, '"Points"');
        $typeRes = pg_query($pq, 'SELECT "NodeType","Int32_1","String64_4","String64_3","IString64_1" FROM vault."Nodes" WHERE idx='.$idx);
        $nodeType = pg_fetch_result($typeRes, 0, '"NodeType"');

        if ($nodeType == kNodePlayerInfo && $numPlayers < 10 && $name != 'PelletDrop')
        {
            $player = pg_fetch_result($typeRes, 0, '"IString64_1"');
            $results['players'][] = array($player, $points);
            $numPlayers++;
        }
        else if ($nodeType == kNodeAgeInfo && $numHoods < 10)
        {
            $userDefName = pg_fetch_result($typeRes, 0, '"String64_4"');
            $instanceName = pg_fetch_result($typeRes, 0, '"String64_3"');
            $seqId = pg_fetch_result($typeRes, 0, '"Int32_1"');
            if ($seqId == 0 || $seqId == "0")
            {
                $hoodName = sprintf('%s %s', $userDefName, $instanceName);
            }
            else
            {
                $hoodName = sprintf('%s (%d) %s', $userDefName, $seqId, $instanceName);
            }
            $results['hoods'][] = array($hoodName, $points);
            $numHoods++;
        }
        pg_free_result($typeRes);
    }
    pg_free_result($res);
    return $results;
}

function get_online_players($pq)
{
    $res = pg_query($pq, 'SELECT "IString64_1","String64_1" FROM vault."Nodes" WHERE "NodeType"='.kNodePlayerInfo.' AND "Int32_1"=1');
    $assoc = array();
    while ($row = pg_fetch_row($res)) {
        $assoc[] = array($row[0], $row[1]);
    }
    pg_free_result($res);
    return $assoc;
}
?>