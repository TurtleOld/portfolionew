<?
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
  mail($to, $subject, $message, $headers); //Отправка письма с помощью функции mail
?>