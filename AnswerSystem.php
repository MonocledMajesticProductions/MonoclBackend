<?php
require("MySQLConnectionFile.php");
$ConnectionFunction=ConnectionReturn();
//functiontosee if answer correct

function IsAnswerCorrect($AnswerGiven,$QuestionID,$UserID){
//checks if it is equal to the sacred string
//checks if the signifanct value reached
if(IsAnswerPreferedText($AnswerGiven,$QuestionID)){
    PointsAward(100,$UserID);
    AddAnswer($QuestionID,$AnswerGiven,$UserID,1);
   //echo "Is Prefered Text - 100 awarded";
    return true;
}
else{
    //echo $AnswerGiven;
    if(IsAnswerAboveSignifValue($AnswerGiven,$QuestionID)){
        PointsAward(50);
        AddAnswer($QuestionID,$AnswerGiven,$UserID,0);
        //echo "Is Above Significant value - 50 awarded";
        return true;
    }
    else{
        AddAnswer($QuestionID,$AnswerGiven,$UserID,0);
        return false;
    }

}
}
function IsAnswerPreferedText($AnswerGiven,$QuestionID){
    
   // echo "called2";
    $PreferredTextQuery = "SELECT * FROM questiontable WHERE `QuestionID` = '$QuestionID' AND `PreferredAnswer` = '$AnswerGiven';";
    $PreferredTextExecution = mysqli_query(ConnectionReturn(),$PreferredTextQuery);
    $numrows = mysqli_num_rows($PreferredTextExecution);
   // echo $numrows;
    if($numrows>=1){
        echo "true";
        return true;
}
else{
    mysqli_error(ConnectionReturn());
    echo  $PreferredTextQuery;
    echo false;
    return false;

}}
function addAnswer($QuestionID,$AnswerText,$UserID,$CorrectOrNot){
    global $ConnectionFunction;
    $AnswerToAddQuery = "INSERT INTO answertable (QuestionID,AnswerText,UserID,GeneralAvailibility,PointsStatus) VALUES ('$QuestionID','$AnswerText','$UserID',$CorrectOrNot,1)";
    $AnswerAddExecution =mysqli_query($ConnectionFunction,$AnswerToAddQuery);
    if($AnswerAddExecution){
        
    }
    else{
        
        echo mysqli_error($ConnectionFunction);
    }

}
function IsAnswerAboveSignifValue($TextToCheck,$QuestionID){
    $ConnectionFunctionPrimary = ConnectionReturn();
    $RetrievalOfSignifValue = "SELECT SignificantValue FROM questiontable WHERE QuestionID = $QuestionID";
    $SignificantValue =0;
    $CountSoFar=0;
    $SignificantValueExecution = mysqli_query($ConnectionFunctionPrimary,$RetrievalOfSignifValue);
    if($SignificantValueExecution){
    foreach($SignificantValueExecution as $row){
        $SignificantValue = $row["SignificantValue"];
    }
    $ConnectionFunctionSecondary = ConnectionReturnSecondaryTable();
    $TextStringArray = preg_split("/[^A-Za-z0-9]/", $TextToCheck);
    $SecondaryTableQuery = "SELECT * FROM `$QuestionID`";
    $SecondaryTableExecution = mysqli_query($ConnectionFunctionSecondary,$SecondaryTableQuery);
    if($SecondaryTableExecution){
        foreach($SecondaryTableExecution as $row){
            $NumberAssigned = $row["Percentage"];
            $itemselected = $row["MainWord"];
            if($NumberAssigned >= $SignificantValue){
                if(in_array($itemselected,$TextStringArray)){
                    if($SecondaryTableExecution["PreviousWord"]=="NULL"){
                        if($SecondaryTableExecution["FollowingWord"]=="NULL"){
                            //one word answer 
                            return true;
                        }
                        else{
                            //do nothing
                        }
                    }
                    else{
                        // do ntihing
                    }
                    //good
                }
                else{
                    //item required not present retrun false not good;
                    return false;
                }
            }
            else{
                if(in_array($itemselected.$TextStringArray)){
                    $CountSoFar = $CountSoFar +$NumberAssigned;
                    //do nothing
                }
                else{
                    //do nothing
                }
            }
        }
        $CountSoFar = $CountSoFar/count($TextStringArray);
        if($CountSoFar > $SignificantValue){
            return true;
        }
        else{
            return false;
        }
    
}
//points award method as common function in mysqlfile

}
else{
    msyqli_error($ConnectionFunctionSecondary);
}
}
?>