<?php
namespace App\Http\Controllers\auto\sender;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use DB;
use App\email_senders as tblEmailSenders;

class email extends Controller
{
    //
    public function main()
    {
        $getdata = tblEmailSenders::where([
            'id'    =>  10001
        ])->first();

        $sender_host = $getdata->host;
        $sender_port = $getdata->port;
        $sender_user = $getdata->user;
        $sender_email = $getdata->email;
        $sender_password = $getdata->password;
        $sender_label = $getdata->label;
        $sender_tls = $getdata->tls;

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $sender_tls;
        $mail->Host = $sender_host;
        $mail->Port = $sender_port;
        $mail->Username = $sender_user;
        $mail->Password = $sender_password;

        //form
        $mail->setFrom($sender_email, $sender_label);        
        $mail->Subject = $subject;
        $mail->MsgHTML($content);
        $mail->addAddress($email, $name); 
        $mail->send();

        //check
        if($mail )
        {
            $data = [
                'message'       =>  ''
            ];  
            
        }
        else
        {
            $data = [
                'message'       =>  "Mailer Error: " . $mail->ErrorInfo
            ];
        }

        return $data;

    }

    public function testing()
    {
        $sender_host = 'smtp.gmail.com';
        $sender_port = '465';
        $sender_user = 'simpeldik@edu.jakarta.go.id';
        $sender_email = 'simpeldik@edu.jakarta.go.id';
        $sender_password = '50bhfuev';
        $sender_label = 'Sender Label';

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = $sender_host;
        $mail->Port = $sender_port;
        $mail->Username = $sender_user;
        $mail->Password = $sender_password;

        //form
        $mail->setFrom($sender_email, $sender_label);        
        $mail->Subject = $subject;
        $mail->MsgHTML($content);
        $mail->addAddress($email, $name); 
        $mail->send();

        dd($mail);
    }
}