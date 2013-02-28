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

	public static function sendNewSupportTicketNotification($message = null)
	{
		self::send('6023191805@vtext.com', 'New Support Ticket', $message);
		self::send('david.barkman13@gmail.com', 'New Support Ticket', $message);
	}

	public static function sendNewFeedbackNotification($message = null)
	{
		self::send('6023191805@vtext.com', 'New Feedback', $message);
		self::send('david.barkman13@gmail.com', 'New Feedback', $message);
	}

	private static function send($address, $subject, $message)
	{
		return mail($address, $subject, $message);
	}
}