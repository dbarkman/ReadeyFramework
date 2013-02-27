<?php

/**
 * SendEmail.class.php
 * Description:
 *
 */

class SendEmail
{
	public static function sendNewWordLogNotification($message = null)
	{
		self::send('6023191805@vtext.com', 'New Word Log', $message);
		self::send('david.barkman13@gmail.com', 'New Word Log', $message);
	}

	public static function sendNewUserNotification($message = null)
	{
		self::send('6023191805@vtext.com', 'New User', $message);
		self::send('david.barkman13@gmail.com', 'New User', $message);
	}

	private static function send($address, $subject, $message)
	{
		return mail($address, $subject, $message);
	}
}