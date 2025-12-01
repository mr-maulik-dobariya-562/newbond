<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function index(Request $request)
    {
        $xmlContent = array();
        $xmlInput = $request->getContent();

        if (empty($xmlInput)) {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "No input data";
            return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
        }

        $doc = new \DOMDocument();
        $doc->loadXML($xmlInput);
        $elements = $doc->getElementsByTagName("value");
        $xmlContent["code"] = 0;

        if ($elements->length > 0) {
            $userName = $doc->getElementsByTagName("UserID")->item(0)->nodeValue;

            $user = DB::table('customer')
                ->where('mobile', $userName)
                ->orWhere('email', $userName)
                ->first();

            if ($user) {
                $newPassword = $this->randomString(8);

                DB::table('customer')
                    ->where('mobile', $userName)
                    ->orWhere('email', $userName)
                    ->update(['password' => $newPassword]);

                $email = $user->email;
                $mobileNo = $user->mobile;

                $message = file_get_contents(resource_path('views/emails/forgetpassword.blade.php'));
                $message = str_replace("%password%", $newPassword, $message);
                $subject = "Spectacase Password";

                try {
                    Mail::html($message, function ($mail) use ($email, $subject) {
                        $mail->from(env('MAIL_FROM_ADDRESS'), 'SpectaCase');
                        $mail->to($email);
                        $mail->subject($subject);
                    });
                    $xmlContent["code"] = 1;
                    $xmlContent["Message"] = 'Email sent successfully';
                } catch (Exception $e) {
                    $xmlContent["code"] = 1;
                    $xmlContent["Message"] = "Unable to send email. Please try again. " . $e->getMessage();
                }

                // $smsMessage = MESSAGE_FORGOT_PASSWORD_1 . $newPassword . MESSAGE_FORGOT_PASSWORD_2;
                // $this->sendSMS($mobileNo, $smsMessage);

                // $smsMessage = MESSAGE_FORGOT_PASSWORD . $newPassword;
                // if (strlen($mobileNo) == 10) {
                //     $mobileNo = "91" . $mobileNo;
                // }
                // $this->sendWhatsappMessage($mobileNo, $smsMessage);

            } else {
                $xmlContent["code"] = 2;
                $xmlContent["Message"] = "Invalid Email ID or Mobile Number.";
            }
        } else {
            $xmlContent["code"] = 0;
            $xmlContent["Message"] = "Invalid request source";
        }

        return response()->json($xmlContent, JSON_UNESCAPED_UNICODE);
    }

    private function randomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
