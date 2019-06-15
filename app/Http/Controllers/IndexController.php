<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Word;
use App\Index;
use Illuminate\Support\Facades\Input;
use App\Document;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function indexar(Request $request){
        $file = fopen(public_path()."\\vacias"."\\PalabrasVacias.txt", "r");
        $cadena="";
        while(!feof($file)) {
             $cadena = $cadena.fgets($file);
        }
        fclose($file);
        $palabrasVacias = $this->scanear_string($cadena);
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
        //dd($vaciasToken);
        //lectura del html
        $file = Input::file('archivo');
        $nameDoc = time().$file->getClientOriginalName();
            // $image->filePath = $name;
        $file->move(public_path() . '/archivos/', $nameDoc);

        $doc = new Document();
        $doc->name=$nameDoc;
        $doc->save();
        $doc_id=$doc->id;
        $url = "\\".$nameDoc;
        $html = file_get_contents(public_path().'\archivos' . $url);
        //dd($html);
        $texto=strip_tags($html);
        $texto=$this->scanear_string($texto);
        $texto=strtolower($texto);
        $texto=utf8_encode($texto);
        $textoLimpio = str_ireplace(array("\r","\n",'\r','\n',",",".","!","¡","?","¿",":",
        "-","#","$","%","&","/","(",")","=","\\","+",".","*","~","<",">","{","}","[","]",
        ";","^","`","_",'"',"|","'","1","2","3","4","5","6","7","8","9"),' ', $texto);

        $textoToken = explode(" ",$textoLimpio);
        foreach($textoToken as $key => $value){
            if(!empty($value)){
                $palabras[$key]=$value;
            }
        }
        //eliminar repetidos
        foreach($palabras as $key => $value){
            foreach($vaciasToken as $llave => $valor){
                if($value == $valor){
                    unset($palabras[$key]);
                }
            }
        }
        //dd($palabras);
        foreach($palabras as $key => $value){
            $consultaWord = Word::where('name',$value)->get();
            //echo "<br> --------------- Primera consulta palabrass ----------------------- <br>";
            //echo $consultaWord;
            //dd(sizeof($consultaWord));
            if(sizeof($consultaWord)==0){
                $word_id=Word::create([
                    "name" => $value
                ])->id;
                //echo "creacion con exito <br>";
                Index::create([
                    "word_id"       => $word_id,
                    "document_id"   => $doc_id,
                    "amount"        => 1
                ]);
            }else{
                //echo "<br> exite <br>";
                $word_id = $consultaWord->first()->id;
                //echo "<br>"+$word_id+"<br>";
                $consultaIndex = Index::where('word_id',$word_id)->Where('document_id',$doc_id)->get();
                if(sizeof($consultaIndex)==0){
                    $index_id=Index::create([
                        "word_id"       => $word_id,
                        "document_id"   => $doc_id,
                        "amount"        => 1
                    ])->id;
                }else{
                    //echo "<br> consulta Index <br>";
                    //echo $consultaIndex;
                    $consulta = $consultaIndex->first();
                    $cantidad = $consulta->amount +1;
                    $consulta->amount = $cantidad;
                    $consulta->save();
                    
                }
            }
        }
        //Session::flash('status_message',$status_message);
        return view('buscador.index');
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
}
