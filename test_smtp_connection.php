<?php
// test_smtp_connection.php
// Quick test untuk koneksi SMTP

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(15); // 15 detik timeout

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

echo "Testing SMTP Connection...\n";
echo "Host: " . EMAIL_HOST . ":" . EMAIL_PORT . "\n";
echo "Encryption: " . EMAIL_ENCRYPTION . "\n";
echo "Username: " . EMAIL_USERNAME . "\n\n";

try {
    echo "1. Creating transport...\n";
    
    $password = str_replace(' ', '', EMAIL_PASSWORD);
    
    $transport = (new Swift_SmtpTransport(EMAIL_HOST, EMAIL_PORT, EMAIL_ENCRYPTION))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword($password)
        ->setTimeout(10) // 10 second timeout
        ->setStreamOptions([
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);
    
    echo "2. Creating mailer...\n";
    $mailer = new Swift_Mailer($transport);
    
    echo "3. Testing connection...\n";
    $transport->start();
    
    echo "\n✅ CONNECTION SUCCESS!\n";
    echo "SMTP server is reachable and credentials are valid.\n\n";
    
    // Test send simple email
    echo "4. Sending test email to: luthfinurafiq@gmail.com\n";
    
    $message = (new Swift_Message('Test Email - Sistem Presensi'))
        ->setFrom([EMAIL_FROM => EMAIL_FROM_NAME])
        ->setTo(['luthfinurafiq@gmail.com'])
        ->setBody('This is a test email from Sistem Presensi SMK. If you receive this, email service is working!', 'text/html');
    
    $result = $mailer->send($message);
    
    if ($result) {
        echo "\n✅ EMAIL SENT SUCCESSFULLY!\n";
        echo "Check inbox/spam at luthfinurafiq@gmail.com\n";
    } else {
        echo "\n❌ Email send failed\n";
    }
    
} catch (Swift_TransportException $e) {
    echo "\n❌ SMTP TRANSPORT ERROR:\n";
    echo $e->getMessage() . "\n\n";
    
    echo "POSSIBLE CAUSES:\n";
    echo "1. Gmail App Password is wrong or expired\n";
    echo "2. Network/firewall blocking port 465\n";
    echo "3. Gmail security settings blocking access\n";
    echo "4. 2-Step Verification not enabled\n\n";
    
    echo "SOLUTIONS:\n";
    echo "1. Generate new App Password: https://myaccount.google.com/apppasswords\n";
    echo "2. Make sure 2-Step Verification is ON\n";
    echo "3. Check firewall/antivirus settings\n";
    echo "4. Try port 587 with TLS instead of 465 with SSL\n";
    
} catch (Exception $e) {
    echo "\n❌ GENERAL ERROR:\n";
    echo $e->getMessage() . "\n";
}
?>
