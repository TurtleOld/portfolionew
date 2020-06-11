<?
    if( ini_get('allow_url_fopen') ) {
        die('allow_url_fopen is enabled. file_get_contents should work well');
    } else {
        die('allow_url_fopen is disabled. file_get_contents would not work');
    }
    $spam = $_POST["surname"];
    $message = $_POST['text'];
    $today = date('j.n.Y H:m');
    $url_google_api = 'https://www.google.com/recaptcha/api/siteverify';
    include('secret.php');
    $query = $url_google_api.'?secret='.$secret.'&response='.$_POST['token'].'&remoteip='.$_SERVER['REMOTE_ADDR'];
    $data = json_decode(file_get_contents($query), true); // записываем полученные данные в виде ассоциативного массива
    $score = $data['score']; // оценка Google recaptcha v3, от 0.1 до 0.9, где 0.9 означает "точно не спам"

    $to = "shurpo.alexander@gmail.com";
    $subject = 'Письмо с сайта'; //Заголовок сообщения
    $message = '
            <html>
                <head>
                    <title>'.$subject.'</title>
                </head>
                <body>
                    <p>Имя: '.$_POST['name'].'</p>
                    <p>Email: '.$_POST['email'].'</p>
                    <p>Сообщение: '.$_POST['text'].'</p>                                   
                </body>
            </html>'; //Текст сообщения
    $headers  = "Content-type: text/html; charset=utf-8 \r\n"; //Кодировка письма
    $headers .= "From: Письмо с сайта <from@example.com>\r\n"; //Наименование и почта отправителя
    
    if (empty($spam)){
        $logText = strip_tags($message); 
        if($data['success']) { // если ответ от сервиса Google был получен в нём есть input-response
            if($score >= 0.5) { // если оценка на spam больше чем 0.5 (Вы можете менять это на своё усмотрение)
                $logFile = "mail.log"; chmod($logFile, 0600); // формируем лог-файл с правами 0600
                file_put_contents($logFile, "\n{$today}\n{$logText}\n", FILE_APPEND); // записываем информацию в лог-файл
                mail($to, $subject, $message, $headers); // отправляем письмо
            }else { // если оценка на spam меньше чем 0.5
                $spamLog = "spam.log"; chmod($spamLog, 0600); // формируем лог-файл для спама, с правами 0600
                // это нужно, чтобы исключить ошибки отсева и отследить корректность нашей оценки (0.5 или выше)
                file_put_contents($spamLog, "\n{$today}\n{$logText}Запрос: {$query}\nScore: {$score}\n", FILE_APPEND);
                $message .= "<b>Это письмо попало в спам</b>"; 
                mail("shurpo.alexander@gmail.com", $subject, $message, $headers);
                exit();
            }
        }
    }else{
        exit();
    }
  
?>