<?php 

class Api
{
  public function v2()
  {
      $user = Authorization::Token();
      $target = func_get_args();
      if ($user&&$user->valid) {$user->GetIp(); $this->ParseRequest($target);return;}
      if(count($target)>=2&&$target[0]=="sheared"&&$target[1]=="files"){$this->ParseRequest($target);return;}
       $autho = isset($_REQUEST['client_secret'])?$_POST:Authorization::AuthoFail();
       $client_id = isset($_REQUEST['client_id'])?$_POST:Authorization::AuthoFail();
       if (!Authorization::CheckAuthoRequest($autho,$client_id)) {Authorization::AuthoFail();}
      $this->ParseRequest($target);
  }
  protected function ParseRequest($param){
    $Req_class = $param[0];
    $Req_method =  isset($param[1])?$param[1]:Authorization::UnsupportedRequest();
    if(!AllowedRequests::Check($Req_class)){Authorization::UnsupportedRequest();}
    $Server =  $Req_class;
    $Server = new $Server;
    if (!method_exists($Server,$Req_method)) {Authorization::UnsupportedRequest();}
    unset($param[1]);
    unset($param[0]);
    $param =$param ? array_values($param): [];
    call_user_func_array([$Server,$Req_method],$param);
  }

}

class User extends DBHelper
{

  public function __construct($token=false)
  {
    if ($token) {
        $res = $this->GetRows("oauth_access_tokens",'id',$token);
        
        if (mysqli_num_rows($res)>0) {$token = mysqli_fetch_assoc($res)['user_id'];}
        $res = $this->GetRows('users','id',$token);
        if (mysqli_num_rows($res)==0) {$this->valid = false;return;}
          $this->valid = true;
          $this->Info  = json_decode(json_encode(mysqli_fetch_assoc($res)));
          $this->Balance = $this->GetSUM("transactions","amount","user_id",$token);
          //Get Laste Charge id
          $Last_charge_row = $this->GetRows("transactions","user_id",$token," AND `cut_from` = 'BANK' Order by `id` DESC");
          $Last_charge_id = mysqli_num_rows($Last_charge_row)>0?mysqli_fetch_assoc($Last_charge_row)['id']:0;
          $this->_id_row = $Last_charge_id;
          // Count How much debet from last chage ...
          $this->Latest_piece_debt = $this->GetSUM("transactions","amount","user_id",$token," AND `cut_from` = 'PIECE' and `id`> " . $Last_charge_id);
          $this->Latest_servce_debt = $this->GetSUM("transactions","amount","user_id",$token," AND `cut_from` = 'SERVICE' and `id`> " . $Last_charge_id);
          // Count Any Old Debet In User Balance...  * in this part the user shold have 0 debet
          $this->Old_debet = $this->GetSUM("transactions","amount","user_id",$token," AND `id` <= "  . $Last_charge_id);
          // Add The Old Debet To User if any or Cut Some debet if he have some money left ...
          $this->piece_debt = $this->Latest_piece_debt +  ($this->Old_debet/2);
          $this->servce_debt = $this->Latest_servce_debt + ( $this->Old_debet/2);
          // Make Sure that the numbers are less then Zero ...
          $this->piece_debt = $this->piece_debt<=0?$this->piece_debt:0;
          $this->servce_debt = $this->servce_debt<=0?$this->servce_debt:0;
    }
  }
  public function IsInRange($id)
  {
    return false;
    // $arrys = json_decode($this->Info->notic_city_ids);
    // return in_array($id,$arrys);
  }
  public function SendNotification($titel,$message,$type = "app"){
    if ($this->Info->notifications=="0") {
      return;
    }
    $tokens = $this->GetRowMultyConditions("devicetoken",['user_id','type'],[$this->Info->id,"Android"]);
    while ($row = mysqli_fetch_assoc($tokens)) {
      $this->SendAndroidPushNotification($row['device_token'],$titel,$message,$type);
    }
  }
  public function SendAndroidPushNotification($token,$titel,$message,$type = "app"){
    $notification = [
            'title' =>$titel,
            'body' => $message,
            'sound' => 'default'
        ];
        $extraNotificationData = ["message" => $notification,"type" =>$type];
        $fcmNotification = [
            'to'                   => $token,
            'notification'         => $notification,
            'data'                 => $extraNotificationData
        ];
        $headers = ['Authorization: key=' . API_NOTIFICATION_KEY,  'Content-Type: application/json'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,NOTIC_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
  }
  public function NotificationsSettings()
  {
    $user =  $this->RequireLogin("Transactions");
    $params =  $_POST['params']=="0"?0:1;
    $Responce = new Responce();
    if (!$this->Update("users",["notifications"],[$params],['id'],$user->Info->id)) {Authorization::UnknownError();}
    $Responce->status = true;
    $Responce->Send();
  }
  public function Image( )
  {
    $Responce = new Responce();
    $Responce->status = true;
    $user =  $this->RequireLogin("update");
    $image =  !empty($_FILES['file']['name'])?$_FILES['file']:Authorization::FillAllBlanks();
    $file_name = uniqid() . ".jpg";
    $path = UPLOAD_DIR_IMAGE . $file_name ;
    $res = $this->UploadImage($path,$image,0,128,0,1);
    $Responce->Code = $res;
    if ($res==3 && $this->Update('users',['image'],[$file_name],['id'],$user->Info->id)) {
        $Responce->image = $file_name;
        $Responce->Send();
    }
    $Responce->status = false;
    $Responce->Send();
  }
  public function UploadImageFile( )
  {
    $Responce = new Responce();
    $Responce->status = true;
    $user =  $this->RequireLogin("update");
    $image =  !empty($_FILES['file']['name'])?$_FILES['file']:Authorization::FillAllBlanks();
    $file_name = uniqid() . ".jpg";
    $path = UPLOAD_DIR_IMAGE . $file_name ;
    $res = $this->UploadImage($path,$image);
    $Responce->Code = $res;
    if ($res==3) {
      $Responce->id = $this->Inject("Images",["user_id","name","object"],[$user->Info->id,$file_name,"0"],true);
    }
    $Responce->Send();
  }

    public function TransactionInfo($id)
    {
      $user =  $this->RequireLogin("Transactions");
      $Settings = (new Settings())->Info;
      $row = mysqli_fetch_assoc($this->GetRowMultyConditions("Transactions",['id','user_id'],[$id,$user->Info->id]));
      $Responce = new Responce();
      $Responce->status = true;
      $Responce->item = new stdClass();
      $Responce->item->id = $row["id"];
      $Responce->item->Order = mysqli_fetch_assoc($this->GetRows("ordersrequests","orderoffer_id",$row["offer_id"]));
      $Responce->item->Offer = mysqli_fetch_assoc($this->GetRows("ordersoffers","id",$row["offer_id"]));
      $Responce->item->Date = date("Y-m-d", strtotime($row['created_at']));
      $Responce->item->Time = date("H:i:s", strtotime($row['created_at']));
      $Responce->item->Amount = $row["amount"];
      if ($row["cut_from"]=="PIECE") {
        $Responce->item->Cut = $Settings->benefit_per_piece . " %";
        $Responce->item->TotalAmount = $Responce->item->Offer['piece_price'];
        $Responce->item->Orderid = $Responce->item->Order['id'];
        $Responce->item->Type = "خصم من ثمة القطعة";
      }elseif ($row["cut_from"]=="SERVICE") {
          $Responce->item->Cut = $Settings->benefit_per_servcice . " %";
          $Responce->item->TotalAmount = $Responce->item->Offer['serevce_price'];
          $Responce->item->Orderid = $Responce->item->Order['id'];
          $Responce->item->Type = "خصم من ثمن الخدمة";
      }elseif ($row["cut_from"]=="BANK") {
        $Responce->item->Cut = "0 %";
        $Responce->item->Amount = "0";
        $Responce->item->TotalAmount = $row["amount"];
        $Responce->item->Orderid = "0";
        $Responce->item->Type = "شحن الحساب";
      }elseif ($row["cut_from"]=="DEBT") {
        $Responce->item->Cut = "0 %";
        $Responce->item->Amount = "0";
        $Responce->item->TotalAmount = $row["amount"];
        $Responce->item->Orderid = "0";
        $Responce->item->Type = "سلفة من دكتور تك";
      }
      $Responce->Send();
    }
   public function Transactions()
   {
      $user =  $this->RequireLogin("Transactions");
      $settings = new Settings();
      $Responce = new Responce();
      $Responce->status = true;
      $Responce->balance = $user->Balance;
      $Responce->bank_name = $settings->Info->Bank_Name;
      $Responce->piece_debt = $user->piece_debt;
      $Responce->servce_debt = $user->servce_debt;
      $Responce->total = $Responce->piece_debt + $Responce->servce_debt;
      $Responce->bank_iban = $settings->Info->Bank_IBAN;
      $Responce->user = $user;
      $Responce->max = $settings->Info->min_indebt;
      $Responce->items = $this->FetchArrayObjects("TransactionObject",$this->GetRows('Transactions','user_id',$user->Info->id," order by `id` DESC"));
      $Responce->Send();
   }
  public function GetIp()
  {
  $ip =  $_SERVER['REMOTE_ADDR'];
  $this->Update("users",["ip_adress"],[$ip],["id"],$this->Info->id);
  }
  public function CutFromOffer($offer_id){
    $offer = new OfferObject($offer_id);
    $settings = new Settings();
    $cut_offer_p = - ($offer->piece_price * ($settings->Info->benefit_per_piece/100));
    $cut_offer_s = - ($offer->serevce_price * ($settings->Info->benefit_per_servcice/100));
    return $this->Inject("transactions",['user_id','amount','offer_id','cut_from'],[$this->Info->id,$cut_offer_p,$offer_id,"PIECE"])&&
           $this->Inject("transactions",['user_id','amount','offer_id','cut_from'],[$this->Info->id,$cut_offer_s,$offer_id,"SERVICE"]);
  }
  public function conversation()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $user =  $this->RequireLogin("chat");
    $order_id = isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::FillAllBlanks();
    $seen = isset($_POST['seen'])&&!empty($_POST['seen'])?$_POST['seen']:Authorization::FillAllBlanks();

    $order_id = mysqli_escape_string($GLOBALS['DB'],$order_id);
    if ($seen == "true") {
      $Responce->messages = $this->FetchArray($this->GetRowMultyFlags("chat",["receiver"],$user->Info->id,
      " AND `order_id` = " . $order_id .  " AND `seen`= 0"));
    }else {
      $Responce->messages = $this->FetchArray($this->GetRowMultyFlags("chat",["sender","receiver"],$user->Info->id,
      " AND `order_id` = " . $order_id ));
    }

    $this->UpdateMultyConditions("chat",["seen","notic"],["1","1"],["receiver","order_id"],[$user->Info->id,$order_id]);
    $Responce->Send();
  }
  public function chat(){
    $Responce = new Responce();
    $Responce->status = true;
    $user =  $this->RequireLogin("chat");
    $message = isset($_POST['message'])&&!empty($_POST['message'])?$_POST['message']:Authorization::FillAllBlanks();
    $type = isset($_POST['type'])?$_POST['type']:Authorization::FillAllBlanks();
    $order_id = isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::FillAllBlanks();
    $sender_id= $user->Info->id;
    $order_row = mysqli_fetch_assoc($this->GetRows("ordersrequests","id",$order_id));
    if(!$order_row){
        $Responce->messageid = 198;
        $Responce->Send();
    }
    if($order_row['status']!="PROCESSING"){
        $Responce->message = 198;
        $Responce->Send();
    }
    if($order_row["user_id"]!=$user->Info->id){
      $receiver_id=$order_row["user_id"];
    }else{
      $offer_id=$order_row["orderoffer_id"];
      $receiver_id=mysqli_fetch_assoc($this->GetRows("ordersoffers","id",$offer_id))['user_id'];
    }
    if($type=="1"){
      $path=  uniqid();
      mkdir(UPLOAD_CHAT_IMAGE .$path);
      $src= UPLOAD_CHAT_IMAGE .$path."/original.jpg";
      $image = file_put_contents($src, base64_decode($message));
      $this->Resize($src,80, UPLOAD_CHAT_IMAGE .$path."/thumbnail.jpg");
      $id = $this->Inject("chat",["order_id","sender","message","receiver","seen","notic","created_at","type"],
      [$order_id,$sender_id,$path,$receiver_id,0,0,date("Y-m-d H:i:s"),1],true);
      if(!$id){
        Authorization::UnknownError();
      }

    }else{
      $id =$this->Inject("chat",["order_id","sender","message","receiver","seen","notic","created_at","type"],
      [$order_id,$sender_id,$message,$receiver_id,0,0,date("Y-m-d H:i:s"),0],true);
      if(!$id){
        Authorization::UnknownError();
      }
      }
      (new User($receiver_id))->SendNotification($user->Info->name,$message,"message");
    $Responce->message=mysqli_fetch_assoc($this->GetRows("chat",'id',$id));
    $Responce->Send();

  }
  public function buy()
  {  $Responce = new Responce();
    $Responce->status = false;
    $user = $this->RequireLogin("buy");
    $address = isset($_POST['address'])&&!empty($_POST['address'])?$_POST['address']:Authorization::FillAllBlanks();
    $first_name = isset($_POST['first_name'])&&!empty($_POST['first_name'])?$_POST['first_name']:Authorization::FillAllBlanks();
    $last_name = isset($_POST['last_name'])&&!empty($_POST['last_name'])?$_POST['last_name']:Authorization::FillAllBlanks();
    $latitude = isset($_POST['latitude'])&&!empty($_POST['latitude'])?$_POST['latitude']:Authorization::FillAllBlanks();
    $longitude = isset($_POST['longitude'])&&!empty($_POST['longitude'])?$_POST['longitude']:Authorization::FillAllBlanks();
    $payment_method_id = isset($_POST['payment_method_id'])&&!empty($_POST['payment_method_id'])?$_POST['payment_method_id']:Authorization::FillAllBlanks();
    $products = isset($_POST['products'])&&!empty($_POST['products'])?$_POST['products']:Authorization::FillAllBlanks();
    $products = json_decode($products);
    for ($i=0; $i < count($products); $i++) {
      $quantity = $products[$i]->quantity;
      $product_id = $products[$i]->id;
      $res = $this->GetRows("products","id",$product_id);
      if (mysqli_num_rows($res)==0) {Authorization::UnknownError();}
      $product_info = mysqli_fetch_assoc($res);
      $total_price = $product_info['price'] * $quantity;
      if (!$this->Inject("payments",["address","total_price","status","product_id","quantity","user_id","first_name","last_name","payment_method_id","latitude","longitude"],
      [$address,$total_price,"PENDING",$product_id,$quantity,$user->Info->id,$first_name,$last_name,$payment_method_id,$latitude,$longitude])){
        Authorization::UnknownError();
      }}
    $Responce->status = true;
    $Responce->Send();
  }
  public function comment($value='')
  {
    $Responce = new Responce();
    $Responce->status = false;
    $user =  $this->RequireLogin("comment");
    $post_id = isset($_POST['post_id'])&&!empty($_POST['post_id'])?$_POST['post_id']:Authorization::FillAllBlanks();
    $comment = isset($_POST['comment'])&&!empty($_POST['comment'])?$_POST['comment']:Authorization::FillAllBlanks();
    $cmnt_id = $this->Inject("comments",['comment','user_id','post_id',"created_at"],[$comment,$user->Info->id,$post_id,date("Y-m-d H:i:s")],true);
    if (!$cmnt_id){Authorization::UnknownError();}
    $row = mysqli_fetch_assoc($this->GetRows('comments','id',$cmnt_id));
    $Responce->status = true;
    $Responce->Send();
 }
  public function likepost()
  {
    $Responce = new Responce();
    $Responce->status = false;
    $user =  $this->RequireLogin("likepost");
    $post_id = isset($_POST['post_id'])&&!empty($_POST['post_id'])?$_POST['post_id']:Authorization::FillAllBlanks();
    $operation = isset($_POST['operation'])&&!empty($_POST['operation'])?$_POST['operation']:Authorization::FillAllBlanks();
    if ($operation=="like") {
       $success = $this->Inject("likeposts",['user_id','post_id'],[$user->Info->id,$post_id]);
    }else {
        $success = $this->DeleteRowMultyCondetions("likeposts",['user_id','post_id'],[$user->Info->id,$post_id]);
    }
    $Responce->status = $success;
    $Responce->Send();
 }

  public function updateInfo()
  {
    $Responce = new Responce();
    $Responce->status = false;
    $user =  $this->RequireLogin("update");
    Logger::O($user);
    if (isset($_POST['phone'])) {
      $phone = isset($_POST['phone'])&&!empty($_POST['phone'])?$_POST['phone']:Authorization::FillAllBlanks();
        $res = $this->GetRows('users','phone',$phone);
      if (mysqli_num_rows($res)>0&&$user->Info->phone!=$phone) {$Responce->messageid = 135 ;$Responce->Send();}
      if (!$this->Update("users",["phone"],[$phone],['id'],$user->Info->id)) {Authorization::UnknownError();}
    }elseif (isset($_POST['username'])) {
      $username = isset($_POST['username'])&&!empty($_POST['username'])?$_POST['username']:Authorization::FillAllBlanks();
      $res = $this->GetRows('users','username',$username);
      if (mysqli_num_rows($res)>0&&$user->Info->username!=$username) {$Responce->messageid = 135 ;$Responce->Send();}
      if (!$this->Update("users",["username"],[$username],['id'],$user->Info->id)) {Authorization::UnknownError();}
    }elseif (isset($_POST['name'])) {
       $name = isset($_POST['name'])&&!empty($_POST['name'])?$_POST['name']:Authorization::FillAllBlanks();
       if (!$this->Update("users",["name"],[$name],['id'],$user->Info->id)) {Authorization::UnknownError();}
    }
    $Responce->status = true;
    $Responce->item = new UserObject(mysqli_fetch_assoc($this->GetRows('users','id',$user->Info->id)));
    $Responce->Send();
   }
   public function changepassword()
   {
     $user =  $this->RequireLogin("update");
     $Responce = new Responce();
     $Responce->status = false;
     //$current_password= isset($_POST['current_password'])&&!empty($_POST['current_password'])?$_POST['current_password']:Authorization::FillAllBlanks();
     $new_password = isset($_POST['new_password'])&&!empty($_POST['new_password'])?$_POST['new_password']:Authorization::FillAllBlanks();
     //if ($this->hashPassword($current_password)!==$user->Info->password) {   $Responce->Send(); }
      if (!$this->Update("users",["password","completed"],[$this->hashPassword($new_password),"1 "],['id'],$user->Info->id)) {Authorization::UnknownError();}
       $Responce->status = true;
       $Responce->Send();
    }
  public function Checkout($value='')
  {
    $settings = new Settings();
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->bank_name = $settings->Info->Bank_Name;
    $Responce->bank_iban = $settings->Info->Bank_IBAN;;
    $Responce->fee = $settings->Info->fee;
    $Responce->Send();
  }

  public function rest()
  {
    $Responce = new Responce();
    $Responce->status = false;
    $phone = isset($_POST['phone'])&&!empty($_POST['phone'])&&(strlen($_POST['phone'])==9||strlen($_POST['phone'])==10)?$_POST['phone']:Authorization::FillAllBlanks();
    $phone = strlen($phone)==10?substr($phone,1):$phone;
    $res = $this->GetRows('users','phone',$phone);
    if(mysqli_num_rows($res)==0){$Responce->messageid=223;$Responce->Send();}
    $row = mysqli_fetch_assoc($res);
    $user = new User($row['id']);
    if(!$user->RestorePasswordRequest()){
        $Responce->messageid = 224;$Responce->Send();
    }else{
        $Responce->status = true;
        $Responce->user_id = $user->Info->id;
        $Responce->messageid = 225;$Responce->Send();
    }
  }
  public function checkcode(){
    $Responce = new Responce();
    $Responce->status = false;
    $Code = isset($_POST['code'])&&!empty($_POST['code'])&&strlen($_POST['code'])>=4?$_POST['code']:Authorization::FillAllBlanks();
    $res = $this->GetRows("users","password_resets",$Code);
    if (mysqli_num_rows($res)==0) {$Responce->messageid = 233 ;$Responce->Send();}
      if (!$this->Update("users",["password_resets"],["0"],['password_resets'],$Code)) {Authorization::UnknownError();}
      $row = mysqli_fetch_assoc($res);
      $user = new User($row['id']);
      $row["remember_token"] = $user->SetToken();
        $Responce->status = true;
        $Responce->user = new UserObject($row);
        $Responce->Send();
  }
  public function communicate()
  {
    $Responce = new Responce();
    $Responce->status = true;
      $user = $this->RequireLogin("communcate");
      $reason_id = isset($_POST['reason_id'])&&!empty($_POST['reason_id'])?$_POST['reason_id']:Authorization::FillAllBlanks();
      $content = isset($_POST['content'])&&!empty($_POST['content'])?$_POST['content']:Authorization::FillAllBlanks();
      if (!$this->Inject("messenger_messages",['topic_id','sender_id','content','sent_at'],[$reason_id,$user->Info->id,$content,Date("Y-m-d")])) {
      $Responce->status = false;
      $Responce->send();
      }
      $Responce->send();
  }
  public function rate()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $user = $this->RequireLogin("rate");
    $order_id = isset($_POST['order'])&&!empty($_POST['order'])?$_POST['order']:Authorization::FillAllBlanks();
    $stars = isset($_POST['stars'])&&!empty($_POST['stars'])?$_POST['stars']:Authorization::FillAllBlanks();
    $stars = $stars>5?5:$stars;
    $stars = $stars<1?1:$stars;
    $res = $this->GetRows("ordersrequests","id",$order_id);
    if (mysqli_num_rows($res)==0) {
      $Responce->messageid = 102;
      $Responce->Send();
    }
    $Order_row = mysqli_fetch_assoc($res);
    $offer = mysqli_fetch_assoc($this->GetRows("ordersoffers","id",$Order_row['orderoffer_id']));
    if (!$offer||$offer== null) {
      $Responce->messageid = 103;
      $Responce->Send();
    }
    if (mysqli_num_rows($this->GetRowMultyConditions("rating",["OrderId","RaterId"],[$order_id,$user->Info->id]))>0) {
      $Responce->messageid = 104;
      $Responce->Send();
    }
    $resiver_id = $offer['user_id']==$user->Info->id?$Order_row['user_id']:$offer['user_id'];
    $notic_msg = $offer['user_id']==$user->Info->id?RATE_NOTIC_USER:RATE_NOTIC_ENG;
    $notic_msg_id = $offer['user_id']==$user->Info->id?171:170;
    if(!$this->Inject("rating",['UserId','RaterId',"Stars","OrderId"],[$offer['user_id'],$user->Info->id,$stars,$order_id])){
      $Responce->messageid = 105;
      $Responce->Send();
    }else if(mysqli_num_rows($this->GetRowMultyConditions("rating",["OrderId","RaterId"],[$order_id,$resiver_id]))==0) {
       $this->Inject('notification',['object_id','message_id','type_id','sender_id','reciever_id','created_at'],
                  [$order_id,$notic_msg_id,'3', $user->Info->id,$resiver_id,Date("Y-m-d H:i:s")]);
                  (new User($resiver_id))->SendNotification("دكتور تك",str_replace("#",$user->Info->name,str_replace('*',$stars,$notic_msg)));
    }
    $Responce->messageid = 106;
    $Responce->Send();
  }
  public function SendSMS($message)
  {
    if ($this->valid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Moblie_SMS);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                http_build_query(array('mobile'                   =>     SMS_USER,
                                       'password'                 =>     SMS_PASS ,
                                       'numbers'                  =>     COUNTY_CODE . $this->Info->phone ,
                                       'sender'                   =>     APP_NAME_EN,
                                       'msg'                      =>     $message,
                                       'lang'                     =>     3,
                                       'applicationType'          =>     3,
                                       'returnJson'               =>     true)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $res = json_decode($server_output);
        return $res->status;
    }else {
        return false;
    }
  }
  public function RestorePasswordRequest()
  {
    if ($this->valid) {
        $code = $this->SetResetCode();
        if (!$code) {Authorization::UnknownError();}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Moblie_SMS);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                http_build_query(array('mobile'                   =>     SMS_USER,
                                       'password'                 =>     SMS_PASS ,
                                       'numbers'                  =>     COUNTY_CODE . $this->Info->phone ,
                                       'sender'                   =>     APP_NAME_EN,
                                       'msg'                      =>     str_replace("*",$code,(new Settings())->Info->Restore_Message),
                                       'lang'                     =>     3,
                                       'applicationType'          =>     3,
                                       'returnJson'               =>     true)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $res = json_decode($server_output);
        return $res->status;
    }else {
        Authorization::UnsupportedRequest();
    }
  }
  public function SendPhoneCode()
  {
    if ($this->valid) {
        $code = $this->SetCode();
        if (!$code) {Authorization::UnknownError();}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Moblie_SMS);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                http_build_query(array('mobile'                   =>     SMS_USER,
                                       'password'                 =>     SMS_PASS ,
                                       'numbers'                  =>     COUNTY_CODE . $this->Info->phone ,
                                       'sender'                   =>     APP_NAME_EN,
                                       'msg'                      =>     str_replace("*",$code,(new Settings())->Info->Active_Message),
                                       'lang'                     =>     3,
                                       'applicationType'          =>     3,
                                       'returnJson'               =>     true)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $res = json_decode($server_output);
        return $res->status;
    }else {
        Authorization::UnsupportedRequest();
    }
  }

  protected function SetCode()
  {
    if (isset($this->Info->phone_activation_code)&&!empty($this->Info->phone_activation_code)) {return $this->Info->phone_activation_code;}
    $Code = rand(111111,999999);
    if ($this->Update('users',['phone_activation_code'],[$Code],['id'],$this->Info->id)) {
      return $Code;
    }
    return false;
  }
  protected function SetResetCode()
  {
    $Code = rand(111111,999999);
    if ($this->Update('users',['password_resets'],[$Code],['id'],$this->Info->id)) {
      return $Code;
    }
    return false;
  }
  public function resend()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $user_id = isset($_POST['user_id'])&&!empty($_POST['user_id'])?$_POST['user_id']:Authorization::FillAllBlanks();
    $user = new User($user_id);
    $remain_time = SEND_CODE_DELAY - (strtotime(Date('Y-m-d H:i:s'))-strtotime($user->Info->phone_send_time));
    if ($remain_time>0) {
      $Responce->Remain_time = $remain_time;
      $Responce->messageid = 231;
      $Responce->Send();
    }
    if (!$user->SendPhoneCode()) {Authorization::UnknownError();}
    if (!$this->Update('users',['phone_send_time'],[date("Y-m-d H:i:s")],['id'],$user->Info->id)){Authorization::UnknownError();}
    $Responce->Remain_time = SEND_CODE_DELAY * 1000;
    $Responce->messageid = 232;
    $Responce->Send();
  }
  public function devices(){
    $user = $this->RequireLogin('devices');
    $Responce = new Responce();
    $Responce->status = true;
    $operation = isset($_POST['operation'])&&!empty($_POST['operation'])?$_POST['operation']:Authorization::FillAllBlanks();
    $type = isset($_POST['type'])&&!empty($_POST['type'])?$_POST['type']:Authorization::FillAllBlanks();
    $device_token = isset($_POST['device_token'])&&!empty($_POST['device_token'])?$_POST['device_token']:Authorization::FillAllBlanks();
    $this->DeleteRow("devicetoken",['device_token'],$device_token);
    if ($operation=='add') { $this->Inject('devicetoken',['type','device_token','user_id'],[$type,$device_token,$user->Info->id]);}
    $Responce->Send();
  }
  public function changephone(){
    $Responce = new Responce();
    $Responce->status = true;
    $user = $this->RequireLogin('changephone');
    $old_phone = $user->Info->phone;
    $new_phone = isset($_POST['phone'])&&!empty($_POST['phone'])&&(strlen($_POST['phone'])==9||strlen($_POST['phone'])==10)?$_POST['phone']:Authorization::FillAllBlanks();
    $new_phone = strlen($new_phone)==10?substr($new_phone,1):$new_phone;
    $remain_time = SEND_CODE_DELAY - (strtotime(Date('Y-m-d H:i:s'))-strtotime($user->Info->phone_send_time));
    if ($old_phone==$new_phone) {
      $Responce->code = 2;
      $Responce->message = "رقم الهاتف المستخدم هو نفسه السابق . يرجى التأكد منه";
      $Responce->Send();
    }
    if (mysqli_num_rows($this->GetRows('users','phone',$new_phone))>0) {
      $Responce->code = 3;
      $Responce->message = "رقم الجوال الجديد تابع لمستخدم اخر يرجى إستخدام رقم جوال جديد";
      $Responce->Send();
    }
    if ($remain_time>0) {
      $Responce->code = 4;
      $Responce->Remain_time = $remain_time;
      $Responce->message = "يرجى الإنتظار قبل استبدال رقم الجوال . بعد " . $remain_time . " ثواني" ;
      $Responce->Send();
    }
    if(!$this->Update("users",['phone'],[$new_phone],['id'],$user->Info->id)){
      $Responce->status = false;
      $Responce->Send();
    }
    if (!$user->SendPhoneCode()) {Authorization::UnknownError();}
    if (!$this->Update('users',['phone_send_time'],[date("Y-m-d H:i:s")],['id'],$user->Info->id)){Authorization::UnknownError();}

    $Responce->Remain_time = SEND_CODE_DELAY * 1000;
    $Responce->message = '0' . $new_phone;
    $Responce->code = 1;
    $Responce->Send();
  }
  public function code()
  {
    $Responce = new Responce();
    $user = $this->RequireLogin('code');
    $code = isset($_POST['code'])&&!empty($_POST['code'])?$_POST['code']:Authorization::FillAllBlanks();
    if (mysqli_num_rows($this->GetRowMultyConditions('users',['id','phone_activation_code'],[$user->Info->id,$code]))==0) {
     $Responce->status = false;
     $Responce->Send();
    }
   if (!$this->Update('users',['Is_phone_activated'],['1'],['id'],$user->Info->id)) {
      $Responce->status = false;
      $Responce->Send();
    }
    $Responce->status = true;
    $Responce->Send();
  }
  public function register()
  {
      $Responce = new Responce();
      $name = isset($_POST['name'])&&!empty($_POST['name'])?$_POST['name']:Authorization::FillAllBlanks();
      $username = isset($_POST['username'])&&!empty($_POST['username'])?$_POST['username']: $name  . "_" . rand(1111,9999);
      $phone = isset($_POST['phone'])&&!empty($_POST['phone'])&&(strlen($_POST['phone'])==9||strlen($_POST['phone'])==10)?$_POST['phone']:Authorization::FillAllBlanks();
      $password = isset($_POST['password'])&&!empty($_POST['password'])?$_POST['password']:Authorization::FillAllBlanks();
      $phone = strlen($phone)==10?substr($phone,1):$phone;
      //  if (mysqli_num_rows($this->GetRows("users","email",$email))>0) {$Responce->status=true;$Responce->code=201;$Responce->Send();}
      if (mysqli_num_rows($this->GetRows("users","phone",$phone))>0) {$Responce->status=true;$Responce->messageid=117;$Responce->Send();}
      $id = $this->Inject('users',['name','phone','password','username','type','Is_phone_activated'],
                                  [$name,$phone,$this->hashPassword($password),$username,'user','1'],true);
      if (!$id) {Authorization::UnknownError();}
      $User = new User($id);
      //  if (!$User->SendPhoneCode()) {Authorization::UnknownError();}
      //  if (!$this->Update('users',['phone_send_time'],[date("Y-m-d H:i:s")],['id'],$User->Info->id)){Authorization::UnknownError();}
      $Responce->status = true;
      $Responce->item = new UserObject(mysqli_fetch_assoc($this->GetRows("users","id",$id)));
      $Responce->item->remember_token = $User->SetToken();
      $Responce->time = SEND_CODE_DELAY;
      $Responce->Send();
  }
  public function finishjob()
  {
    $Responce = new Responce();
    $Engineer = $this->RequireLogin('finishjob');
    $order_id =  isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::UnsupportedRequest();
    $offer_id = mysqli_fetch_assoc($this->GetRows("ordersrequests","id",$order_id))["orderoffer_id"];
    if (mysqli_num_rows($this->GetRowMultyConditions('ordersoffers',['id','user_id'],[$offer_id,$Engineer->Info->id]))==0) {Authorization::UnsupportedRequest();}
    $Order_object = new OfferObject($order_id);
    $user_id = mysqli_fetch_assoc($this->GetRows("ordersrequests","id",$order_id))["user_id"];
    if (!$this->Update('ordersrequests',['status'],['COMPLETE'],['id'],$order_id)||
        !$this->Inject('notification',['object_id','message_id','type_id','sender_id','reciever_id','created_at'],
        [$order_id,168,'3', $Engineer->Info->id,$user_id,Date("Y-m-d H:i:s")]) ||
        !$Engineer->CutFromOffer($offer_id)
        )
         {Authorization::UnknownError();}
         (new User($user_id))->SendNotification($Engineer->Info->name,str_replace('*',$Engineer->Info->name,DELEVERED_ORDER_NOTIC));
        $Responce->status = true;
        $Responce->Send();
  }
  public function accept()
  {
     $Responce = new Responce();
     $user = $this->RequireLogin('addoffer');
     $order_id =  isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::UnsupportedRequest();
     $offer_id =  isset($_POST['offer_id'])&&!empty($_POST['offer_id'])?$_POST['offer_id']:Authorization::UnsupportedRequest();
     if (mysqli_num_rows($this->GetRowMultyConditions('ordersrequests',['id','user_id'],[$order_id,$user->Info->id]))==0) {Authorization::UnsupportedRequest();}
     if (mysqli_num_rows($this->GetRowMultyConditions('ordersoffers',['id','order_id'],[$offer_id,$order_id]))==0) {Authorization::UnsupportedRequest();}
     $offer_object = new OfferObject($offer_id);
     $Engineer = new User($offer_object->user_id);
     if (!$this->Update('ordersrequests',['price','orderoffer_id','status'],[$offer_object->total_price,$offer_object->id,'PROCESSING'],['id'],$order_id)||
         !$this->Inject('notification',['object_id','message_id','type_id','sender_id','reciever_id','created_at'],
         [$order_id,167,'1',$user->Info->id,$Engineer->Info->id,Date("Y-m-d H:i:s")])) {Authorization::UnknownError();}
            (new User($Engineer->Info->id))->SendNotification($user->Info->name,ACCEPT_OFFER_NOTIC . $user->Info->name);
         $Responce->status = true;
         $Responce->Send();
  }
  public function cancelorder()
  {
     $Responce = new Responce();
     $user = $this->RequireLogin('cancelorder');
     $order_id =  isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::UnsupportedRequest();
      if (mysqli_num_rows($this->GetRowMultyConditions('ordersrequests',['id','user_id'],[$order_id,$user->Info->id]))==0) {Authorization::UnsupportedRequest();}
    if (!$this->Update('ordersrequests',['deleted_at'],[Date("Y-m-d H:i:s")],['id'],$order_id)){Authorization::UnknownError();}
         $Responce->status = true;
         $Responce->Send();
  }
  public function offer()
  {
    $Responce = new Responce();
    $user = $this->RequireLogin('addoffer');
    if ($user->Balance<-(new Settings())->Info->min_indebt) {
      $Responce->status = false;
      $Responce->messageid = 204;
      $Responce->Send();
    }

    $order_id = isset($_POST['id'])&&!empty($_POST['id'])?$_POST['id']:Authorization::FillAllBlanks();
    $Offer_Desc = isset($_POST['Offer_Desc'])&&!empty($_POST['Offer_Desc'])?$_POST['Offer_Desc']:Authorization::FillAllBlanks();

    $Serevce_Price = isset($_POST['Serevce_Price'])&&!empty($_POST['Serevce_Price'])?$_POST['Serevce_Price']:Authorization::FillAllBlanks();
    $Piece_Price = $_POST['Piece_Price'];
    $Offer_Total = floatval($Piece_Price) + floatval($Serevce_Price);

    $Resive_d = isset($_POST['Resive_d'])?$_POST['Resive_d']:Authorization::FillAllBlanks();
    $Resive_h = isset($_POST['Resive_h'])?$_POST['Resive_h']:Authorization::FillAllBlanks();
    $Resive_m = isset($_POST['Resive_m'])?$_POST['Resive_m']:Authorization::FillAllBlanks();

    $fix_d = isset($_POST['fix_d'])?$_POST['fix_d']:Authorization::FillAllBlanks();
    $fix_h = isset($_POST['fix_h'])?$_POST['fix_h']:Authorization::FillAllBlanks();
    $fix_m = isset($_POST['fix_m'])?$_POST['fix_m']:Authorization::FillAllBlanks();

    $Extra_Note = isset($_POST['Extra_Note'])?$_POST['Extra_Note']:Authorization::FillAllBlanks();
    $Piece_Type =  $_POST['Piece_Type'];
    $Servce_Type = isset($_POST['Servce_Type'])&&!empty($_POST['Servce_Type'])?$_POST['Servce_Type']:Authorization::FillAllBlanks();
    $offer_garanty_id = isset($_POST['offer_garanty_id'])?$_POST['offer_garanty_id']:Authorization::FillAllBlanks();
    //  if (mysqli_num_rows($this->GetRowMultyConditions("ordersoffers",["order_id","user_id"],[$order_id,$user->Info->id]))>0) {
    //   $Responce->status = true;
    //   $Responce->code = 1;
    //   $Responce->items = mysqli_fetch_assoc($this->GetRowMultyConditions("ordersoffers",["order_id","user_id"],[$order_id,$user->Info->id]));
    //   $Responce->Send();
    // }
    $order_row = mysqli_fetch_assoc($this->GetRows("ordersrequests","id",$order_id));
    $onner = mysqli_fetch_assoc($this->GetRows("users","id",$order_row['user_id']));

    if (!empty($order_row['orderoffer_id'])) {
      $Responce->status = false;
      $Responce->messageid = 203;
      $Responce->Send();
    }
    if(!$this->Inject("ordersoffers",["order_id","offer_description","serevce_price","piece_price","total_price","extra_note","piece_type","servce_type","user_id","Resive_d","Resive_h","Resive_m","fix_d","fix_h","fix_m","created_at","offer_garanty"],
                                      [$order_id,$Offer_Desc,$Serevce_Price,$Piece_Price,$Offer_Total,$Extra_Note,$Piece_Type,$Servce_Type,$user->Info->id,$Resive_d,$Resive_h,$Resive_m,$fix_d,$fix_h,$fix_m,date('Y-m-d H:i:s'),$offer_garanty_id]) ||
      !$this->Inject("notification",['object_id','message_id','type_id','sender_id','reciever_id'],
      [$order_id,166,1,$user->Info->id,$order_row['user_id']])){
        Authorization::UnknownError();
     }
     $Ownner = new User($order_row['user_id']);
     $Ownner->SendNotification("عرض جديد",NEW_OFFER_NOTIC . $user->Info->name);
     //SendSMS
     $Ownner->SendSMS(
       "عزيزي العميل لديك عرض صيانه لطلبك رقم " .
       " " . $order_id . " " .
       "بمبلغ " .
       " " . $Offer_Total . " " .
       "ريال شاهد تفاصيل العرض في تطبيق دكتور تك "
     );

     $Responce->status = true;
     $Responce->Send();
  }
  public function join()
  {
     $JoinRequest = new JoinRequest($this);
     $JoinRequest->Send();
  }
  public function order()
  {
    $user = $this->RequireLogin('order');
    $UserOrders = new Orders($user);
    $UserOrders->AddNew();
  }
  public function token()
  {
    $Responce = new Responce();
    $user = $this->RequireLogin('token');
    $Responce->status = true;
    $Responce->access_token = $user->SetToken();
    $Responce->refresh_token = $user->RefrashToken($Responce->access_token);
    $Responce->Send();
  }
  public function orders()
  {
    $user = $this->RequireLogin('orders');
    $UserOrders = new Orders($user);
    $UserOrders->GetAll();
  }
  public function getOffers(){
    $Responce = new Responce();
    $user = $this->RequireLogin('orders');
    $id = isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::UnsupportedRequest();
    $Responce->items =  $this->FetchArrayObjects('OfferObject',$this->GetRows('ordersoffers','order_id',$id," order by `id` DESC"));
    $Responce->status = true;
    $Responce->Send();
  }
  public function RemoveOffer()
  {
    $Responce = new Responce();
    $user = $this->RequireLogin('addoffer');
    $offer_id =  isset($_POST['offer_id'])&&!empty($_POST['offer_id'])?$_POST['offer_id']:Authorization::UnsupportedRequest();
    if (mysqli_num_rows($this->GetRowMultyConditions('ordersoffers',['id','user_id'],[$offer_id,$user->Info->id]))==0) {Authorization::UnsupportedRequest();}
    if (!$this->Update('ordersoffers',['deleted_at'],[date("Y-m-d H:i:s")],['id'],$offer_id)) {Authorization::UnknownError();}
        $Responce->status = true;
        $Responce->Send();
  }
  public function otherorders()
  {
    $user = $this->RequireLogin('orders');
    $UserOrders = new Orders($user);
    $UserOrders->GetOthers();
  }
  public function NotificationAction($key)
  {
    $user = Authorization::Token();
    switch ($key) {
      case 'remove':
        $id = isset($_POST['target'])&&!empty($_POST['target'])?$_POST['target']:Authorization::UnsupportedRequest();
        if(mysqli_num_rows($this->GetRowMultyConditions('notification',['id','reciever_id'],[$id,$user->Info->id]))==0){Authorization::UnsupportedRequest();}
          $Responce = new Responce();
          if (!$this->Update("notification",['deleted_at'],[date('Y-m-d H:i:s')],['id'],$id)) {
            $Responce->status = false;
            $Responce->Send();
          }
        $Responce->status = true;
        $Responce->Send();
        break;
        case 'read':
          $id = isset($_POST['notification_id'])&&!empty($_POST['notification_id'])?$_POST['notification_id']:Authorization::UnsupportedRequest();
          if(mysqli_num_rows($this->GetRowMultyConditions('notification',['id','reciever_id'],[$id,$user->Info->id]))==0){Authorization::UnsupportedRequest();}
            $Responce = new Responce();
            if (!$this->Update("notification",['is_read'],[1],['id'],$id)) {
              $Responce->status = false;
              $Responce->Send();
            }
          $Responce->status = true;
          $Responce->Send();
          break;
    }
  }
  public function Notification($key)
  {
    $user = $this->RequireLogin('notifications');
    $res = $this->GetRows("notification","reciever_id",$user->Info->id," AND `deleted_at` is NULL AND `celled`= 0 order by `id` DESC");
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->username = "";
    $Responce->userimage = "";
    if(mysqli_num_rows($res)>0){
      $row = mysqli_fetch_assoc($res);
       $this->Update("notification",['celled'],['1'],['id'],$row['id']);
       $Responce->title = "دكتور تك";
       $Responce->ticker = "اشعار جديد - دكتور تك";
       $Responce->message = $row['message'];
       $Responce->note = $row['created_at'];
       $Responce->code = 1;
       $Responce->Send();
    }else {
        $res = $this->GetRows("chat","receiver",$user->Info->id," AND `notic`= 0");
        if(mysqli_num_rows($res)>0){
           $row = mysqli_fetch_assoc($res);
           $SenderUser = new user($row["sender"]);
           $this->Update("chat",['notic'],['1'],['id'],$row['id']);
           $Responce->title = "رسالة جديدة من " . (new user($row["sender"]))->Info->name;
           $Responce->ticker = $SenderUser->Info->name;
           $Responce->message = $row['message'];
           $Responce->note = $row['created_at'];
           $Responce->code = $row['order_id'];
           $Responce->username = $SenderUser->Info->name;
           $Responce->userimage = $SenderUser->Info->image;
           $Responce->Send();
        }
    }
    $Responce->message = "";
    $Responce->Send();
  }
  public function Notifications()
  {
    $user = $this->RequireLogin('notifications');
    $res = $this->GetRows("notification","reciever_id",$user->Info->id," AND `deleted_at` is NULL");
    $Items = [];
    while ($row = mysqli_fetch_assoc($res)) {
      $row['sender'] = new EngineerObject($row['sender_id']);
      // $row['receiver'] = new EngineerObject($row['reciever_id']);
      // $row['order'] = new OrderObject($row['object_id']);
      $row['timeStemp'] = (strtotime(Date("Y-m-d H:i:s")) - strtotime($row['created_at'])) * 1000;
      $Items[] = $row;
    }
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);;
    $Responce->items = array_reverse($Items);
    $Responce->Send();
  }
  public function logout()
  {
    $user = $this->RequireLogin('logout');
    $Responce = new Responce();
    $Responce->status = $this->DeleteRow('oauth_access_tokens',['id'],Authorization::Key());
    $Responce->Send();
  }
  public function info()
  {
    $Responce = new Responce();
    $user = $this->RequireLogin('info');
    $row = mysqli_fetch_assoc($this->GetRows("users",'id',$user->Info->id));
    $row["remember_token"] = Authorization::Key();
    $Responce->status = true;
    $Responce->item = new UserObject($row);
    $Responce->Send();
  }
  public function OrderInfo()
  {
    $Responce = new Responce();
    $user = Authorization::Token();
    $id = isset($_POST['order_id'])&&!empty($_POST['order_id'])?$_POST['order_id']:Authorization::UnsupportedRequest();
    $res = $this->GetRows("ordersrequests",'id',$id);
    $Responce->item = new OrderObject(mysqli_fetch_assoc($this->GetRows("ordersrequests",'id',$id)));
    if ($Responce->item->user_id!=$user->Info->id && $Responce->item->acceptoffer->user_id!=$user->Info->id) {
      $Responce->item = null;
    }
    $user = $this->RequireLogin('info');
    $Responce->status = true;
    $Responce->id = $id;
    $Responce->valid = mysqli_num_rows($res)>0;
    $Responce->Send();
  }
  public function login()
  {
    $Responce = new Responce();
    $this->email = $_REQUEST['username'];
    $this->password = Password::Hash($_REQUEST['password']);
    $res = $this->GetRowMultyConditions('users',['password','phone'],[$this->password,$this->email]);
    if (mysqli_num_rows($res)==0) {
      $phone = strlen($this->email)==10?substr($this->email,1):$this->email;
      $res = $this->GetRowMultyConditions('users',['password','phone'],[$this->password,$phone]);
      if(mysqli_num_rows($res)==0){$Responce->status = false; $Responce->Send();}
    }
    $row = mysqli_fetch_assoc($res);
    $this->Info = json_decode(json_encode($row));
    $this->id = $this->Info->id;
    $row["remember_token"] = $this->SetToken();
    $Responce->status = true;
    $Responce->item = new UserObject($row);
    $Responce->Send();
  }
  protected function SetToken(){
    $token =  $this->RandomToken(32) . $this->Info->id;
    if (!$this->Inject('oauth_access_tokens',['id','user_id','client_id','scopes','revoked','expires_at'],
                                             [$token,$this->Info->id,$_REQUEST['client_id'],'[]','0',date('Y-m-d', strtotime('+1 years'))])) {return "";}
    return $token;
   }
  protected function RefrashToken($i){
    $token =  $this->RandomToken(32) . $this->Info->id;
    if (!$this->Inject('oauth_refresh_tokens',['id','access_token_id','expires_at','revoked'],
                                              [$token,$i,date('Y-m-d', strtotime('+1 years')),'0'])) {return "";}
    return $token;
   }
   public function Distance($Lat_1,$Log_1,$Lat_2,$Log_2){
        $PI = pi();
        $Earth_Radius = 6378.137;
        $DLat = $Lat_2 * $PI / 180 - $Lat_1 * $PI / 180;
        $DLog = $Log_2 * $PI / 180 - $Log_1 * $PI / 180;
        $A  = sin($DLat/2) *  sin($DLat/2) +
        cos($Lat_1 * $PI / 180) * cos($Lat_2 * $PI / 180) *
        sin($DLog/2) * sin($DLog/2);
        $C =  2 * atan2(sqrt($A),sqrt(1-$A));
        $D = $Earth_Radius * $C;
        return $D;
    }
  protected function RequireLogin($method)
  {
      if (in_array($method,RequireLoginMethods)) {
         $user = Authorization::Token();
         if (!$user||!$user->valid){
           Authorization::UserAuthoFail();
         }
         return $user;
      }else {
        Logger::T($method);
      }
  }

}

/**
 *
 */
class sheared extends DBHelper
{
  public function mobile($args,$key=0)
  {
    switch ($args) {
      case 'types':
        $this->GetTypes();
        break;
      case 'type':
        $this->SubType($key);
        break;
    }
  }
  public function Parteners()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->Partners = [];
    $res = $this->GetTable('parteners');
    while ($row = mysqli_fetch_assoc($res)) {
       $row["icon"] = base64_encode(file_get_contents('./app/storage/logos/' . $row["photo_logo"]));
       $Responce->Partners[] = $row;
    }

    $Responce->Send();
  }
  public function search($key)
  {
    $search = isset($_POST['search'])&&!empty($_POST['search'])?$_POST['search']:Authorization::UnsupportedRequest();
    switch ($key) {
      case 'Product':
        $res = $this->LikeRowMultyFlags('products',['description','product_name'],$search,' order by `id` DESC LIMIT 30');
        $Responce = new Responce();
        $Responce->status = true;
        $Responce->items = $this->FetchArrayObjects('ProductObject',$res);
        $Responce->page_count =mysqli_num_rows($res);
        $Responce->Send();
      break;
      case 'Post':
        $user = Authorization::Token();
        $res = $this->LikeRowMultyFlags("posts",['description','title'],$search," order by `id` DESC LIMIT 30");
        $Items = [];
        while ($row = mysqli_fetch_assoc($res)) {
          $row["is_like"] =($user->valid)?mysqli_num_rows($this->GetRowMultyConditions('likeposts',['user_id','post_id'],[$user->Info->id,$row["id"]]))>0:false;
          $row["user"] = mysqli_fetch_assoc($this->GetRows('users','id',$row["user_id"]));
          $row["url_file"] = ImagesRoot . $row["file"];
          $row["comments"] = [];
           $res_2 =   $this->GetRows('comments','post_id',$row["id"]," order by `id` DESC ");
           while ($row_2 = mysqli_fetch_assoc($res_2)) {
             $row_2['user'] = mysqli_fetch_assoc($this->GetRows('users','id',$row_2["user_id"]));
             $row["comments"][] = $row_2;
           }
          $Items[] = $row;
        }
        $Responce = new Responce();
        $Responce->status = true;
        $Responce->page_count = mysqli_num_rows($res);;
        $Responce->items = $Items;
        $Responce->Send();
      break;
    }
  }
  public function terms()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->terms = base64_decode((new Settings())->Info->terms);
    $Responce->Send();
  }
  public function service_desc()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->desc =  base64_decode((new Settings())->Info->service_description);
    $Responce->Send();
  }
  public function aboutapp()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->about = base64_decode((new Settings())->Info->aboutapp);
    $Responce->Send();
  }
  public function statics()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->item = (new Settings())->Info;
    $Responce->Send();
  }
  public function reasons()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->items = $this->FetchArray($this->GetTable('reasonscontacts'));
    $Responce->Send();
  }
  public function ServesTypes()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->items = $this->FetchArray($this->GetTable('offer_service_type'));
    $Responce->Send();
  }
  public function products($key='')
  {
    switch ($key) {
      case 'category':
        $id = isset(func_get_args()[1])&&!empty(func_get_args()[1])?func_get_args()[1]:Authorization::UnsupportedRequest();
        $res = $this->GetRows('products','category_id',$id);
        $Responce = new Responce();
        $Responce->status = true;
        $Responce->items = $this->FetchArrayObjects('ProductObject',$res);
        $Responce->page_count =mysqli_num_rows($res);
        $Responce->Send();
        break;
      default:
        break;
    }

  }
  public function PiceTypes()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->items = $this->FetchArray($this->GetTable('offer_piece_type'));
    $Responce->Send();
  }
  public function offerGaranty()
  {
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->items = $this->FetchArray($this->GetTable('offer_garanty'));
    $Responce->Send();
  }
  public function files($folder = false,$fileName =false,$chat_img=false){
   if (!$folder||!$fileName) {Authorization::UnsupportedRequest();  }
   if(!$chat_img){
      $path =  STORAGE . $folder . DS . $fileName;
     }else{
       $path = STORAGE . $folder . DS . $fileName . DS .$chat_img;
    }
    if(file_exists($path)){
       header('Content-type: Image/jpeg');
      $img = file_get_contents($path);
       if (isset($_GET['size'])) {
          $im = imagecreatefromstring($img);
          $width = imagesx($im);
          $height = imagesy($im);
          $newwidth = $_GET['size'];
          $newheight = $height * ($newwidth/$width);
          $thumb = imagecreatetruecolor($newwidth, $newheight);
          imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
          if (isset($_GET['bluer'])) {
            for ($x=1; $x<=50; $x++){
                  imagefilter($thumb, IMG_FILTER_GAUSSIAN_BLUR);
             }
          }
          imagejpeg($thumb) ;
          imagedestroy($thumb);
          imagedestroy($im);
       }else {
         echo $img;
       }
    }else {
      echo Authorization::UnsupportedRequest();
    }

  }

public function postInfo()
{
  $id = isset($_POST['post_id'])&&!empty($_POST['post_id'])?$_POST['post_id']:Authorization::UnsupportedRequest();
  $Items = [];
  $res_2 =   $this->GetRows('comments','post_id',$id," order by `id` DESC");
  while ($row_2 = mysqli_fetch_assoc($res_2)) {
    $user_row = mysqli_fetch_assoc($this->GetRows('users','id',$row_2["user_id"]));
    $row_2['user'] = ["image"=>$user_row['image'],"id"=>$user_row['id'],"name"=>$user_row["name"]] ;
    $Items [] = $row_2;
  }
  $Responce = new Responce();
  $Responce->status = true;
  $Responce->items = $Items;
  $Responce->Send();

}
  public function posts($page)
  {
     $header = apache_request_headers();
     $start = ($page - 1 )  * MAX_IN_PAGE;
     $Limit = $start + MAX_IN_PAGE;
     $user = Authorization::Token();
     $res = $this->GetTable("posts"," order by `id` DESC LIMIT $start , $Limit");
     $Items = [];
   while ($row = mysqli_fetch_assoc($res)) {
     $user_row = mysqli_fetch_assoc($this->GetRows('users','id',$row["user_id"]));
     $row['user'] = ["image"=>$user_row['image'],"id"=>$user_row['id'],"name"=>$user_row["name"]] ;
     $row["is_like"] =($user->valid)?mysqli_num_rows($this->GetRowMultyConditions('likeposts',['user_id','post_id'],[$user->Info->id,$row["id"]]))>0:false;
     $row["url_file"] = ImagesRoot . $row["file"];
     $row["comments"] = mysqli_num_rows($this->GetRows('comments','post_id',$row["id"]));
     $row["likes"] =  mysqli_num_rows($this->GetRows('likeposts','post_id',$row["id"]));
     $Items[] = $row;
   }
   $Responce = new Responce();
   $Responce->status = true;
   $Responce->page_count = mysqli_num_rows($res);;
   $Responce->items = $Items;
   $Responce->Send();
  }
  public function advertisements()
  {
    $res = $this->GetTable("advertisments");
    $Items = [];
    while ($row = mysqli_fetch_assoc($res)) {
      $row['url_image'] =  ImagesRoot . $row['image'];
      $Items[] = $row;
    }
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);
    $Responce->items = $Items;
    $Responce->Send();
  }
  public function home($key)
  {
    switch ($key) {
      case 'products':
          $this->GetHomeProducts();
        break;
    }
  }
  protected function GetHomeProducts()
  {
    $user = Authorization::Token();
    $res = $this->GetTable("categoryproducts");
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);
    $Responce->items = $this->FetchArray($res);
    $Responce->Send();
  }
  public function problems()
  {
    $res = $this->GetTable("problems");
    $Items = [];
    while ($row = mysqli_fetch_assoc($res)) {$Items[] = $row;}
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);;
    $Responce->items = $Items;
    $Responce->Send();
  }
  public function citys()
  {
    $res = $this->GetTable("towns");
    $Items = [];
    while ($row = mysqli_fetch_assoc($res)) {$Items[] = $row;}
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);;
    $Responce->items = $Items;
    $Responce->Send();
  }
  public function street($key)
  {
    $res = $this->GetRows("cities","id_town",$key);
    $Items = [];
    while ($row = mysqli_fetch_assoc($res)) {$Items[] = $row;}
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = mysqli_num_rows($res);;
    $Responce->items = $Items;
    $Responce->Send();
  }
public function SubType($key)
{
  $res = $this->GetRows("modelmobiles","type_id",$key);
  $Items = [];
  while ($row = mysqli_fetch_assoc($res)) {$Items[] = $row;}
  $Responce = new Responce();
  $Responce->status = true;
  $Responce->page_count = mysqli_num_rows($res);;
  $Responce->items = $Items;
  $Responce->Send();
}
protected function GetTypes()
{
  $res = $this->GetTable("typemobiles");
  $Items = [];
  while ($row = mysqli_fetch_assoc($res)) {$Items[] = $row;}
  $Responce = new Responce();
  $Responce->status = true;
  $Responce->page_count = mysqli_num_rows($res);;
  $Responce->items = $Items;
  $Responce->Send();

}
public function GetAllOptions($value='')
{
  $Responce = new Responce();
  $Responce->status = true;
  $Responce->PhoneTypes = $this->FetchArrayObjects("PhoneTypeObject",$this->GetTable("typemobiles"));
  $Responce->Problems = $this->FetchArray($this->GetTable("problems"));
  $Responce->PieceType = $this->FetchArray($this->GetTable("offer_piece_type"));
  $Responce->ServiceType = $this->FetchArray($this->GetTable("offer_service_type"));
  $Responce->Garanty = $this->FetchArray($this->GetTable("offer_garanty"));
  $Responce->Citys = $this->FetchArrayObjects("CityObject",$this->GetRows("towns","hide","0"));
  $Responce->ServiceDescription =  base64_decode((new Settings())->Info->service_description);
  $Responce->Send();
}
}



















/**
 *
 */
 class Notification extends DBHelper{
   static function NoticAllEngineers($_id){
     $DBHelper = new DBHelper();
     $Order_object = new OrderObject($_id);
     $res = $DBHelper->GetRows("users","type","engineer");
     while ($row = mysqli_fetch_assoc($res)){
       $user = new User($row['id']);
       $Distance = $user->Distance($Order_object->location_latitude,$Order_object->location_longitude,
                                     $user->Info->location_latitude,$user->Info->location_longitude);
       $notic = $Distance>1?NEW_ORDER_NOTIC . " " .  round($Distance)  . " " . KM_UINT:NEW_ORDER_NOTIC . " " . round($Distance*1000) . " " . M_UINT;
        $MaxDistance = $user->Info->Distance>0?$user->Info->Distance:(new Settings())->Info->Distance;
       if ($Distance <= $MaxDistance  ) {
         $DBHelper->Inject('notification',['object_id','message_id','type_id','sender_id','reciever_id','created_at'],
                                     [$_id,169,'1',$Order_object->user_id,$user->Info->id,Date("Y-m-d H:i:s")]);
         $user->SendNotification("طلب صيانة جيد",$notic);
       }
     }
  }
  static function NoticAllAdmins(){
    $DBHelper = new DBHelper();
    $res = $DBHelper->GetRows("users","role_id","1");
    while ($row = mysqli_fetch_assoc($res)){
      $user = new User($row['id']);
        $user->SendNotification("طلب إنضمام جديد","تم استقبال طلب انضمام جديد من طرف " . $user->Info->name);
      }
    }
}
class Password {static function Hash($pass){return sha1($pass);}}
class Responce{  function Send(){header('Content-type: text/json');die(json_encode($this));}}
class Logger {static function T($t){file_put_contents(log_file,file_get_contents(log_file) . "\n". $t);}static function O($t){file_put_contents(log_file,file_get_contents(log_file) . "\n". json_encode($t));}}
class AllowedRequests {static function Check($parm){return in_array(strtolower($parm),Allowed);}}
class Authorization {
    static function Token(){
      $header = apache_request_headers();
      $Authorization = isset($header['X-Authorization'])?$header['X-Authorization']:false;
      if (!$Authorization) {return false;}
      return new User($Authorization);
    }
    static function FillAllBlanks()
    {
      $Responce = new Responce();
      $Responce->status = false;
      $Responce->messageid = 3;
      $Responce->Send();
    }
    static function Key(){
      $header = apache_request_headers();
      $Authorization = isset($header['X-Authorization'])?$header['X-Authorization']:false;
      return $Authorization;
    }
    static function AuthoFail()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1000;
      $Error->messageid = 212;
      $Error->support = Support;
      $Error->Send();
    }
    static function UnsupportedRequest()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1001;
      $Error->messageid = 213;
      $Error->support = Support;
      $Error->Send();
    }
    static function UserAuthoFail()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1002;
      $Error->messageid = 214;
      $Error->support = Support;
      $Error->Send();
    }
    static function UnsuccessfulRequest()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1003;
      $Error->messageid = 215;
      $Error->support = Support;
      $Error->Send();
    }

    static function UnsuccessfulOperation()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1004;
      $Error->message = "Unsuccessful Operation , please check your inputs . you can get help following the support link";
      $Error->support = Support;
      $Error->Send();
    }
    static function UnknownError()
    {
      $Error = new Responce();
      $Error->status = false;
      $Error->code = 1005;
      $Error->message = "Unknown Error Error . Please Connect Us as Soon as possible";
      $Error->support = Support;
      $Error->Send();
    }
    static function CheckAuthoRequest($autho,$client_id)
    {
      return true;
    }
}


/**
 * Orders
 */
class Orders extends DBHelper
{
  function __construct($user)
  {
   $this->user = $user;
  }
  public function AddNew(){
    $type_problem_id = isset($_POST['type_problem_id'])?$_POST['type_problem_id']:Authorization::UnsuccessfulRequest();
    $type_mobile_id = isset($_POST['type_mobile_id'])?$_POST['type_mobile_id']:Authorization::UnsuccessfulRequest();
    $model_mobile_id = isset($_POST['model_mobile_id'])?$_POST['model_mobile_id']:Authorization::UnsuccessfulRequest();
    $description_problem = isset($_POST['description_problem'])?$_POST['description_problem']:Authorization::UnsuccessfulRequest();
    $location_longitude = isset($_POST['location_longitude'])?$_POST['location_longitude']:Authorization::UnsuccessfulRequest();
    $location_latitude = isset($_POST['location_latitude'])?$_POST['location_latitude']:Authorization::UnsuccessfulRequest();
    $note = isset($_POST['note'])?$_POST['note']:Authorization::UnsuccessfulRequest();
    $town = isset($_POST['town'])?$_POST['town']:Authorization::UnsuccessfulRequest();
    $city = isset($_POST['city'])?$_POST['city']:Authorization::UnsuccessfulRequest();
    $street = isset($_POST['street'])?$_POST['street']:Authorization::UnsuccessfulRequest();
    $Images = isset($_POST['Images'])?json_decode($_POST['Images']):json_decode("[]");
    $_id = $this->Inject('ordersrequests',["user_id","description_problem","location_longitude","location_latitude","type_mobile_id","model_mobile_id","type_problem_id","created_at","updated_at","note","town","city","street"],
                                       [$this->user->Info->id,$description_problem,$location_longitude,$location_latitude,$type_mobile_id,$model_mobile_id,$type_problem_id,date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$note,$town,$city,$street],true);
    if(!$_id){Authorization::UnsuccessfulOperation();}
        for ($i=0; $i < count($Images); $i++) {
          $this->UpdateMultyConditions("images",["object"],[$_id],["id","user_id"],[$Images[$i],$this->user->Info->id]);
        }
     Notification::NoticAllEngineers($_id);
     $Responce = new Responce();
     $Responce->status = true;
     $Responce->Send();

  }
  public function GetAll()
  {
    if ($this->user->Info->type=="user") {
      $res = $this->GetRows("ordersrequests","user_id",$this->user->Info->id," AND `deleted_at` is null order by `id` DESC");
    }else{
      $res = $this->JoindTables("ordersoffers","ordersrequests",["*"],[],"id","orderoffer_id","user_id",$this->user->Info->id," AND `ordersrequests`.`deleted_at` is null  order by `id` DESC");
    }
    $Responce = new Responce();
    $Responce->status = true;
    $Responce->page_count = 0;
    $Responce->items = $this->FetchArrayObjects("OrderObject",$res);
    $Responce->Send();
  }
  public function GetOthers()
  {
    $page_indx  = $_POST['pages'] ?? 1;
    $Page_count = (new Settings())->Info->pages;
    $start_from = $Page_count  * ($page_indx - 1);
    $end_At = $Page_count;
    $Responce = new Responce();
    $res = $this->GetRowsNot("ordersrequests",'user_id',$this->user->Info->id," AND `status` = 'WAITING' AND `deleted_at` is Null order by `id` DESC  LIMIT " . $start_from . "," . $end_At);
    $this->Count = mysqli_num_rows($res);
    $Items = [];
    $Responce->page_count = mysqli_num_rows($res);
    $Responce->status = true;
    $Responce->page = $page_indx;
    $Responce->Page_count = $Page_count;
    $Responce->items = [];
    $tmp_items  = $this->FetchArrayObjects("OrderObject",$res);
    for ($i=0; $i < count($tmp_items); $i++) {
       $Order_object = $tmp_items[$i];
       $Distance = $this->user->Distance($Order_object->location_latitude,$Order_object->location_longitude,$this->user->Info->location_latitude,$this->user->Info->location_longitude);
       $MaxDistance = $this->user->Info->Distance>0?$this->user->Info->Distance:(new Settings())->Info->Distance;
       if ($Distance <= $MaxDistance  ) {
         $Responce->items[] = $Order_object;
       }
    }
    $Responce->Send();
  }
}

/**
 *
 */
class JoinRequest extends DBHelper
{

  function __construct($user)
  {

    $Responce = new Responce();
    if (mysqli_num_rows($this->GetRows("joining_requests","user_id",$user->Info->id))>0) {
      $Responce->status = true;
      $Responce->message = 3102;
      $Responce->Send();
    }
    $this->FirstName = isset($_POST['First_name'])&&!empty($_POST['First_name'])?$_POST['First_name']:$this->FillAllBlanks();
    $this->Family_name = isset($_POST['Family_name'])&&!empty($_POST['Family_name'])?$_POST['Family_name']:$this->FillAllBlanks();
    $this->Father_Name = isset($_POST['Father_name'])&&!empty($_POST['Father_name'])?$_POST['Father_name']:$this->FillAllBlanks();
    $this->GrandPa_name = isset($_POST['GrandPa_name'])&&!empty($_POST['GrandPa_name'])?$_POST['GrandPa_name']:$this->FillAllBlanks();
    $this->BirthDay = isset($_POST['BirthDay'])&&!empty($_POST['BirthDay'])?$_POST['BirthDay']:$this->FillAllBlanks();
    $this->ID_Number = isset($_POST['ID_Number'])&&!empty($_POST['ID_Number'])?$_POST['ID_Number']:$this->FillAllBlanks();
    $this->Main_Phone = isset($_POST['Main_Phone'])&&!empty($_POST['Main_Phone'])?$_POST['Main_Phone']:$this->FillAllBlanks();
    $this->town_id = isset($_POST['user_town_id'])&&!empty($_POST['user_town_id'])?$_POST['user_town_id']:$this->FillAllBlanks();
    $this->city_id = isset($_POST['user_city_id'])&&!empty($_POST['user_city_id'])?$_POST['user_city_id']:$this->FillAllBlanks();
    $this->user_lang = isset($_POST['user_long'])&&!empty($_POST['user_long'])?$_POST['user_long']:$this->FillAllBlanks();
    $this->user_lat = isset($_POST['user_lat'])&&!empty($_POST['user_lat'])?$_POST['user_lat']:$this->FillAllBlanks();
    $this->Extra_Phone_1 = json_encode(["number"=>$_POST['Extra_Phone_1'],"name"=>$_POST['Extra_Phone_1_name']]);
    $this->Extra_Phone_2 = json_encode(["number"=>$_POST['Extra_Phone_2'],"name"=>$_POST['Extra_Phone_2_name']]);
    $this->Extra_Phone_3 = json_encode(["number"=>$_POST['Extra_Phone_3'],"name"=>$_POST['Extra_Phone_3_name']]);
    $this->Extra_Phone_4 = json_encode(["number"=>$_POST['Extra_Phone_4'],"name"=>$_POST['Extra_Phone_4_name']]);
    $this->Work_Place = isset($_POST['Work_Place'])?$_POST['Work_Place']:$this->FillAllBlanks();
    $this->Is_WorkShop = isset($_POST['Is_WorkShop'])?$_POST['Is_WorkShop']:$this->FillAllBlanks();
    $this->Is_one_person  = isset($_POST['Is_one_person'])?$_POST['Is_one_person']:$this->FillAllBlanks();
    $this->Is_Car_shop = isset($_POST['Is_Car_shop'])?$_POST['Is_Car_shop']:$this->FillAllBlanks();
    $this->Is_home_servce = isset($_POST['Is_home_servce'])?$_POST['Is_home_servce']:$this->FillAllBlanks();
    $this->Is_Delvery_servce = isset($_POST['Is_Delvery_servce'])?$_POST['Is_Delvery_servce']:$this->FillAllBlanks();
    $this->Is_Company = isset($_POST['Is_Company'])?$_POST['Is_Company']:$this->FillAllBlanks();

    $Face_Image =  !empty($_FILES['Face_Image_file']['name'])?$_FILES['Face_Image_file']:$this->FillAllBlanks();
    $ID_Image =  !empty($_FILES['ID_Image_file']['name'])?$_FILES['ID_Image_file']:$this->FillAllBlanks();
    $CV_File =  !empty($_FILES['CV_File_file']['name'])?$_FILES['CV_File_file']:$this->FillAllBlanks();

    $this->Face_Image_name = uniqid() . '.jpg';
    $this->ID_Image_name = uniqid() . '.jpg';
    $this->CV_Image_name = uniqid() . '.jpg';
    if ($this->UploadImage($this->Face_Image_name,$Face_Image) != 3) {
      $Responce->status = false;
      $Responce->message = "حدث خطأ في رفع الصورة الشخصية . تأكد من انك ترفع الصيغة الصحيحة";
      $Responce->Send();
    }
    if ($this->UploadImage($this->ID_Image_name,$ID_Image) != 3) {
      $Responce->status = false;
      $Responce->message = "حدث خطأ في رفع ملف الهوية . تأكد من ان الملف صالح";
      $Responce->Send();
    }
    if ($this->UploadImage($this->CV_Image_name,$CV_File) != 3) {
      $Responce->status = false;
      $Responce->message = "حدثت مشكلة في رفع السيرة الذاتية . تأكد من اختيارك للملف الصحيح";
      $Responce->Send();
    }
    $results = $this->Inject('joining_requests',["first_name","father_name","g_father_name","family_name","birth_date","identity",
    "principal_phone","phone_1","phone_2","phone_3","phone_4","work_place","offering_service","photo","cv","personel_photo","status","user_id",
    "Is_one_person","Is_Car_shop","Is_home_servce","Is_Delvery_servce","Is_Company","town_id","city_id"],
                                     [$this->FirstName,$this->Father_Name,$this->GrandPa_name,$this->Family_name,$this->BirthDay,
                                      $this->ID_Number,$this->Main_Phone,$this->Extra_Phone_1,$this->Extra_Phone_2,$this->Extra_Phone_3,
                                      $this->Extra_Phone_4,$this->Work_Place,$this->Is_WorkShop,$this->ID_Image_name,$this->CV_Image_name,$this->Face_Image_name,'0',
                                      $user->Info->id,$this->Is_one_person,$this->Is_Car_shop,$this->Is_home_servce,$this->Is_Delvery_servce,$this->Is_Company,$this->town_id,$this->city_id]) &&
     $this->update('users',['location_latitude','location_longitude'],[$this->user_lat,$this->user_lang],['id'],$user->Info->id);
     Notification::NoticAllAdmins();
      if (!$results) {
        $Responce->status = false;
        $Responce->message = "حدث خطأ في ارسال الطلب يرجى التوصال معنا لحل المشكلة";
        $Responce->Send();
      }
      $Responce->message = "تم ارسال طلب بنجاح . سيصلك اشعار بعد قبول الطلب من طرف الادارة";
      $Responce->status = true;
      $Responce->Send();
  }
  public function FillAllBlanks()
  {
    $Responce = new Responce();
    $Responce->status = false;
    $Responce->message = "الرجاء تعبئة جميع الخانات الازمة";
    $Responce->Send();
  }
}
/**
 *
 */
class Settings extends DBHelper {function __construct(){$this->Info =json_decode(json_encode(mysqli_fetch_assoc($this->GetRows('settings','id',1))));}}
class OrderObject extends DBHelper {
  function __construct($id)
  {     $U_Me = Authorization::Token();
        $row = $id;
        if (!isset($id['id'])) {$row = mysqli_fetch_assoc($this->GetRows("ordersrequests","id",$id));}
        $this->id = $row['id'];
        $this->price = $row['price'];
        $this->order_code = $row['order_code'];
        $this->description_problem = $row['description_problem'];
        $this->location_address = $row['location_address'];
        $this->location_latitude = $row['location_latitude'];
        $this->location_longitude = $row['location_longitude'];
        $this->status = $row['status'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        $this->deleted_at = $row['deleted_at'];
        $this->type_problem_id = $row['type_problem_id'];
        $this->type_mobile_id = $row['type_mobile_id'];
        $this->model_mobile_id = $row['model_mobile_id'];
        $this->user_id = $row['user_id'];
        $this->evaluation = $row['evaluation'];
        $this->problem = mysqli_fetch_assoc($this->GetRows('problems','id',$row['type_problem_id']));
        $this->model_mobile = mysqli_fetch_assoc($this->GetRows('modelmobiles','id',$row['model_mobile_id']));
        $this->acceptoffer = mysqli_fetch_assoc($this->GetRows('ordersoffers','id',$row['orderoffer_id']));
        $this->Images = $this->FetchArray($this->GetRows('images','object',$row['id']));
        $this->type_mobile = mysqli_fetch_assoc($this->GetRows('typemobiles','id',$row['type_mobile_id']));
        $this->offers =  $this->FetchArrayObjects('OfferObject',$this->GetRows('ordersoffers','order_id',$row['id']," AND `deleted_at` is null order by `id` DESC"));
        $this->note = $row['note'];
        $this->town = mysqli_fetch_assoc($this->GetRows('towns','id',$row['town']));
        $this->city = mysqli_fetch_assoc($this->GetRows('cities','id',$row['city']));
        $this->street = $row['street'];
        $this->orderoffer_id = $row['orderoffer_id'];
        $this->user = new EngineerObject($row['user_id']);
        $this->acceptoffer =  new OfferObject($row['orderoffer_id']);
        $this->engineer =  $this->acceptoffer->engineer;
        $this->timeStemp =  (strtotime(Date("Y-m-d H:i:s")) - strtotime($row['created_at'])) * 1000;
        $this->IsOffered = mysqli_num_rows($this->GetRowMultyConditions('ordersoffers',['user_id','order_id'],[$U_Me->Info->id,$row['id']]))>0;
        $this->distance =  $U_Me->Distance($this->location_latitude,$this->location_longitude,$U_Me->Info->location_latitude,$U_Me->Info->location_longitude);
        $this->MyOffers = $this->FetchArrayObjects('OfferObject',$this->GetRows('ordersoffers','order_id',$row['id']," AND `deleted_at` is null and `user_id` = " . $U_Me->Info->id));
        if ($this->user->id==$U_Me->Info->id) {
           $this->OrderOffersCount = mysqli_num_rows($this->GetRows("ordersoffers","order_id",$row['id']," AND `deleted_at` is null"));
        }


  }
}
class OfferObject extends DBHelper {
  function __construct($id)
  {
      $row = $id;
      if (!isset($id['id'])) {$row = mysqli_fetch_assoc($this->GetRows('ordersoffers','id',$id));}
      $this->id = $row['id'];
      $this->order_id = $row['order_id'];
      $this->offer_description = $row['offer_description'];
      $this->serevce_price = $row['serevce_price'];
      $this->piece_price = $row['piece_price'];
      $this->fix_d = $row['fix_d'];
      $this->fix_h = $row['fix_h'];
      $this->fix_m = $row['fix_m'];
      $this->Resive_d = $row['Resive_d'];
      $this->Resive_h = $row['Resive_h'];
      $this->Resive_m = $row['Resive_m'];
      $this->total_price = $row['total_price'];
      $this->extra_note = $row['extra_note'];
      $this->piece_type = mysqli_fetch_assoc($this->GetRows('offer_piece_type','id',$row['piece_type']));
      $this->servce_type =  mysqli_fetch_assoc($this->GetRows('offer_service_type','id',$row['servce_type']));
      $this->offer_garanty =  mysqli_fetch_assoc($this->GetRows('offer_garanty','id',$row['offer_garanty']));
      $this->user_id = $row['user_id'];
      $this->created_at = $row['created_at'];
      $this->deleted_at = $row['deleted_at'];
      $this->resive_time = $row['resive_time'];
      $this->updated_at= $row['updated_at'];
      $this->engineer = new EngineerObject($row['user_id']);
  }
}
class UserObject extends DBHelper {
  function __construct($id)
  {
      $row = $id;
      if (!isset($id['id'])) {$row = mysqli_fetch_assoc($this->GetRows('users','id',$id));}
      $this->id = $row['id'];
      $this->name = $row['name'];
      $this->phone = "0" . $row['phone'];
      $this->remember_token = $row['remember_token'];
      $this->updated_at = $row['updated_at'];
      $this->role_id = $row['role_id'];
      $this->username = $row['username'];
      $this->Distance = $row['Distance'];
      $this->Address = $row['Address'];
      $this->Is_phone_activated = $row['Is_phone_activated'];
      $this->type = $row['type'];
      $this->image = $row['image'];
      $this->approved = $row['approved'];
      $this->balance = $this->GetSUM("transactions","amount","user_id",$row['id']);
      $this->completed = $row['completed'];
      $this->town = mysqli_fetch_assoc($this->GetRows('towns','id',(mysqli_fetch_assoc($this->GetRows('joining_requests','user_id',$row['id']))['town_id'])));
      $this->city = mysqli_fetch_assoc($this->GetRows('cities','id',(mysqli_fetch_assoc($this->GetRows('joining_requests','user_id',$row['id']))['city_id'])));
      $this->rate = 0;
      $res = $this->GetRows("rating","UserId",$this->id);
      while ($row_x = mysqli_fetch_assoc($res)) {  $this->rate += $row_x['Stars'];}
      $count = mysqli_num_rows($res);
      $this->rate = $count>0?$this->rate/$count:0;
      $this->rate = round($this->rate * 100)/100;
      $this->Purchases = mysqli_num_rows($this->GetRows("payments","user_id",$this->id));
      $this->Orders = mysqli_num_rows($this->GetRows("ordersrequests","user_id",$this->id));
      $this->Dealings = $this->GetDealingsCount();
      $this->url_image = Root . 'index.php?url=api/v2/sheared/images/' . $row['image'];
  }
  public function GetDealingsCount()
  {
    if ($this->type != "user") {
      return mysqli_num_rows($this->JoindTablesNoRepeat("ordersoffers","ordersrequests",['user_id'],'id','orderoffer_id','user_id',$this->id));
    }else {
      return mysqli_num_rows($this->JoindTablesNoRepeat("ordersrequests","ordersoffers",['user_id'],'id','order_id','user_id',$this->id));
    }
  }
}
class EngineerObject extends DBHelper {
  function __construct($id)
  {
      $row = $id;
      if (!isset($id['id'])) {$row = mysqli_fetch_assoc($this->GetRows('users','id',$id));}
      $this->id = $row['id'];
      $this->name = $row['name'];
      $this->phone = "0" . $row['phone'];
      $this->remember_token = $row['remember_token'];
      $this->updated_at = $row['updated_at'];
      $this->role_id = $row['role_id'];
      $this->username = $row['username'];
      $this->type = $row['type'];
      $this->image = $row['image'];
      $this->approved = $row['approved'];
      $this->town = mysqli_fetch_assoc($this->GetRows('towns','id',(mysqli_fetch_assoc($this->GetRows('joining_requests','user_id',$row['id']))['town_id'])));
      $this->city = mysqli_fetch_assoc($this->GetRows('cities','id',(mysqli_fetch_assoc($this->GetRows('joining_requests','user_id',$row['id']))['city_id'])));
      $this->rate = 0;
      $res = $this->GetRows("rating","UserId",$this->id);
      while ($row_x = mysqli_fetch_assoc($res)) {
        $this->rate += $row_x['Stars'];
      }
      $count = mysqli_num_rows($res);
      $this->rate = $count>0?$this->rate/$count:0;
      $this->rate = round($this->rate * 100)/100;
      $this->url_image = Root . 'index.php?url=api/v2/sheared/images/' . $row['image'];
  }
}
class ProductObject extends DBHelper {
  function __construct($id)
  {
    $row = $id;
    if (!isset($id['id'])) {$row = mysqli_fetch_assoc($this->GetRows('products','id',$id));}
        $this->id = $row['id'];
        $this->product_name = $row['product_name'];
        $this->price = $row['price'];
        $this->description = $row['description'];
        $this->product_code = $row['product_code'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        $this->deleted_at = $row['deleted_at'];
        $this->category_id = $row['category_id'];
        $this->available = $row['available'];
        $this->wait = $row['wait'];
        $this->shipping = (new Settings())->Info->fee;
        $this->product_image = $row['product_image'];
        $this->product_image_2 = $row['product_image_2'];
        $this->product_image_3 = $row['product_image_3'];
        $this->product_image_4 = $row['product_image_4'];
        $this->product_image_5 = $row['product_image_5'];
        $this->images  = [
          ImagesRoot . $row['product_image'],
          ImagesRoot . $row['product_image_2'],
          ImagesRoot . $row['product_image_3'],
          ImagesRoot . $row['product_image_4'],
          ImagesRoot . $row['product_image_5'],
        ];
        $this->is_evaluate = false;
        $this->total_evaluate = 0;
        $this->evaluation = [];
  }
}
class TransactionObject  extends DBHelper
{

  function __construct($row)
  {
    $this->id = $row['id'];
    $this->user_id = $row['user_id'];
    $this->amount = $row['amount'];
    $this->offer_id = $row['offer_id'];
    $this->cut_from = $row['cut_from'];
    $this->transaction_id = $row['transaction_id'];
    $this->delated_at = $row['delated_at'];
    $this->created_at =  $row['created_at'];
    $this->order_id =  mysqli_fetch_assoc($this->GetRows("ordersrequests","orderoffer_id",$row['offer_id']))['id'];
  }
}
class PhoneTypeObject extends DBHelper
{
  function __construct($row)
  {
    $this->id = $row['id'];
    $this->Text = $row['brand'];
    $this->Types = $this->FetchArray($this->GetRows("modelmobiles","type_id",$row['id']));

  }
}
class CityObject extends DBHelper
{
  function __construct($row)
  {
    $this->id = $row['id'];
    $this->Text = $row['name'];
    $this->Types = $this->FetchArray($this->GetRows("cities","id_town",$row['id']));

  }
}

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
  while ($row = mysqli_fetch_assoc($res)) {
    $object ='\\'. $className;
    $data[] = new $object($row);
  
  }
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
