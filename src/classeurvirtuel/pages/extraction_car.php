 <?php  

            include '../include/bdd.php';
            $date = date("y-m-d-H-i-s");
            $jour=$_POST['jour_extra'];
            $car=$_POST['bus_extra'];
            
 
          
                $filename="".$car."-".$jour.".csv";

                $req=$bdd->prepare("SELECT concat (id_codebarre, '.jpg') as id_codebarre, ele_prenom as prenom, ele_nom as nom, codeBarre as code_barre, ele_numeromobile as Téléphone, num_car from t_sporthiver, t_eleve INNER JOIN t_classe ON t_eleve.idx_classe = t_classe.id_classe where t_sporthiver.id_eleve = t_eleve.id_eleve AND num_car='".$car."' AND (select cla_joursSport from t_classe where idx_classe = id_classe) ='".$jour."'");
                $req->execute();

                if (!function_exists("makeCSV"))
                {
                    function makeCSV($req){
                        $csv_terminated = "\n";
                        $csv_separator = ";";
                        $csv_enclosed = '';
                        $csv_escaped = "\\";

                        //recupération nom de colonne
                        foreach(range(0, $req->columnCount() - 1) as $column_index){
                            $nameCol[] = $req->getColumnMeta($column_index)['name'];
                        }
                        
                

                        //création ligne d'en tete
                        array_walk($nameCol, 'format', $csv_enclosed);
                        $out = implode(';', $nameCol).$csv_terminated;
                        
                         
                            
                        //création ligne d'enregistrement
                        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {

                            array_walk($row, 'format', $csv_enclosed); 
                            $out .= implode(';', $row).$csv_terminated;
                        }
                        return $out;
                    }

                    //fonction de formatage des cellules
                    function format(&$item,$key, $escaped){
                        $item = $escaped.addcslashes($item,$escaped).$escaped;
                    }
                }


                //creation du csv
                $out = makeCSV($req);
                $out = iconv("UTF-8", "WINDOWS-1252", $out);
                
                

                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Length: " . strlen($out));
                header("Content-type: text/x-csv;charset=WINDOWS-1252");
                header("Content-Disposition: attachment; filename=$filename");
                
                echo $out;

               
                header('Content-Type: text/csv; charset=utf-8');
                header("Content-Disposition: attachment; filename=car_".$date.".csv");
               

               
        ?>



