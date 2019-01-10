<?php namespace Controllers\Api;

$GLOBALS['DB'] = new \mysqli(env('DB_HOST'),env('DB_USER'),env('DB_PASS'),env('DB_NAME'));
mysqli_set_charset($GLOBALS['DB'],"utf8");

class DBHelper {
public function GetTable($TableName,$extra ="")
{
   $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    return $DB->query("SELECT * FROM  `$TableName`" . $extra);
     $sql->execute();
    return $sql->get_result();
}

public function hashPassword($pass){
    return sha1($pass);
}
public function JsonHeader($a)
{
   header('Content-Type: text/json');
   die(json_encode($a));
}
public function IsImage($Image)
{
return exif_imagetype($Image);
}
public function Resize($src, $desired_width, $destination=""){
  list($Width, $height, $imageFileType, $attr) = getimagesize($src);

	/* read the source image */
  if($imageFileType == IMAGETYPE_JPEG ){
    $source_image = imagecreatefromjpeg($src);
  }else if($imageFileType== IMAGETYPE_PNG){
    $source_image = imagecreatefrompng($src);
  }else if($imageFileType== IMAGETYPE_GIF ){
    $source_image = imagecreatefromgif($src);
  }
	$width = imagesx($source_image);
	$height = imagesy($source_image);

	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height * ($desired_width / $width));

	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);

	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

	/* create the physical thumbnail image to its destination */
  $destination = !empty($destination)?$destination:$src;
	imagejpeg($virtual_image, $destination);
}
public function crop($src, array $rect)
{
    $dest = imagecreatetruecolor($rect['width'], $rect['height']);
    imagecopy(
        $dest,
        $src,
        0,
        0,
        $rect['x'],
        $rect['y'],
        $rect['width'],
        $rect['height']
    );

    return $dest;
}
public function GetImgeType($path)
{
  $arr = [IMAGETYPE_GIF,
         IMAGETYPE_JPEG,
         IMAGETYPE_PNG,
         IMAGETYPE_SWF,
         IMAGETYPE_PSD,
         IMAGETYPE_BMP,
         IMAGETYPE_TIFF_II,
         IMAGETYPE_TIFF_MM,
         IMAGETYPE_JPC,
         IMAGETYPE_JP2,
         IMAGETYPE_JPX,
         IMAGETYPE_JB2,
         IMAGETYPE_SWC,
         IMAGETYPE_IFF,
         IMAGETYPE_WBMP,
         IMAGETYPE_XBM,
         IMAGETYPE_ICO,
         IMAGETYPE_WEBP];
   $results = exif_imagetype($path);
   return $arr[$results-1];
}
public function CutImage($target_file,$w=-1,$h=-1)
{
  try{
  list($Width, $height, $imageFileType, $attr) = getimagesize($target_file);
      if ($w==-1 && $h==-1) {
         if ($Width>$height) {
           $w=$height;
           $h=$height;
         }else {
           $w=$Width;
           $h=$Width;
         }
      }
  if($imageFileType == IMAGETYPE_JPEG ){
    $im = imagecreatefromjpeg($target_file);
  }else if($imageFileType== IMAGETYPE_PNG){
    $im = imagecreatefrompng($target_file);
  }else if($imageFileType== IMAGETYPE_GIF ){
    $im = imagecreatefromgif($target_file);
  }
  $im2 = $this->crop($im, ['x' => 0, 'y' => 0, 'width' => $w,
   'height' => $h]);
  if ($im2 !== FALSE) {
   return imagejpeg($im2, $target_file);
 }else {
   return false;
 }
 }catch(Exception $e){
   return false;
 }
}
public function JoindTablesNoRepeat($KeysTable,$SearchTable,$CulumnsKeysTable,$JoinKey,$JoinValue,$key,$value,$Extra = "")
{
  //DISTINCT
      $DB = $GLOBALS['DB'];
      $KeysTable  = mysqli_escape_string($DB,strtolower($KeysTable));
      $SearchTable  = mysqli_escape_string($DB,strtolower($SearchTable));
      $JoinKey  = mysqli_escape_string($DB,$JoinKey);
      $JoinValue  = mysqli_escape_string($DB,$JoinValue);
      $key  = mysqli_escape_string($DB,$key);
      $value  = mysqli_escape_string($DB,$value);
      $culomns = '';
      for ($i=0; $i < count($CulumnsKeysTable); $i++) {
          $ThisClumnName = mysqli_escape_string($DB,$CulumnsKeysTable[$i]);
          if($i == (count($CulumnsKeysTable)-1)){
            if(!empty($culomns) && $i == 0){
              $culomns .= ", `$KeysTable`.`$ThisClumnName` ";
            }else{
              $culomns .= " `$KeysTable`.`$ThisClumnName` ";
            }
          }else if(!empty($culomns) && $i == 0){
            $culomns .= " , `$KeysTable`.`$ThisClumnName` ";
          }else {
            $culomns .= " `$KeysTable`.`$ThisClumnName` , ";
          }
      }
      $cmd = "SELECT DISTINCT $culomns FROM `$SearchTable` INNER
      JOIN `$KeysTable` ON `$KeysTable`.`$JoinKey` =  `$SearchTable`.`$JoinValue` WHERE  `$KeysTable`.`$key` =  '$value'" . $Extra;
      $cmd = str_replace("`*`","*",$cmd);
       return $DB->query($cmd);
      $sql->execute();
      return  $sql->get_result();
}
public function JoindTables($KeysTable,$SearchTable,$CulumnsSerachTable,$CulumnsKeysTable,$JoinKey,$JoinValue,$key,$value,$Extra = "")
{
    $DB = $GLOBALS['DB'];
  $KeysTable  = mysqli_escape_string($DB,strtolower($KeysTable));
  $SearchTable  = mysqli_escape_string($DB,strtolower($SearchTable));
  $JoinKey  = mysqli_escape_string($DB,$JoinKey);
  $JoinValue  = mysqli_escape_string($DB,$JoinValue);
  $key  = mysqli_escape_string($DB,$key);
  $value  = mysqli_escape_string($DB,$value);
  $culomns = '';
  for ($i=0; $i < count($CulumnsSerachTable); $i++) {
        $ThisClumnName = mysqli_escape_string($DB,$CulumnsSerachTable[$i]);
        if($i ==(count($CulumnsSerachTable)-1)){
        $culomns .= " `$SearchTable`.`$ThisClumnName` ";
        }else {
        $culomns .= " `$SearchTable`.`$ThisClumnName` , ";
        }
  }
  for ($i=0; $i < count($CulumnsKeysTable); $i++) {
        $ThisClumnName = mysqli_escape_string($DB,$CulumnsKeysTable[$i]);
        if($i == (count($CulumnsKeysTable)-1)){
          if(!empty($culomns) && $i == 0){
            $culomns .= ", `$KeysTable`.`$ThisClumnName` ";
          }else{
            $culomns .= " `$KeysTable`.`$ThisClumnName` ";
          }
        }else if(!empty($culomns) && $i == 0){
          $culomns .= " , `$KeysTable`.`$ThisClumnName` ";
        }else {
          $culomns .= " `$KeysTable`.`$ThisClumnName` , ";
        }
  }
  $cmd = "SELECT $culomns FROM `$SearchTable` INNER
    JOIN `$KeysTable` ON `$KeysTable`.`$JoinKey` =  `$SearchTable`.`$JoinValue` WHERE  `$KeysTable`.`$key` =  '$value'" . $Extra;
  $cmd = str_replace("`*`","*",$cmd);
  return $DB->query($cmd);
  $sql->execute();
  return  $sql->get_result();
}
public function GetCountAll($TableName,$ClumnName,$Value,$Extra='')
{
  $TableName = strtolower($TableName);
  $DB = $GLOBALS['DB'];
  $flags ='';
  $TableName =mysqli_escape_string($DB,$TableName);
  $Value =mysqli_escape_string($DB,$Value);
  $Extra =mysqli_escape_string($DB,$Extra);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        if($i ==(count($ClumnName)-1)){
        $flags .=" `$ThisClumnName` = '$Value' ";
        }else {
        $flags .=" `$ThisClumnName` = '$Value' or ";
        }
  }
  return $DB->query("SELECT * FROM `$TableName` WHERE $flags " . $Extra);
  $sql->execute();
  return mysqli_num_rows($sql->get_result());
}
public function go_to($url){header('location: ' . $url );die();}
public function GetCountNoRepeat($TableName,$ClumnName,$Value,$NoRepeatClumnName,$Extra='')
{
  $TableName = strtolower($TableName);
  $DB = $GLOBALS['DB'];
  $flags ='';
  $NoRepeat='';
  $TableName =mysqli_escape_string($DB,$TableName);
  $Value =mysqli_escape_string($DB,$Value);
  $Extra =mysqli_escape_string($DB,$Extra);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        if($i ==(count($ClumnName)-1)){
        $flags .=" `$ThisClumnName` = '$Value' ";
        }else {
        $flags .=" `$ThisClumnName` = '$Value' or ";
        }
  }
  for ($i=0; $i < count($NoRepeatClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$NoRepeatClumnName[$i]);
        if($i ==(count($NoRepeatClumnName)-1)){
        $NoRepeat .=" `$ThisClumnName` ";
        }else {
        $NoRepeat .=" `$ThisClumnName` , ";
        }
  }                        //SELECT  DISTINCT  $NoRepeat FROM purches WHERE Seller = 2
  return $DB->query("SELECT  DISTINCT  $NoRepeat FROM `$TableName` WHERE $flags " . $Extra);
  $sql->execute();
 return mysqli_num_rows($sql->get_result());
}
public function GetRowsNoRepeat($TableName,$ClumnName,$Value,$NoRepeatClumnName,$Extra='')
{
  $TableName = strtolower($TableName);
  $DB = $GLOBALS['DB'];
  $flags ='';
  $NoRepeat='';
  $TableName =mysqli_escape_string($DB,$TableName);
  $Value =mysqli_escape_string($DB,$Value);
  $Extra =mysqli_escape_string($DB,$Extra);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        if($i ==(count($ClumnName)-1)){
        $flags .=" `$ThisClumnName` = '$Value' ";
        }else {
        $flags .=" `$ThisClumnName` = '$Value' or ";
        }
  }
  for ($i=0; $i < count($NoRepeatClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$NoRepeatClumnName[$i]);
        if($i ==(count($NoRepeatClumnName)-1)){
        $NoRepeat .=" `$ThisClumnName` ";
        }else {
        $NoRepeat .=" `$ThisClumnName` , ";
        }
  }
  return $DB->query(" SELECT  * FROM `$TableName` WHERE $flags GROUP BY  $NoRepeat" . $Extra);
  $sql->execute();
 return  $sql->get_result();
}
public function FetchArray($res)
{
  $data = [];
  while ($row = mysqli_fetch_assoc($res)) {$data[] = $row;}
  return $data;
}
public function FetchArrayKeys($res,$key)
{
  $data = [];
  while ($row = mysqli_fetch_assoc($res)) {$data[] = $row[$key];}
  return $data;
}
public function FetchArrayObjects($className,$res)
{
  $data = [];
  while ($row = mysqli_fetch_assoc($res)) {$data[] = new $className($row);}
  return $data;
}
public function UploadImage($Path,$File,$Limit = 0,$CutWidth = 0 ,$CutHieght = 0,$aspact = 0)
{
  $target_file = $Path  ; //basename($_FILES["fileToUpload"]["name"]);
  $imageFileType = strtolower(pathinfo($File['name'],PATHINFO_EXTENSION));
  $check = getimagesize($File["tmp_name"]);
  if ($File["size"] > $Limit && $Limit > 0) {
    return 0;
  }
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
    return 1;
   }
    if (move_uploaded_file($File["tmp_name"], $target_file)) {
      if(!$this->IsImage($target_file)){
        unlink($target_file);
        return 2;
      }
      if ($CutWidth != 0) {
          if ($aspact != 0) {
            $r = $this->CutImage($target_file,-1,-1,$imageFileType);
            if (!$r) {return 5;}
            $this->Resize($target_file,$CutWidth);
          }else {
            $r = $this->CutImage($target_file,$CutWidth,$CutHieght,$imageFileType);
            if (!$r) {return 5;}
          }
      }
      return 3;
    } else {
      return 4;
    }

}
public function VideoUploader($Video,$id){
  $target_dir = $GLOBALS['VideoRoot'];
  $target_file = $target_dir . 'video_' . $id . '_' . 0 . '_.mp4';
  $uploadOk = 1;
  $VideoFileType = pathinfo($Video["name"],PATHINFO_EXTENSION);
  $check = mime_content_type($Video["tmp_name"]);
  if(!strstr($check, "video/")){
  return $this->Translate(210);
  }
  if ( $GLOBALS['MaxForVideo']>0){
  if($Video["size"] > $GLOBALS['MaxForVideo'] ) {
  return $this->Translate(211);
  }
  }
  if($VideoFileType != "mp4" && $VideoFileType != "MP4" ) {
  return $this->Translate(212);
  }
  if (move_uploaded_file($Video["tmp_name"], $target_file)) {
  return 1;
  } else {
  return $this->Translate(23);
  }
}
public function GetSUM($TableName,$SumKey,$ClumnName,$Value,$Extra="")
{
   $TableName = strtolower($TableName);
   $DB = $GLOBALS['DB'];
   $TableName =mysqli_escape_string($DB,$TableName);
   $ClumnName =mysqli_escape_string($DB,$ClumnName);
   $SumKey =mysqli_escape_string($DB,$SumKey);
   $Value =mysqli_escape_string($DB,$Value);
   $res = $DB->query("SELECT sum(`$SumKey`) FROM `$TableName` WHERE `$ClumnName` = '$Value' " . $Extra );
   $row = mysqli_fetch_row($res);
   return isset($row[0])?$row[0]:0.00;
}
public function GetRows($TableName,$ClumnName,$Value,$Extra="")
{
   $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $TableName =mysqli_escape_string($DB,$TableName);
    $ClumnName =mysqli_escape_string($DB,$ClumnName);
    $Value =mysqli_escape_string($DB,$Value);
    return $DB->query("SELECT * FROM `$TableName` WHERE `$ClumnName` = '$Value' " . $Extra );
    $sql->execute();
     return $sql->get_result();
}
public function GetRowsNot($TableName,$ClumnName,$Value,$Extra="")
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $TableName =mysqli_escape_string($DB,$TableName);
    $ClumnName =mysqli_escape_string($DB,$ClumnName);
    $Value =mysqli_escape_string($DB,$Value);
    return $DB->query("SELECT * FROM `$TableName` WHERE `$ClumnName` != '$Value' " . $Extra );
     $sql->execute();
     return $sql->get_result();
}
public function GetRowsCostumData($TableName,$ClumnName,$Value,$Data)
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $TableName =mysqli_escape_string($DB,$TableName);
    $ClumnName =mysqli_escape_string($DB,$ClumnName);
    $Value =mysqli_escape_string($DB,$Value);
    $D_t="";
    for ($i=0; $i < count($Data); $i++) {
          $ThisClumnName =mysqli_escape_string($DB,$Data[$i]);
          if($i ==(count($Data)-1)){
          $D_t .=" `$ThisClumnName`   ";
          }else {
          $D_t .=" `$ThisClumnName` , ";
          }
    }
    return $DB->query("SELECT $D_t FROM `$TableName` WHERE `$ClumnName` = '$Value' ");
   $sql->execute();
   return $sql->get_result();
}
public function GetTableCostumData($TableName,$Data,$Extra ='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $TableName =mysqli_escape_string($DB,$TableName);
    $D_t="";
    for ($i=0; $i < count($Data); $i++) {
          $ThisClumnName =mysqli_escape_string($DB,$Data[$i]);
          if($i ==(count($Data)-1)){
          $D_t .=" `$ThisClumnName`   ";
          }else {
          $D_t .=" `$ThisClumnName` , ";
          }
    }
     return $DB->query("SELECT $D_t FROM `$TableName` " . $Extra);
     $sql->execute();
   return $sql->get_result();
}
public function LikeRowMultyFlags($TableName,$ClumnName,$Value,$Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $flags ='(';
    $TableName =mysqli_escape_string($DB,$TableName);
    $Value =mysqli_escape_string($DB,$Value);
    for ($i=0; $i < count($ClumnName); $i++) {
          $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
          if($i ==(count($ClumnName)-1)){
          $flags .=" `$ThisClumnName` like '%$Value%' )";
          }else {
          $flags .=" `$ThisClumnName` = '%$Value%' or ";
          }
    }
    return $DB->query("SELECT * FROM `$TableName` WHERE $flags " . $Extra);
    $sql->execute();
   return $sql->get_result();
}
public function GetRowMultyFlags($TableName,$ClumnName,$Value,$Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $flags ='(';
    $TableName =mysqli_escape_string($DB,$TableName);
    $Value =mysqli_escape_string($DB,$Value);
    for ($i=0; $i < count($ClumnName); $i++) {
          $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
          if($i ==(count($ClumnName)-1)){
          $flags .=" `$ThisClumnName` = '$Value' )";
          }else {
          $flags .=" `$ThisClumnName` = '$Value' or ";
          }
    }
    return $DB->query("SELECT * FROM `$TableName` WHERE $flags " . $Extra);
    $sql->execute();
   return $sql->get_result();
}
public function GetLastID($TableName)
{  $DB = $GLOBALS['DB'];
    return $DB->query("SELECT `id` FROM `$TableName` ORDER BY `id` DESC LIMIT 1" );
    $sql->execute();
    return mysqli_fetch_assoc($sql->get_result())['id'];
}
public function GetRowMultyConditions($TableName,$ClumnName,$Value,$Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $flags ='';
    $TableName =mysqli_escape_string($DB,$TableName);
    for ($i=0; $i < count($ClumnName); $i++) {
      $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
      $ThisValue =mysqli_escape_string($DB,$Value[$i]);
          if($i ==(count($ClumnName)-1)){
          $flags .=" `$ThisClumnName` = '$ThisValue' ";
          }else {
          $flags .=" `$ThisClumnName` = '$ThisValue' and ";
          }
    }
    $cmd = "SELECT * FROM `$TableName` WHERE $flags " . $Extra;
    return $DB->query($cmd);
    $sql->execute();
   return $sql->get_result();
}
public function GetRowMultyConditionsAndFlags($TableName,$ClumnName,$Value,$Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
    $flags ='';
    $TableName =mysqli_escape_string($DB,$TableName);

    for ($k=0; $k <  count($ClumnName); $k++) {
        $Culs = $ClumnName[$k];
        $Vals = $Value[$k];
        $flags .= "(" ;
        for ($i=0; $i < count($Culs); $i++) {
          $ThisClumnName = mysqli_escape_string($DB,$Culs[$i]);
          $ThisValue =mysqli_escape_string($DB,$Vals[$i]);
              if($i ==(count($ClumnName)-1)){
              $flags .=" `$ThisClumnName` = '$ThisValue' ";
              }else {
              $flags .=" `$ThisClumnName` = '$ThisValue' and ";
              }
        }
        $flags .= ")" ;
        if ($k < count($ClumnName)-1) {
           $flags .= " OR " ;
        }
    }


    $cmd = "SELECT * FROM `$TableName` WHERE ($flags) " . $Extra;
    return $DB->query($cmd);
    $sql->execute();
   return $sql->get_result();
}
public function Inject($TableName,$ClumnName,$Value,$returnId = false)
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
  $Culs = '(';
  $TableName =mysqli_escape_string($DB,$TableName);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        if($i ==(count($ClumnName)-1)){
        $Culs .=" `$ThisClumnName` ";
        }else {
        $Culs .=" `$ThisClumnName` , ";
        }
  }
  $Culs .= ')';
  $Vals = '(';
  for ($i=0; $i < count($Value); $i++) {
        $ThisValue =mysqli_escape_string($DB,$Value[$i]);
        if($i ==(count($ClumnName)-1)){
        $Vals .=" '$ThisValue' ";
        }else {
        $Vals .=" '$ThisValue' , ";
        }
  }
  $Vals .= ')';
   if($returnId){
    $DB->query("INSERT INTO `$TableName` $Culs Values $Vals ");
    return $DB->insert_id;
  }else{
    return $DB->query("INSERT INTO `$TableName` $Culs Values $Vals ");
  }
}
public function DeleteRow($TableName,$ClumnName,$Value)
{
  $TableName = strtolower($TableName);
   $DB = $GLOBALS['DB'];
$flags='';
   for ($i=0; $i < count($ClumnName); $i++) {
         $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
         if($i ==(count($ClumnName)-1)){
         $flags .=" `$ThisClumnName` = '$Value' ";
         }else {
         $flags .=" `$ThisClumnName` = '$Value' or ";
         }
   }
   return $DB->query("DELETE FROM  `$TableName`   WHERE $flags ");
   return  $sql->execute();
}
public function DeleteRowCondetions($TableName,$ClumnName,$Value,$ClumnCondetion,$ValueCondetion)
{
  $TableName = strtolower($TableName);
   $DB = $GLOBALS['DB'];
$flags='';
   for ($i=0; $i < count($ClumnName); $i++) {
         $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
         if($i ==(count($ClumnName)-1)){
         $flags .=" `$ThisClumnName` = '$Value' ";
         }else {
         $flags .=" `$ThisClumnName` = '$Value' or ";
         }
   }
$flags.='And';
   for ($i=0; $i < count($ClumnCondetion); $i++) {
         $ThisClumnName =mysqli_escape_string($DB,$ClumnCondetion[$i]);
         if($i ==(count($ClumnCondetion)-1)){
         $flags .=" `$ThisClumnName` = '$ValueCondetion' ";
         }else {
         $flags .=" `$ThisClumnName` = '$ValueCondetion' or ";
         }
   }

   return $DB->query("DELETE FROM  `$TableName`   WHERE $flags ");
   dei("DELETE FROM  `$TableName`   WHERE $flags ");
   return  $sql->execute();
}
public function DeleteRowMultyCondetions($TableName,$ClumnName,$Value)
{
  $TableName = strtolower($TableName);
   $DB = $GLOBALS['DB'];
$flags='';
   for ($i=0; $i < count($ClumnName); $i++) {
         $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
         $ThisValuee =mysqli_escape_string($DB,$Value[$i]);
         if($i ==(count($ClumnName)-1)){
         $flags .=" `$ThisClumnName` = '$ThisValuee' ";
         }else {
         $flags .=" `$ThisClumnName` = '$ThisValuee' and ";
         }
   }

   return $DB->query("DELETE FROM  `$TableName`   WHERE $flags ");
   return  $sql->execute();
}
public function Update($TableName,$ClumnName,$Value, $flagCulmns,$Flag, $Extra='')
{
  $TableName = strtolower($TableName);
  $DB = $GLOBALS['DB'];
  $Culs='' ;$Vals='';
  $TableName =mysqli_escape_string($DB,$TableName);
  $Flag=mysqli_escape_string($DB,$Flag);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        $ThisValue =mysqli_escape_string($DB,$Value[$i]);
        if($i ==(count($ClumnName)-1)){
        $Culs .=" `$ThisClumnName` =  '$ThisValue' ";
        }else {
        $Culs .=" `$ThisClumnName` =  '$ThisValue' , ";
        }
  }

  for ($i=0; $i < count($flagCulmns); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$flagCulmns[$i]);
        if($i ==(count($flagCulmns)-1)){
        $Vals .=" `$ThisClumnName` = '$Flag' ";
        }else {
        $Vals .=" `$ThisClumnName` = '$Flag' or ";
        }
  }
    return $DB->query("UPDATE `$TableName` SET $Culs WHERE $Vals " . $Extra);
    return  $sql->execute();
}
public function UpdateMultyConditions($TableName,$ClumnName,$Value, $flagCulmns,$Flag, $Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
  $Culs='' ;$Vals='';
  $TableName =mysqli_escape_string($DB,$TableName);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        $ThisValue =mysqli_escape_string($DB,$Value[$i]);
        if($i ==(count($ClumnName)-1)){
        $Culs .=" `$ThisClumnName` =  '$ThisValue' ";
        }else {
        $Culs .=" `$ThisClumnName` =  '$ThisValue' , ";
        }
  }

  for ($i=0; $i < count($Flag); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$flagCulmns[$i]);
        $ThisFlag =mysqli_escape_string($DB,$Flag[$i]);
        if($i ==(count($flagCulmns)-1)){
        $Vals .=" `$ThisClumnName` = '$ThisFlag' ";
        }else {
        $Vals .=" `$ThisClumnName` = '$ThisFlag' and ";
        }
  }
  return $DB->query("UPDATE `$TableName` SET $Culs WHERE $Vals " . $Extra);
    return  $sql->execute();
}
public function UpdateRange($TableName,$ClumnName,$Value, $flagCulmns,$Flag,$Operation, $Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
  $Culs='' ;$Vals='';
  $TableName =mysqli_escape_string($DB,$TableName);
  $Flag=mysqli_escape_string($DB,$Flag);
  for ($i=0; $i < count($ClumnName); $i++) {
        $ThisClumnName =mysqli_escape_string($DB,$ClumnName[$i]);
        $ThisValue =mysqli_escape_string($DB,$Value[$i]);
        if($i ==(count($ClumnName)-1)){
        $Culs .=" `$ThisClumnName` =  '$ThisValue' ";
        }else {
        $Culs .=" `$ThisClumnName` =  '$ThisValue' , ";
        }
  }

   $ThisClumnName =mysqli_escape_string($DB,$flagCulmns);
   $Vals .=" `$ThisClumnName` " . $Operation ." '$Flag' ";
   return $DB->query("UPDATE `$TableName` SET $Culs WHERE $Vals " . $Extra);
    return  $sql->execute();
}
public function UpdateRangeOldValue($TableName,$ClumnName,$Value, $key,$StartValue,$Operation, $Extra='')
{
  $TableName = strtolower($TableName);
    $DB = $GLOBALS['DB'];
   $Vals='';
  $TableName =mysqli_escape_string($DB,$TableName);
   $Culs = "`$ClumnName` = (`$ClumnName`" . $Value ;
    $Vals .=" `$key` " . $Operation ." $StartValue ";
   return $DB->query("UPDATE `$TableName` SET $Culs WHERE $Vals " . $Extra);
  return  $sql->execute();
}
public function Post($url,$PostDatakeys=[],$PostDataValues=[])
{
  $PostData=[];
  for ($i=0; $i < count($PostDatakeys); $i++) {
    $PostData[$PostDatakeys[$i]] = $PostDataValues[$i];
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,
           http_build_query($PostData));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec ($ch);
  curl_close ($ch);
  return $server_output;
}
public function HttpGet($url,$PostDatakeys=[],$PostDataValues=[])
{
  $PostData=[];
  for ($i=0; $i < count($PostDatakeys); $i++) {
    $PostData[$PostDatakeys[$i]] = $PostDataValues[$i];
  }
  return file_get_contents($url . "?" . str_replace("%2C",",",http_build_query($PostData)));
}
  public function Translate($value){
    try{
      $this->Languge =   isset($_SESSION['lang']) ?  $_SESSION['lang'] : 'Arabic';
          if(file_exists('./app/Translate/' . $this->Languge) ){
              $file=file_get_contents('./app/Translate/' . $this->Languge);
          }else {
                  $file=file_get_contents('./app/Translate/Arabic');
          }
        $FirstArray =explode("#",$file);
        $TheTrans =explode("\n",$FirstArray[1]);
        $line = $TheTrans[$value - 1];
        $string = trim(preg_replace('/\s\s+/', ' ', $line));
        return $string;
      }catch (Exception $e){
     return "No Translation Found";
      }

  }
  public function CheckCrossSiteRequestForgery()
  {
    if(!isset($_POST["att_value"]) || $this->GetCookie("Att_Token") !== $_POST["att_value"]){
      die(json_encode(["Ok"=>0,"Message"=>$this->Translate(55)]));
    }
  }
  public function CrossSiteRequestForgery()
  {
    $Value = $this->RandomToken(50);
    $this->AddCookie("Att_Token",$Value);
    ?>
     <input type="hidden" name="att_value" value="<?php echo $Value ?>">
    <?php
  }
public function RemoveAllCookies()
    {
      if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }
    public function RandomToken($Length)
    {
      $Genrated_Value = "";
      $Keys = "0123456789_azertyuiopqsdfghjklmwxcvbn-AZERTYUIOPQSDFGHJKLMWXCVBN";
      for ($i=0; $i < $Length  ; $i++) {
       $Genrated_Value .= $Keys[rand(0,62)];
      }
      return $Genrated_Value;
    }
    public function GoHome()
    {
        header("Location: " . ROOT ) ;
    }
    public function isJson($string) {
     return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
      }
    public function AddCookie($Cookie_Name,$Cookie_Value,$LifeTime = 30)
    {
      setcookie($Cookie_Name, $Cookie_Value, time() + (86400 * $LifeTime) , "/");
    }
    public function UpdateCookie($Cookie_Name,$Cookie_Value,$LifeTime = 30)
    {
     setcookie($Cookie_Name, $Cookie_Value, time() + (86400 * $LifeTime) , "/");
    }
    public function GetCookie($Cookie_Name)
    {
      if(isset($_COOKIE[$Cookie_Name])) {
      return  $_COOKIE[$Cookie_Name];
      }else {
        return false;
      }
    }
    public function RemoveCookie($Cookie_Name)
    {
      setcookie($Cookie_Name, null, -1 , "/");
    }
    public function GetTime($value)
    {
      return Date("Y-m-d",strtotime($value));
    }
    public function Direction()
    {
        if(isset($_SESSION['lang'])){
        $this->Languge = $_SESSION['lang'];
        }
        if(file_exists('./app/Translate/' . $this->Languge) ){
        $file=file_get_contents('./app/Translate/' . $this->Languge);
        }else {
        $file=file_get_contents('./app/Translate/Arabic');
        }
        $FirstArray =explode("#",$file);
        return $FirstArray[0];
    }
    public function KillDir($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK );
            foreach( $files as $file ){
                $this->KillDir( $file );
            }
            rmdir($target);
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }
  }
