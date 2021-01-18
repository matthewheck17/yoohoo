<?php


    function search($array, $str){
        $indexes = array();
        foreach($array as $k => $v){
            //If stristr, add the index to our
            //$indexes array.
            if(strcmp($k, $str) == 0){
                $indexes[] = $k;
            }
        }
        return $indexes;
    }

    $shortcuts = array("y" => "yes", "n" => "no", "m" => "maybe", "l" => "later", "t" => "tomorrow");

    $searchString = $_GET['sc'];

    $matches = search($shortcuts, $searchString);

    $lastElement = end($matches);

    echo '{ "shortcuts": [';
    foreach($matches as $match){
        if ($match == $lastElement){
            echo '"' . $shortcuts[$match] . '"';
        } else {
            echo '"' . $shortcuts[$match] . '",';
        }
    }
    echo "]}";
?>
