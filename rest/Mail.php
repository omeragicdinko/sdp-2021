<?php
class Mail
{
	public static function send_reservation_info($reservation_code, $car_id, $manager_email)
	{
		// Create the Transport
		$transport = (new Swift_SmtpTransport('smtp.elasticemail.com', 2525))
			->setUsername('dinko.omeragic@stu.ibu.edu.ba')
			->setPassword('73643BBE1F9BAD2E3EE2682FF5D9E685F10E');

		// Create the Mailer using your created Transport
		$mailer = new Swift_Mailer($transport);

		// Create a message
		$message = (new Swift_Message('New car reserved :D'))
			->setFrom([$manager_email => 'Manager'])
			->setTo([$manager_email => 'Manager'])
			->setBody('Dear Manager, <br><br> Customer with reservation code: <b>' . $reservation_code . '</b> will pick up the car with id: <b>' . $car_id . '</b> <br><br> Kind regards,', 'text/html');

		// Send the message
		$result = $mailer->send($message);
	}
}