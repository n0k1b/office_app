<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use App\debit_account;
use App\credit_account;
use App\token;
class UserController extends Controller
{
    //
    public function test()
    {
        return 2;
    }
    
    public function login(Request $request)
    {
        $a = user::where('name','=',$request->user_name)->where('password','=',$request->password)->first();
        if($a)
        {
            $status_code = '200';
            $user_id =$a->id;
            $name = $request->user_name;
            return response()->json(['status_code'=>$status_code,'user_id'=>$user_id,'name'=>$name]);
        }
        else
        {
            return response()->json(['status_code'=>404]);
        }
        
    }
    public function get_credit_list(Request $request)
    {
        $a = DB::table('credit_type')->get();
        return response()->json(['credit_type'=>$a]);
    }
    
    public function send_debit_data(Request $request)
    {
        //file_put_contents('test_debit_data.txt',$request);
        date_default_timezone_set("Asia/Dhaka");
        $date = date("m-d-Y");
        $month = date('M');
        $year = date ('Y');
         $user_id = $request->user_id;
       $name = user::where('id','=',$user_id)->first()->name;
       
       $debit_type_id = $request->debit_type;
     $debit_name =  DB::table('debit_type')->where('id','=',$debit_type_id)->first()->name;
       
       
        debit_account::create(['user_id'=>$request->user_id,'debit_type_id'=>$request->debit_type,'debited_to_id'=>$request->debited_to,'amount'=>$request->amount,'note'=>$request->note,'month'=>$month,'date'=>$date,'year'=>$year]);
         $this->push_notification('Debit','Debited by: '.$name."\n".'Amount: '.$request->amount."\n".'Type: '.$debit_name."\n".'Note: '.$request->note);
        return response()->json(['status_code'=>200]);
    }
    
    public function get_history_yearly(Request $request)
    {
        $year = $request->year;
        $month = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $response = array();
        for($i=0;$i<sizeof($month);$i++)
        {
            $credit = credit_account::where('month','=',$month[$i])->where('year','=',$year)->get()->sum('amount');
            $debit = debit_account::where('month','=',$month[$i])->where('year','=',$year)->get()->sum('amount'); 
            $revenue = $credit - $debit;
            array_push($response,['month'=>$month[$i],'credit'=>$credit,'debit'=>$debit,'revenue'=>$revenue]);
        }
        return response()->json($response);
    }
    function push_notification_android($title,$msg) {
         $token = token::get();
    $tokens = array();
    for($i=0;$i<sizeof($token);$i++)
    {
        array_push($tokens,$token[$i]->token);
    }
        
$url = 'https://fcm.googleapis.com/fcm/send';
$api_key = 'AAAAbpwKQKE:APA91bG6ioaccbJRDq1LkDDVtXr4TTLO-Ho3KTsHtfu0-Hy_vOBDrAsNDFTMPmitH7kw-roVeK0hrriWdlE42pKdtIVhe5UZavjwPgZvjfPdnma9QQv3xkidnrA7LkkR-PuppqfnSOaG';
$messageArray = array();
$messageArray["notification"] = array (
    'title' => $title,
    'message' => $msg,
   
);
$fields = array(
    'registration_ids' => $tokens,
    'data' => $messageArray,
);
$headers = array(
    'Authorization: key=' . $api_key, //GOOGLE_API_KEY
    'Content-Type: application/json'
);
// Open connection
$ch = curl_init();
// Set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Disabling SSL Certificate support temporarly
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
// Execute post
$result = curl_exec($ch);
if ($result === FALSE) {
    echo 'Android: Curl failed: ' . curl_error($ch);
}
// Close connection
curl_close($ch);
return $result;
}
    
    
    function push_notification($title,$message)
    { 
    $token = token::get();
    $tokens = array();
    for($i=0;$i<sizeof($token);$i++)
    {
         //$title ="hi";
   // $message = "hello";        
    $image_url ="https://storage.googleapis.com/imp-projects/flower-show/1.2.3/images/splash/frontflower_left.png";

    $path_to_fcm= 'https://fcm.googleapis.com/fcm/send';
    $server_key= "AAAAbpwKQKE:APA91bG6ioaccbJRDq1LkDDVtXr4TTLO-Ho3KTsHtfu0-Hy_vOBDrAsNDFTMPmitH7kw-roVeK0hrriWdlE42pKdtIVhe5UZavjwPgZvjfPdnma9QQv3xkidnrA7LkkR-PuppqfnSOaG";
 
        
        //$key = "eJT2DVfht28:APA91bGFTzjbKS2AsEsrvw4wFeJqU9zFDpff1YUX2hlaL0ZcDDzai1Wamu20yP6UHaz32lbGTWwReDsL6r6PqqzOaeAHzpI-je0Ud2vKNGkFMmlBHbK0HOYhAb-Ks1riXzg29CeDMYTj";
 
        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
		 
		 $fields = array('to' => $token[$i]->token,
            'notification' => array('title'=>$title,'body'=>$message,'sound'=>"default"));
		
		$payload =json_encode($fields);
       
        $curl_session = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
		curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
        
		$result =curl_exec($curl_session);
		
		curl_close($curl_session);
    }
    //file_put_contents('token.txt',json_encode($tokens));
   
	
 
        
    }
    public function send_credit_data(Request $request)
    {
       date_default_timezone_set("Asia/Dhaka");
        $date = date("d-m-Y");
        $month = date('M');
        $year = date ('Y');
         $user_id = $request->user_id;
       $name = user::where('id','=',$user_id)->first()->name;
       
       $credit_type_id = $request->credit_type;
     $credit_name =  DB::table('credit_type')->where('id','=',$credit_type_id)->first()->name;
       
        credit_account::create(['user_id'=>$request->user_id,'credit_type_id'=>$request->credit_type,'amount'=>$request->amount,'note'=>$request->note,'month'=>$month,'date'=>$date,'year'=>$year]);
        $this->push_notification('Credit','Credited by: '.$name."\n".'amount: '.$request->amount."\n".'note: '.$credit_name);
        return response()->json(['status_code'=>200]);
    }
    
    public function send_token(Request $request)
    {
        //file_put_contents('token.txt',$request);
        $user_id = $request->user_id;
        $token = $request->token;
        if(token::where('user_id','=',$user_id)->first())
        {
            token::where('user_id','=',$user_id)->update(['token'=>$token]);
        }
        else
        {
           token::create(['user_id'=>$user_id,'token'=>$token]); 
        }
        return response()->json(['status_code'=>200]);
    }
    function date_compare($a, $b)
{
    $t1 = strtotime($a['created_at']);
    $t2 = strtotime($b['created_at']);
    return $t2 - $t1;
} 
    public function edit(Request $request){
       
       $user_id = $request->user_id;
       $name = user::where('id','=',$user_id)->first()->name;
        $id = $request->id;
        $type = $request->type;
        $amount = $request->amount;
        $note = $request->note;
        $date = $request->date;
        
       // file_put_contents("test.txt",$request ."\n".$id." ".$type." ".$amount." ".$note." ".$date);
        //$debit_type_id = $request->debit_id;
        if($type ==='debit')
        {
         debit_account::where('id','=',$id)->update(['amount'=>$amount,'note'=>$note,'date'=>$date]);
         $this->push_notification('Debit Edit','Edited by: '.$name."\n".'amount: '.$amount."\n".'note: '.$note);
         
        }
        else
        {
           credit_account::where('id','=',$id)->update(['amount'=>$amount,'note'=>$note,'date'=>$date]);
           $this->push_notification('Credit Edit','Edite by: '.$name."\n".'amount: '.$amount."\n".'note: '.$note);
        }
          return response()->json(['status_code'=>200]);
    }
    
    public function delete(Request $request)
    {
          $id = $request->id;
        $type = $request->type;
        if($type ==='debit')
        {
          debit_account::where('id','=',$id)->delete();
        }
        else
        {
            credit_account::where('id','=',$id)->delete();
        }
          return response()->json(['status_code'=>200]);
    }
    
    public function get_history(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $debit = debit_account::where('month','=',$month)->where('year','=',$year)->get();
        $total_debit =debit_account::where('month','=',$month)->where('year','=',$year)->get()->sum('amount'); 
        $credit = credit_account::where('month','=',$month)->where('year','=',$year)->get();
        $total_credit =credit_account::where('month','=',$month)->where('year','=',$year)->get()->sum('amount');
        $response = array();
        for($i=0;$i<sizeof($credit);$i++)
        {  
            $utilites = DB::table('credit_type')->where('id','=',$credit[$i]->credit_type_id)->first()->name;
            array_push($response,['header'=>$utilites,'id'=>$credit[$i]->id,'amount'=>$credit[$i]->amount,'note'=>$credit[$i]->note,'date'=>$credit[$i]->date,'created_at'=>$credit[$i]->created_at,'type'=>'credit']);
        }
        
          for($i=0;$i<sizeof($debit);$i++)
        {  
            $utilites = DB::table('debit_type')->where('id','=',$debit[$i]->debit_type_id)->first()->name;
            if($utilites === 'salary' ||  $utilites === 'individual' )
             {
                 $name = user::where('id','=',$debit[$i]->debited_to_id)->first()->name;
                  array_push($response,['header'=>$utilites,'id'=>$debit[$i]->id,'amount'=>$debit[$i]->amount,'note'=>$name,'date'=>$debit[$i]->date,'created_at'=>$debit[$i]->created_at,'type'=>'debit']);
             }
             else
             {
                  array_push($response,['header'=>$utilites,'id'=>$debit[$i]->id,'amount'=>$debit[$i]->amount,'note'=>$debit[$i]->note,'date'=>$debit[$i]->date,'created_at'=>$debit[$i]->created_at,'type'=>'debit']);
             }
           
        }
        if(sizeof($response)>0)
        {
        usort($response,array($this,'date_compare'));
        return response()->json(['status_code'=>'200','list'=>$response,'total_credit'=>$total_credit,'total_debit'=>$total_debit,'revenue'=>$total_credit-$total_debit]);
        }
        else
        {
            return response()->json(['status_code'=>400]);
        }
        
    }
    
    public function get_profile(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $user_id = $request->user_id;
        $debit = debit_account::where('month','=',$month)->where('year','=',$year)->where('debited_to_id','=',$user_id)->get();
        $total_debit =debit_account::where('month','=',$month)->where('year','=',$year)->where('debited_to_id','=',$user_id)->get()->sum('amount'); 
       
        $response = array();
       
        
          for($i=0;$i<sizeof($debit);$i++)
        {  
            $utilites = DB::table('debit_type')->where('id','=',$debit[$i]->debit_type_id)->first()->name;
            array_push($response,['header'=>$utilites,'amount'=>$debit[$i]->amount,'note'=>$debit[$i]->note,'date'=>$debit[$i]->date,'created_at'=>$debit[$i]->created_at,'type'=>'debit']);
        }
        
         if(sizeof($response)>0)
        {
        usort($response,array($this,'date_compare'));
        return response()->json(['status_code'=>'200','list'=>$response,'total_debit'=>$total_debit]);
        }
        else
        {
            return response()->json(['status_code'=>400]);
        }
       // usort($response,array($this,'date_compare'));
        //return response()->json(['list'=>$response,'total_credit'=>$total_credit,'total_debit'=>$total_debit,'revenue'=>$total_credit-$total_debit]);
        
        
        
        
        
    }
    
    public function get_debit_list(Request $request)
    {
        $debit_type = DB::table('debit_type')->get();
        $user = User::get();
        return response()->json(['debit_type'=>$debit_type,'debited_to'=>$user]);
    }
    
    public function get_remaining_fund(Request $request)
    {
        $total_debit = debit_account::get()->sum('amount'); 
        $total_credit = credit_account::get()->sum('amount'); 
        return response()->json(['remaining_fund'=>$total_credit-$total_debit]);
    }
    
    
    
}
