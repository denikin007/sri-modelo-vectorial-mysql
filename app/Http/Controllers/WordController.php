<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Word;
use App\Index;
use App\Document;

class WordController extends Controller
{ 
    public function vectorial($palabra){
        $palabra = $this->scanear_string($palabra);

        //palabras vacias
        $file = fopen(public_path()."\\vacias"."\\PalabrasVacias.txt", "r");
        $cadena="";
        while(!feof($file)) {
             $cadena = $cadena.fgets($file);
        }
        fclose($file);
        $palabrasVacias = $cadena;
        $palabrasVacias = strtolower($palabrasVacias);
        $palabrasVacias = utf8_encode($palabrasVacias);
        $palabrasVacias = str_ireplace(array("\r","\n",'\r','\n',",",".","!","¡","?","¿",":",
        "-","#","$","%","&","/","(",")","=","\\","+",".","*","~","<",">","{","}","[","]",";","^","`","_",'"',"|","'"),' ', $palabrasVacias);
        $palabrasVacias=explode(" ",$palabrasVacias);
        //eliminacion de espacios vacioso
        foreach($palabrasVacias as $key => $value){
            if(!empty($value)){
                $vaciasToken[$key]=$value;
            }
        }
        //proceso de busqueda
        $cadena = strtolower($palabra);
        $cadena=utf8_encode($cadena);
        $textoToken = explode(" ",$cadena);
        foreach($textoToken as $key => $value){
            if(!empty($value)){
                $palabras[$key]=$value;
            }
        }
        foreach($palabras as $key => $value){
            foreach($vaciasToken as $llave => $valor){
                if($value == $valor){
                    unset($palabras[$key]);
                }
            }
        }
        //dd($palabras);
        $coleccion = [];
        foreach($palabras as $key => $value){
            $word_id = Word::getIdByWord($palabras[$key]);
            //dd($word_id);
            if(!empty($word_id)){
                if($word_id == null ){
                    echo "palabras fallidas";
                }else{
                    $indices = Index::getAllIndexsByWordId($word_id->id);
                    foreach($indices as $indice){
                        $coleccion[$indice->document_id] = $indice->amount;
                    }
                }
            }
        }

        //dd($coleccion);

        $mapTF = [];
        foreach($palabras as $llave => $valor){
            $valores = [];
            foreach ($coleccion as $key => $value) {
                $valores[$key]='0';
            }
            $id = $palabras[$llave];
            $mapTF[$id] = $valores;
        }
        //construyento la matriz
        foreach ($mapTF as $llave => $valores) {
            //dd($llave);
            $word_id = Word::getIdByWord($llave);
            if(!empty($word_id)){
                $indices = Index::getAllIndexsByWordId($word_id->id);
            
                foreach($indices as $indice){
                    foreach($valores as $numero => $cantidad){
                        if($numero == $indice->document_id){
                            $valores[$numero] = $indice->amount;
                            $mapTF[$llave] = $valores;
                        }
                    }
                    
                }
            }

        }
        //dd($mapTF);
        $cantidadDoc = sizeof($coleccion);
        //calculando la frecuencia
        $mapFrecuencia = [];
        foreach ($mapTF as $llave => $valores) {
            $count=0;
            foreach($valores as $numero => $cantidad){
                if($cantidad > 0){
                    $count++;
                }
            }
            if($count != 0){
                $mapFrecuencia[$llave] = log($cantidadDoc/$count,10) ;
            }else{
                $mapFrecuencia[$llave] =0;
            }
        }
        //dd($mapFrecuencia);
        //matriz IDF
        $mapIDF = [];
        foreach ($mapTF as $llave => $valores) {
            $frecuencias=[];
            foreach($valores as $numero => $cantidad){
                foreach($mapFrecuencia as $palabra => $fre){
                    if($palabra == $llave){
                        $frecuencias[$numero] = $cantidad*$fre;
                        $mapIDF[$llave] = $frecuencias;
                    }
                }
            }
        }
        //dd($mapIDF);        

        //calculando las consultas
        $consultas=[];
        foreach($mapTF as $llave => $valores){
            foreach($palabras as $palabra){
                if($llave == $palabra){
                    $consultas[$llave]=0;
                }
            }
        }

        foreach($mapTF as $llave => $valores){
            foreach($palabras as $palabra){
                if($llave == $palabra){
                    $cantidad = $consultas[$llave]+1;
                    $consultas[$llave]=$cantidad;
                }
            }
        }
        $mapConsultaIDF=[];
        foreach($consultas as $palabra => $cantidad){
            foreach($mapFrecuencia as $llave => $fre){
                if($palabra == $llave){
                    $mapConsultaIDF[$llave] = $cantidad*$fre;
                }
            }
        }
        //dd($mapConsultaIDF);
        //calculando similutud por documento
        $simulitud=[];
        foreach($mapIDF as $llave => $valores){
            foreach($valores as $doc => $fre){
                $simulitud[$doc]=0;
            }
        }
        $array=[];
        foreach($coleccion as $doc_id => $cantidad){
            foreach($mapIDF as $llave => $valores){
                foreach($valores as $doc => $fre){
                    if($doc_id==$doc){
                        $cantidad = $simulitud[$doc_id]+$fre*$cantidad;
                        $simulitud[$doc_id]=$cantidad;
                        $document=Document::find($doc_id);
                        $simi = [
                            'id'        => $doc_id,
                            'cantidad'  => $cantidad,
                            'name_doc'  => $document->name,
                            'url'       => '\archivos\\'.$document->name
                        ];
                        if(in_array($simi,$array)){

                        }else{
                            array_push ( $array , $simi );
                            // dd($array);
                            // foreach ($array as $key => $value) {
                            //     dd($value);
                            //     if($value['id']==$doc_id){
                            //         //array_replace($array,$simi);
                            //     }
                            // }
                        }
                    }
                }
                
            }
            // foreach($valores as $doc => $fre){
            //     $simulitud[$doc]=$consulta[$llave];
            // }
        }
        // dd($simulitud);
        $this->array_sort_by($array, 'cantidad', $order = SORT_DESC);
        // dd($consulta);
        return $array;
    }


    public function scanear_string($string){
    
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
    
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
    
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
    
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
    
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
    
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
    
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", '"',
                "·", '$', '%', '&', '/',
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                ".", " "),
            ' ',
            $string
        );
 
 
    return $string;
    }

    //para ordenar
    public function array_sort_by(&$arrIni, $col, $order = SORT_ASC){
        $arrAux = array();
        foreach ($arrIni as $key=> $row)
        {
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
        }
        array_multisort($arrAux, $order, $arrIni);
    }
}
