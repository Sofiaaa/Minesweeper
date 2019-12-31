<?php
define('ROWS', 9);
define('COL', 9);
define('MINES', 15);



define ('O_MINE','B');  //ανιχτή βόμβα
define ('MINE','c'); //ορίζω c την κλειστή βόμβα γιατί το css δεν είναι case sensitive και θα μπερδεύει τις 2 κλάσεις αν βαλω b
define ('CLOSED','_');  //κλειστό άδειο κελί 
define ('OPEN',' ');    //ανοιχτό άδειο

define('START','Game hasn\'t started yet');
define('PLAY','Game is in progress');
define('WIN','You won!');
define('LOOSE','You lost!');

//--------------------------------------------------------------------------------------------------------------------------

$ar = createMinefield(); 
showField($ar);
$status=fieldStatus($ar); 
echo $status;       //δείχνει την κατάσταση του παιχνιδιού στην αρχή και μετά από κάθε κίνηση

$ar=openAllCell($ar,3,6);  //ανοίγει το δεδομένο κελί και τα γύρω αν δεν υπάρχει ούτε μια νάρκη δίπλα του 
showField($ar);
$status=fieldStatus($ar);
echo $status;

if ($status!==LOOSE){   //αν ο παίκτης δεν έχει χάσει το παιχνίδι συνεχίζεται
    $ar=openCell($ar,7,3);  //ανοίγει το δεδομένο κελί
    showField($ar);
    $status=fieldStatus($ar);
    echo $status;
}

if ($status!==LOOSE){   
    $ar=openAllCell($ar,9,9); 
    showField($ar);
    $status=fieldStatus($ar);
    echo $status;
}

//---------------------------------------------------------------------------------------------------------------------------

function createMinefield() {

    $field = array_fill(0, ROWS, 0);

    for ($r = 0; $r < ROWS; $r++) {                     
        $field[$r] = array_fill(0, COL, CLOSED);        
    }
    $i = 0;
    while ($i < MINES) {
        $pos_r = rand(0, ROWS - 1);
        $pos_c = rand(0, COL - 1);
        if ($field[$pos_r][$pos_c] !== MINE) {
            $field[$pos_r][$pos_c] = MINE;
            $i++;
        }
    }
    return $field;
}

function rowCount($field) {
    return count($field);           //επιστρέφει τον αριθμό των σειρών 
}

function columnCount($field) {
    if (count($field) > 0) {
        return count($field[0]);        //επιστρέφει τον αριθμό των στηλών για την πρώτη σειρά
    }
}


function countMines($field,$r,$c){
    $rmax=rowCount($field)-1;
    $cmax=columnCount($field)-1;
    $counter=0;

   if ($r>0){
        if ($field[$r-1][$c]===MINE) // ελέγχει το πάνω κελί
            $counter++;
   }

   if ($r<$rmax){
        if($field[$r+1][$c]===MINE) // το κάτω κελί 
            $counter++;
   }

   if ($c>0){
       if ($field[$r][$c-1]===MINE) // αριστερό κελί 
            $counter++;
        if ($r>0){
            if ($field[$r-1][$c-1]===MINE)  //αριστερό επάνω κελί 
                $counter++;
        }
        if ($r<$rmax){
            if($field[$r+1][$c-1]===MINE) // αριστερό κάτω
                $counter++;
       }
   }

   if($c<$cmax){
        if ($field[$r][$c+1]===MINE) //δεξί 
            $counter++;
            if ($r>0){
                if ($field[$r-1][$c+1]===MINE) //δεξί επάνω 
                $counter++;
            }
            if ($r<$rmax){
                if($field[$r+1][$c+1]===MINE) //δεξί κάτω
                    $counter++;
           }
   }
   return $counter;
}


function cellContent($field,$r,$c){
    return  $field[$r][$c];
}



function openCell($field,$r,$c){
    if ($field[$r][$c]===MINE){     //αν είναι κλειστό κελί με βόμβα επέστρεψε ανοιχτό κελί με βόμβα
        $field[$r][$c]=O_MINE;
    }else if ($field[$r][$c]===CLOSED){     //αν είναι κλειστό άδειο επέστρεψε ανοιχτό άδειο
        $field[$r][$c]=OPEN;
    }
    return $field;
}

function openAllCell($field,$r,$c,$counter=0){
    $rmax=rowCount($field)-1;               //τα όρια του πίνακα
    $cmax=columnCount($field)-1;
            switch ($field[$r][$c]){        
                case OPEN:      //αν πατήσει κελί ανοιχτό δε γίνεται κάτι 
                    break;

                case MINE:
                    $field[$r][$c]=O_MINE;          //αν πατήσει κλειστό με βόμβα γίνεται ανοιχτό με βόμβα
                    break;
                
                case CLOSED:                            //αν είναι κλειστό  χωρίς βόμβα 
                    $field[$r][$c]=OPEN;                //το ανοίγει
                    if ($counter===0 && countMines($field,$r,$c)===0){    //αν ο counter είναι μηδέν γίνεται αναδρομή
                        if($r>0)                                           //και αν γύρω δεν υπάρχουν βόμβες
                            $field=openAllCell($field,$r-1,$c,1);           //ανοίγει τα κλειστά γύρω κελιά και με counter=1 η αναδρομή σταματά
                        if ($r<$rmax)
                            $field=openAllCell($field,$r+1,$c,1);
                        if ($c>0)
                            $field=openAllCell($field,$r,$c-1,1);
                        if ($c<$cmax)
                            $field=openAllCell($field,$r,$c+1,1);
                        if ($r>0 && $c>0)
                            $field=openAllCell($field,$r-1,$c-1,1);
                        if ($r>0 && $c<$cmax)
                            $field=openAllCell($field,$r-1,$c+1,1);
                        if ($r<$rmax && $c>0)
                            $field=openAllCell($field,$r+1,$c-1,1);
                        if ($r<$rmax && $c<$cmax)
                            $field=openAllCell($field,$r+1,$c+1,1);
                    }
            }
    return $field;

}

function fieldStatus($field){
    $rNum=rowCount($field);
    $cNum=columnCount($field);
    for ($r=0;$r<$rNum;$r++){
        for($c=0;$c<$cNum;$c++){
            if($field[$r][$c]===O_MINE){        //αν υπάρχει ανοιχτή βόμβα ο παίχτης έχασε
                return LOOSE;
            }
        }
    }
    
    for ($r=0;$r<$rNum;$r++){
        for($c=0;$c<$cNum;$c++){
            if($field[$r][$c]===OPEN){                      //αν υπάρχει κάποιο ανοιχτό χωρίς βόμβα κελί 
                for ($r=0;$r<$rNum;$r++){
                    for($c=0;$c<$cNum;$c++){
                        if($field[$r][$c]===CLOSED){        //αλλά και κάποιο κλειστό χωρίς βόμβα, το παιχνίδι συνεχίζεται
                            return PLAY;
                        }
                    }
                }
                return WIN;             // αν δεν υπάρχει κλειστο χωρίς βόμβα κέρδισε 
        
            }
        }
    }
   return START;        //αν δεν υπάρχει κανένα ανοιχτό κελί τοπαιχνίδι είναι στην αρχή
    
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
               
  

         table{
            background: rgb(204, 204, 204);
            border-style:solid;
            border-width: 3px;
            border-top-color:rgb(104,104,104);
            border-left-color: rgb(104,104,104);
            border-right-color:white;
            border-bottom-color: white;
            border-spacing:0.001px;
            }
       

        td{
            width:4vh;
            height:4vh;
           
            border:0.2vh solid rgb(104,104,104);
           
          
        }
        td.c,td._{                                              /* κλειστό κελί με βόμβα και χωρίς */
            border-style:solid;
            border-width: 0.4vh;
            border-top-color:white;
            border-left-color: white;
            border-right-color:rgb(104,104,104);
            border-bottom-color: rgb(104,104,104);
          
            color:rgb(204, 204, 204);
        }
        td.B{                                              /* ανοιχτό με βόμβα */
            background-image:url(mine.png);
            background-repeat: no-repeat;
            background-position:center;
            background-size: 2vh;
        }
       
    </style>

</head>            
<body>
        <?php
        function showField($field) {
    echo '<br><br><div >
    <table><tbody>';
    for ($r = 0; $r < ROWS; $r++) {
        echo '<tr>';
        for ($c = 0; $c < COL; $c++)
            echo '<td class="'.$field[$r][$c].'"></td>';    //το περιεχόμενο κάθε κελιού του php πίνακα αντιστοιχεί σε μία κλάση 
        echo '</tr>';                                       
    }
    echo '</tbody></table>
    </div> '; }?>
    </div>
</body>
