<?php

    /*
     * --- FROM ---
     * 
     * $_GET['from']
     * 
     * a - artykuły
     * ca - kategorie
     * l - języki
     * co - zawartość
     * 
     * --- FUNCTIONS ---
     * 
     * $_GET['func']
     * 
     * getAll
     * 
     * --- AGRUMENTS ---
     * 
     * $_GET['args']
     * 
     * limit
     * 
     * sort
     *  - by
     * 
     * --- SYNTAX ---
     * 
     * : - before arg value (limit:2)
     * ^ - between args (sort:asc^by:id)
     * 
     */

    $mysqli = new mysqli('localhost','root','','antonpedia');

    $appendix = "";

    if (isset($_GET['from']) && is_string($from = isTableSelectedProperly($mysqli, $_GET['from']))){
        if (isset($_GET['func'])){
            header("Content-Type: application/json; charset=UTF-8");

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

            switch ($func) { 
                case "getAll":
                    $result = $mysqli -> query("SELECT * FROM ".$from." ".$appendix);
                    break;  

                default:
                    header("Content-Type: text/html; charset=UTF-8");
                    echo "To polecenie nie istnieje";
            }
        }

        else {
            echo "Należy wybrać funkcję (func=...)</br>";

            $result = null;
        }
    }

    else{
        echo "Należy wybrać tabelę (from=...)</br>";

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

    function isTableSelectedProperly($mysqli, $selectedTable){
        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='antonpedia';";

        $result = $mysqli -> query($query);

        $tables = array();

        while ($row = $result -> fetch_array()){
            $tables[] = $row[0];
        }

        foreach ($tables as $t){
            if (strpos($t, $selectedTable) === 0){
                return $t;
            }
        }

        return $tables;
    }
?>