<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use Mail;
// use Yajra\Datatables\Datatables;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => 'friend_request_action']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    public function get_user_table(){
        $users = User::join('user_friend_mapping', 'users.id', '=', 'user_friend_mapping.friend_id','left')
            ->select(['users.id', 'users.name', 'users.email', 'users.password', 'users.created_at', 'users.updated_at','friend_status','user_id'])->distinct('users.id'); 
        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                $logged_user_id = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
                $request_status = '';
                if($logged_user_id!=$user->id ){
                if($user->friend_status==1){
                    $request_status = '<a href="javascript:void(0)" class="btn btn-xs btn-default">Friends</a>';
                }else if($user->friend_status==2){
                    $request_status = '<a href="javascript:void(0)" class="btn btn-xs btn-default">Request sent</a>';
                }else if($user->friend_status==4){
                    $request_status =  '<a href="javascript:void(0)" class="btn btn-xs btn-default">Rejected</a>';
                }else if($user->friend_status==3){
                    $request_status = '';
                }else{
                    if($logged_user_id!=$user->user_id ){
                        $request_status = '<a href="javascript:void(0)"  id="friendadd_'.$user->id.'" data-id="'.$user->id.'" class="btn btn-xs btn-primary add_friend">Add Friend</a>';
                    }
                }
            }else{

                        $request_status = '';
                }
                return '<a href="profile/'.$user->id.'"  id="friend_'.$user->id.'" data-id="'.$user->id.'" class="btn btn-xs btn-info view_friend">View Friend</a>'.$request_status;
            })
            ->removeColumn('id','password','updated_at','friend_status','user_id')
            ->make(true);

    }
    public function friend_requests(){
        return view('friend_request');
    }
    public function get_friend_requests_table(){
        $logged_user_id = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $friend_request = User::join('user_friend_mapping', 'users.id', '=', 'user_friend_mapping.user_id','inner')
            ->select(['users.id', 'users.name', 'users.email','user_friend_mapping.id as request_id','user_friend_mapping.friend_status'])->where([['friend_status', 2],['friend_id',$logged_user_id]])->distinct('users.id'); 
        return Datatables::of($friend_request)
            ->addColumn('action', function ($friend_request) {
                return '<a href="javascript:void(0)"  id="friend_'.$friend_request->request_id.'" data-id="'.$friend_request->request_id.'" data-action="1" class="btn btn-xs btn-success accept_button friend_action friend_'.$friend_request->request_id.'">Accept</a><a href="javascript:void(0)"  id="friend_'.$friend_request->request_id.'" data-id="'.$friend_request->request_id.'" data-action="4" class="btn btn-xs btn-danger friend_action friend_'.$friend_request->request_id.'">Reject</a>';
            })
            ->removeColumn('id','friend_status','request_id')
            ->make(true);

    }
    public function add_friend(Request $request){
         $friend_id = $request->get('friend_id');
         $logged_user_id = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
         if((int)$friend_id > 0 && $logged_user_id > 0){
            $friend_details = User::find($friend_id);
            if ($friend_details->id > 0) {
                $request_token = $this->get_random_token(20);
                $logged_in_user = User::find($logged_user_id);
               $insert_id = DB::table('user_friend_mapping')->insertGetId(
                    ['user_id' => $logged_user_id, 'friend_id' => $friend_id,'request_token'=>$request_token]
                );
                   $data = ['reciever_name'=>$friend_details->name,'reciever_mail'=>$friend_details->email,'sender_name' => $logged_in_user->name,'token'=>urlencode($request_token)];
                    Mail::send('emails.friend', ['template_arg'=>$data], function ($m) use ($data) {
                        $m->from('fbappint@gmail.com', 'Fbapp Application');

                        $m->to($data['reciever_mail'], $data['reciever_name'])->subject('Friend Request');
                    });
                return $insert_id > 0 ? response()->json(["status"=>"Success","msg"=>"Friend request sent successfully","results"=>[]],200) :response()->json(["status"=>"Failure","msg"=>"Some error occured","results"=>[]],200);
            }
         }else{
                return response()->json(["status"=>"Failure","msg"=>"Some error occured","results"=>[]],200);
         }
    }
    public function get_random_token($length) 
    {
        $validCharacters = "ABCDEFGHIJKLMNPQRSTUXYVWZ123456789". strtotime('now');
        $validCharNumber = strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $length; $i++)
        {
            $index = mt_rand(0, $validCharNumber - 1);
            $result .= $validCharacters[$index];
        }
        return password_hash($result, PASSWORD_DEFAULT);
    } 
    public function friend_request_action_mail(Request $request){
        $status = '';
        $input_array = $request->input();
        $token = urldecode($input_array['token']);
        $token_exist = DB::table('user_friend_mapping')->where('request_token', $token)->first();

        if(!empty($token_exist) && in_array($input_array['action'], array(1,4))  && $token!=''){
            DB::table('user_friend_mapping')
            ->where('request_token', $token)
            ->update(['friend_status' => $input_array['action'],'request_token'=>'']);
            if($input_array['action']==1){
                $status = 'accepted';
            }else{
                $status = 'rejected';
            }
            echo $status;
        }else{
          echo  $status = 'not a valid request';
        }
    }
    public function friend_request_action(Request $request){
        $msg = '';
         $request_id = $request->get('request_id');
         $request_action = $request->get('request_action');
         $logged_user_id = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
         if((int)$request_id > 0 && $logged_user_id > 0 && in_array($request_action,array(1,4))){
             $request_exist = DB::table('user_friend_mapping')->where([['id', $request_id],['friend_id',$logged_user_id]])->first();
             if(!empty($request_exist)){
                 DB::table('user_friend_mapping')
                ->where([['id', $request_id],['friend_id',$logged_user_id]])
                ->update(['friend_status' => $request_action,'request_token'=>'']);

                $msg = $request_action ==1?"Friend request accepted":"Friend request rejected";
                return response()->json(["status"=>"Success","msg"=>$msg,"results"=>[]],200); 
             }
         }else{
               $msg = "Some error occured";
         }
         return response()->json(["status"=>"Failure","msg"=>$msg,"results"=>[]],200);
    }
    public function profile($id){
        $data = array();
        $user_id = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $data['profile_details'] = json_decode(User::find((int)$id));
        $mutual_friend_array = array();
        if(!empty($data['profile_details'])){

            $user_friends = DB::select('SELECT GROUP_CONCAT(if(uf1.`user_id` = '.$id.', uf1.`friend_id`,uf1.`user_id`)) AS user_friend FROM `user_friend_mapping` uf1 WHERE uf1.user_id = '.$id.' OR uf1.friend_id = '.$id.' limit 1');
            // print_r();exit;
            if(!empty($user_friends)&& $user_friends[0]->user_friend !=''){
                 $my_friends = DB::select('SELECT GROUP_CONCAT(DISTINCT if(uf1.`user_id` = '.$user_id.', uf1.`friend_id`,uf1.`user_id`)) AS my_friend FROM `user_friend_mapping` uf1 WHERE uf1.user_id = '.$user_id.' OR uf1.friend_id = '.$user_id.' limit 1');
                  if(!empty($my_friends)&& $my_friends[0]->my_friend !=''){
                     $mutual_friend_array = array_unique(array_merge(array_intersect(explode(',',$user_friends[0]->user_friend),explode(',',$my_friends[0]->my_friend))));
                  }
            }
            $data['mutual_friends'] = json_decode(User::findMany($mutual_friend_array,['id','name']));
            return view('profile',$data);
        }
        else{
            return view('404');
        }
    }
}
