<?php

    /*
     * --- FROM ---
     * 
     * $_GET['from']
     * 
     * articles - artykuły
     * categories - kategorie
     * languages - języki
     * content - zawartość
     * 
     * --- FUNKCJE ---
     * 
     * $_GET['func']
     * 
     * getAll
     * 
     * --- ARGUMENTY ---
     * 
     * $_GET['args']
     * 
     */

    $appendix = "";

    $mysqli = new mysqli
    (
        'localhost',
        'root',
        '',
        'antonpedia'
    );

    $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='antonpedia';";

    $result = $mysqli -> query($query);

    $tables = array();

    while ($row = $result -> fetch_array()){
        $tables[] = $row[0];
    }

    // echo implode(",", $tables);

    if (isset($_GET['from']) && in_array($_GET['from'], $tables)){
        $from = $_GET['from'];

        if (isset($_GET['func'])){
            $func = $_GET['func'];
            
            if (isset($_GET['args'])){
                $str = $_GET['args'];
                $args = explode('^', $str); 

                for ($i = 0; $i < count($args); $i++){
                    $temp = explode(':', $args[$i]);
                    $assocTable[$temp[0]] = $temp[1];
                }            

                if(isset($assocTable['sort']) && isset($assocTable['by'])){
                    if ($assocTable['sort'] == 'asc' || $assocTable['sort'] == 'desc'){
                        $appendix =  $appendix." ORDER BY ".$assocTable['by']." ".$assocTable['sort'] ;
                    }    
                }

                if(isset($assocTable['limit'])){
                    if ($assocTable['limit'] > 0){
                        $appendix = $appendix." LIMIT ".$assocTable['limit'];
                    }    
                }
            }

            header("Content-Type: application/json; charset=UTF-8");

            switch ($func) { 
                case "getAll":
                    $result = $mysqli -> query ("SELECT * FROM ".$from." ".$appendix);
                    break;  

                default:
                    header("Content-Type: text/html; charset=UTF-8");
                    echo "To polecenie nie istnieje";
            }
        }

        else {
            $result = $mysqli -> query ("SELECT * FROM dane");
        }
    }

    else{
        echo "Podaj poprawną nazwę tabeli</br>";

        $result = null;
    }

    if ($result){
        echo json_encode($result -> fetch_all(MYSQLI_ASSOC));
    }

    else if (is_null($result)){

    }

    else {
        echo "Błąd połączenia";
    }

    $mysqli -> close(); 
?>