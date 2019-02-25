<?php
/*
Apparantly you can't login or anything using this, it is for creating facebook apps so I don't think it can help us with logging in and viewing friends. I will leave the facebook class in the /Classes directory just incase somebody finds a use. Documentation is here: http://developers.facebook.com/
*/
$email = urlencode('facebook@vectra-bot.net');
$pass = '';
$id = '';
$secret = '';
$facebook = new Facebook(array(
  'appId' => $id,
  'secret' => $secret,
  'cookie' => true,
)); 

?>