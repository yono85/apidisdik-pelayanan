<?php
namespace App\Http\Controllers\email;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\email_senders as tblEmailSenders;
use App\Http\Controllers\config\index as Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\email_sender_tables as tblEmailSenderTables;

class send extends Controller
{
    //
    public function main($request)
    {
        $Config = new Config;

        $getData = $request;
        $to = json_decode($getData->to);
        $temp = json_decode($getData->template);

        //
        $getsender = tblEmailSenders::where([
            'id'        =>  $getData->sender_id
        ])->first();


        $sender_host = $getsender->host; //'smtp.gmail.com';
        $sender_port = $getsender->port; //'465';
        $sender_user = $getsender->user; //'simpeldik@edu.jakarta.go.id';
        $sender_email = $getsender->email; //'simpeldik@edu.jakarta.go.id';
        $sender_password = $getsender->password; //'50bhfuev';
        $sender_label = $getsender->label; //'Sender Label';
        $sender_secure = $getsender->tls;

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $sender_secure;
        $mail->Host = $sender_host;
        $mail->Port = $sender_port;
        $mail->Username = $sender_user;
        $mail->Password = $sender_password;



        //form
        $mail->setFrom($sender_email, $temp->subject);        
        $mail->Subject = $temp->subject;
        $mail->MsgHTML($getData->body);
        $mail->addAddress($to->email, $to->name); 
        $mail->send();

        // SUCCESS
        if($mail)
        {
            
            $update = tblEmailSenderTables::where([
                'id'        =>  $getData->id,
                'status'    =>  1
            ])->update([
                'sending'       =>  1,
                'sending_info' =>  json_encode(['date'=>date('Y-m-d H:i:s', time()),'message'=>'Terkirim']),
                'status'        =>  0
            ]);

            $data = [
                'message'        =>  ''
            ];
            return $data;
        }


        //if error
        $update = tblEmailSenderTables::where([
            'id'        =>  $getData->id,
            'status'    =>  1
        ])->update([
            'sending'       =>  2,
            'sending_info' =>  json_encode(['date'=>date('Y-m-d H:i:s', time()),'message'=>'Error: ' . $mail->ErrorInfo]),
            'status'        =>  0
        ]);

        $data = [
            'message'       =>  'Error sending email: ' . $mail->ErrorInfo
        ];
        return $data;

    }
}