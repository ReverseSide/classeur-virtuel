      <?php  

                    include '../include/bdd.php';
                   $date = date("y-m-d-H-i-s");
 
                 /*    $fichier_de_sauvegarde = 'semaine_sportive-'.$date.'.csv'; 

                   $handle = tmpfile();
                    $info = stream_get_meta_data($handle);

                    if($handle)
                        {
                            try
                            {
                                $ressource =$bdd->prepare("SELECT * FROM t_eleve JOIN t_sporthiver ON t_sporthiver.id_eleve=t_eleve.id_eleve JOIN t_classe ON t_classe.id_classe = t_eleve.idx_classe ORDER BY t_eleve.id_eleve");
                                $ressource->execute();


                                // PDO::FETCH_ASSOC pour récupérer le nom des colonnes avec array_keys
                                $first_line = $ressource->fetch(\PDO::FETCH_ASSOC);

                                // Pour indiquer les colonnes avec la première lettre en majuscule
                                $colonnes = array_map('ucfirst',array_keys($first_line));
                                fputcsv($handle, $colonnes, ';');
                                // Première ligne de données
                                fputcsv($handle, $first_line, ';');
                                
                                   
                                
                                
                                while ($line = $ressource->fetch(\PDO::FETCH_NUM)) 
                                {


                                  fputcsv($handle, $line, ';');
                                     
                                }
                               
                                $ressource->closeCursor();

                            }
                            catch(\PDOException $e)
                            {
                                fclose($handle);
                                exit("Erreur de sauvegarde du compteur. Avertir l'administrateur si le problème persiste");
                            }
                        }	







               $filesize = filesize($info['uri']);
    
               
                header("Content-Type: application/force-download; name=\"".$fichier_de_sauvegarde."\"");
                header('Content-type: text/csv; charset=UTF-8');
                header("Content-Transfer-Encoding: text/csv;");
                header("Content-Length: ".$filesize);
                header("Content-Disposition: attachment; filename=\"".$fichier_de_sauvegarde."\"");
                header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public");
                header("Pragma: no-cache"); 
                header("Expires: 0");
                $handle = iconv("UTF-8", "WINDOWS-1252", $handle);
                
                


                
              

                

                rewind($handle);
                fpassthru($handle);
                fclose($handle);
                exit;*/

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


                $zipName = "../tmp/export_". time() .".zip";
                $zip = new ZipArchive();
                $zip->open($zipName, ZipArchive::CREATE);








                $filename="semaine_sportive.csv";
               // $req=$bdd->prepare("SELECT * FROM t_eleve INNER JOIN t_sporthiver ON t_sporthiver.id_eleve=t_eleve.id_eleve JOIN t_classe ON t_classe.id_classe = t_eleve.idx_classe ORDER BY t_eleve.id_eleve");
                    $req=$bdd->prepare("SELECT ele_prenom as prenom, ele_nom as nom, ele_datedenaissance as Date_de_naissance,codeBarre as code_barre, (select cla_nom from t_classe where id_classe=t_eleve.idx_classe) as nom_classe,(select cla_joursSport from t_classe where id_classe=t_eleve.idx_classe) as jours_de_cours, choixSport as sport, materiel, coursESS, paiement, lieuxDepart, montant,ele_Rue as Rue, ele_npa as NPA, ele_localite as Ville, ele_numeromobile as Téléphone , num_car from t_sporthiver, t_eleve where t_sporthiver.id_eleve = t_eleve.id_eleve order by t_eleve.id_eleve");
                    $req->execute();

                    $test=$bdd->prepare('SELECT * from t_eleve');
                    $test->execute();
                    $test=$test->fetchAll();
                        
                
                


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

                          $result = ob_get_clean();
                         $zip->addFromString($out,$result);

                $zip->close();
                $zipContent = file_get_contents($zipName);
                unlink($zipName);

                // Retour de résultat
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Length: " . strlen($zipContent));
                header("Content-type: application/zip");
                header("Content-Disposition: attachment; filename=semaine_sportive_".$date.".zip");
                die($zipContent);

        ?>



