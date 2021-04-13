<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\User;
use App\Verification;
use App\Mail\MailtrapEmail;
use App\Mail\VerificationMail;
use App\Mail\InviteMail;
use App\Mail\PinCode;
use Illuminate\Support\Facades\Mail;
use DB;
class UsersController extends Controller
{
    

     public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->accessToken;
           //After successfull authentication, notice how I return json parameters
            return response()->json([
              'success' => true,
              'token' => $success,
              'user' => $user
          ]);
        } else {
       //if authentication is unsuccessfull, notice how I return json parameters
          return response()->json([
            'success' => false,
            'message' => 'Invalid Email or Password',
        ], 401);
        }
    }

      /**
     * Register api.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required',
            // 'email' => 'required|unique:users|regex:/(0)[0-9]{10}/',
            'email' => 'required|email|unique:users',
        ]);
        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['user_role'] = 'user';
        $user = User::create($input);
        $success['token'] = $user->createToken('appToken')->accessToken;
        return response()->json([
          'success' => true,
          'token' => $success,
          'user' => $user
      ]);
    }

     public function logout(Request $res)
    {
      if (Auth::user()) {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
          'success' => true,
          'message' => 'Logout successfully'
      ]);
      }else {
        return response()->json([
          'success' => false,
          'message' => 'Unable to Logout'
        ]);
      }
     }

     public function sendInvitetoUser(Request $request){
     	if(!empty($request->email)){
		    Mail::to($request->email)->send(new InviteMail()); 
		    return 'A message has been sent to '.$request->email.'!';
     	}
     }

     public function inviteResponce(Request $request){

     	 $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
        ]);
        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 401);
        }
        $input = $request->all();
        $pin = rand(100000,999999);
         $input['password'] = bcrypt($input['password']);
         $input['user_role'] = 'user';
         $input['status'] = 0;
         $user = User::create($input);
         // $exist = Verification::where('user_id', $user->id)->get();
         $stat = Verification::create([
         			'pin' => $pin,
         			'status' => 0,
         			'user_id' => $user->id
         		]);

         $data = ['pin' => $stat->pin, 'name' => $user->name];
          	 Mail::to($request->email)->send(new PinCode($pin)); 
        	
		
		return 'Email has been send to this email with 6 digit pin code to verify your this . '.$request->email.'!';      

     	// return view('users.complete_registration');
     }


     public function profileComplete(Request $request){

			 $validator = Validator::make($request->all(), [
	            'name' => 'required',
	            'user_name' => 'required',
	        		]);
			  if ($validator->fails()) {
		          return response()->json([
		            'success' => false,
		            'message' => $validator->errors(),
		          ], 401);
		        }
		        $input = $request->all();
		        $e = $input['email'];
		        $n= $input['name'];
		        $u_n=$input['user_name'];




                //make sure yo have image folder inside your public
		        $file = $request->file('avatar');
                $destination_path = 'image/';
                $profileImage = date("Ymd").".".$file->getClientOriginalName();
                $file->move($destination_path, $profileImage);
                //save the link of thumbnail into myslq database        
                $avatar = $destination_path . $profileImage;

		      $exist = DB::table('users')->where('email', $e)->update([
		      			'name' => $n, 
		      			'user_name' =>$u_n,
		      			'avatar' =>$avatar
		      ]);

 return response()->json(['success' => true,
				            'message' => 'Your profile is finally completed. Congratulation!',
				          ], 401);

     }	

     public function verifyPinCode(Request $request){
     	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pin' => 'required'

        ]);
     	 if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 401);
        }
        	$pin = $request->pin;
     		$email = $request->email;
        $user = User::where('email', $email)->first();
     	
        $code = Verification::where('user_id', $user['id'])->first();

        if ($code['pin']  == $pin){

        		$update  = DB::table('users')
	            ->where('email', $email)
	            ->update(['status' => 1]);

	        	 Mail::to($email)->send(new VerificationMail()); 
        	return "Pin code Matched Please check email for next step.";


        }else{
        	return "pin code is not matched. ";
        }
     }
}
