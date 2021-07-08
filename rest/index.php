<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS, PATCH');

require 'Auth.php';
require 'Mail.php';

require_once('../vendor/autoload.php');
require_once('config.php');
require_once('dao/UserDao.class.php');
require_once('dao/CarDao.class.php');
require_once('dao/CommentDao.class.php');
require_once('dao/RentBaseDao.class.php');
require_once('dao/ReservationDao.class.php');
require_once('otp.php');

Flight::register('user_dao', 'UserDao');
Flight::register('car_dao', 'CarDao');
Flight::register('comment_dao', 'CommentDao');
Flight::register('base_dao', 'RentBaseDao');
Flight::register('reservation_dao', 'ReservationDao');

Flight::route('GET /users', function()
{
  $data = getallheaders();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $users = Flight::user_dao()->get_all();
  Flight::json($users);
});

Flight::route('GET /reservations', function()
{
  $data = getallheaders();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $reservations = Flight::reservation_dao()->get_all();
  Flight::json($reservations);
});

Flight::route('GET /bases', function()
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $bases = Flight::base_dao()->get_all();
  Flight::json($bases);
});

Flight::route('POST /base', function()
{
  $base = Flight::request()->data->getData();
  Flight::base_dao()->add($base);
});

Flight::route('GET /user/@id', function($id)
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $users = Flight::user_dao()->get_user_by_id($id);
  Flight::json($users);
});

Flight::route('GET /user-info/@id', function($id)
{
  $users = Flight::user_dao()->get_user_by_id($id);
  Flight::json($users);
});

Flight::route('GET /base/@id', function($id)
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $bases = Flight::base_dao()->get_by_id($id);
  Flight::json($bases);
});

Flight::route('GET /reservation/@id', function($id)
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $reservation = Flight::reservation_dao()->get_by_id($id);
  Flight::json($reservation);
});

Flight::route('GET /car/@id', function($id)
{
  $cars = Flight::car_dao()->get_car_by_id($id);
  Flight::json($cars);
});

Flight::route('POST /user', function()
{
  $user = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_email($user['email']);
  if ($db_user) {
    Flight::halt(404, 'User with the same email already exists');
  } else {
    if (filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
      if (is_password_valid($user['password'])) {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        Flight::user_dao()->add($user);
      } else {
        Flight::halt(404, 'Password is not safe enough. Please change it.');
      }
    } else {
      Flight::halt(404, 'Email is not valid');
    }
  }
});

Flight::route('POST /update-password', function()
{
  $user = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_id($user['id']);
  if ($db_user) {
    if (password_verify($user['current_password'], $db_user['password'])) {
      $user['new_password'] = password_hash($user['new_password'], PASSWORD_DEFAULT);
      Flight::user_dao()->update_password($user['id'], $user['new_password']);
    } else {
      Flight::halt(404, 'Entered current password is not correct.');
    }
  } else {
    Flight::halt(404, 'There is a problem connection to the database');
  }
});

Flight::route('POST /reservation', function()
{
  $reservation = Flight::request()->data->getData();
  Flight::reservation_dao()->add($reservation);
});

Flight::route('POST /reservationCode', function()
{
  $reservation_data = Flight::request()->data->getData();
  Mail::send_reservation_info($reservation_data['reservation_code'] ,$reservation_data['car_id'] , 'dinko.omeragic@stu.ibu.edu.ba');
});

Flight::route('POST /car', function()
{
  $car = Flight::request()->data->getData();
  Flight::car_dao()->add($car);
});

Flight::route('POST /login', function()
{
  $user = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_email($user['email']);

  if ($db_user) {
    if (password_verify($user['password'], $db_user['password'])) {
      $token_data = [
        'id' => $db_user['id'],
        'email' => $db_user['email'],
        'name' => $db_user['name'],
        'surname' => $db_user['surname'],
        'phone_number' => $db_user['phone_number'],
        'admin' => $db_user['admin'],
        'otp' => $db_user['otp_setup']
      ];
      $jwt = Auth::encode_jwt($token_data);
      Flight::json(['user_token' => $jwt]);
    } else {
      Flight::halt(404, 'Password is not correct');
    }
  } else {
    Flight::halt(404, 'User not found');
  }
});

Flight::route('GET /cars', function()
{
  $cars = Flight::car_dao()->get_car_info();
  Flight::json($cars);
});

Flight::route('POST /car/availability/no/@id', function($id)
{
  Flight::car_dao()->update_availability_to_NO($id);
});

Flight::route('POST /car/availability/yes/@id', function($id)
{
  Flight::car_dao()->update_availability_to_YES($id);
});

Flight::route('POST /bases', function()
{
  $request = Flight::request()->data->getData();
  $id = $request['id'];
  Flight::base_dao()->update_base($request, $id);
  Flight::json('Updated');
});

Flight::route('POST /cars', function()
{
  $request = Flight::request()->data->getData();
  $id = $request['id'];

  Flight::car_dao()->update_car($request, $id);
  Flight::json('Updated');
});

Flight::route('POST /users', function()
{
  $request = Flight::request()->data->getData();
  $id = $request['id'];
  Flight::user_dao()->update_user($request, $id);
  Flight::json('Updated');
});

Flight::route('POST /edit-user-profile', function()
{
  $user = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_id($user['id']);
  if ($db_user) {
    if (password_verify($user['password'], $db_user['password'])) {
      $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
      Flight::user_dao()->update_user($user, $user['id']);
      Flight::json('Updated');
    } else {
      Flight::halt(404, 'Password does not match');
    }
  } else {
    Flight::halt(404, 'Error connecting to the database');
  }
});

Flight::route('POST /reservation/update', function()
{
  $request = Flight::request()->data->getData();
  $id = $request['id'];
  Flight::reservation_dao()->update_status($request, $id);
  Flight::json('Updated');
});

Flight::route('GET /comments', function()
{
  $comments = Flight::comment_dao()->get_comments();
  Flight::json($comments);
});

Flight::route('POST /comment', function()
{
  $comment = Flight::request()->data->getData();
  Flight::comment_dao()->add($comment);
});

Flight::route('GET /workers', function()
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if ($user_data['data']['admin'] <= 0) {
    Flight::halt(403, 'It is allowed only for admin users');
  }
  $workers = Flight::user_dao()->get_workers();
  Flight::json($workers);
});

Flight::route('DELETE /user/@id', function($id)
{
  Flight::user_dao()->delete_user($id);
});

Flight::route('DELETE /base/@id', function($id)
{
  Flight::base_dao()->delete_base($id);
});

Flight::route('DELETE /car/@id', function($id)
{
  Flight::car_dao()->delete_car($id);
});

Flight::route('DELETE /reservation/@id', function($id)
{
  Flight::reservation_dao()->delete_reservation($id);
});

Flight::route('GET /non-workers', function()
{
  $data = apache_request_headers();
  $user_data = Auth::decode_jwt($data);
  if (!($user_data['data']['admin']>0)) {
      Flight::halt(403, 'It is allowed only for admin users');
    }
  $nonWorkers = Flight::user_dao()->get_non_workers();
  Flight::json($nonWorkers);
});

Flight::route('POST /non-worker/'+id, function($id)
{
  Flight::user_dao()->delete_non_worker($id);
});

Flight::route('POST /generateQR', function()
{
  $data = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_email($data['email']);
  $otp_secret = generate_otp_secret();

  Flight::user_dao()->update_OTP_secret($data['email'], $otp_secret);
  Flight::user_dao()->update_OTP_enabled_to_0($data['email']);
  echo generate_qrcode($otp_secret);
});

Flight::route('POST /validateOTPCode', function()
{
  $data = Flight::request()->data->getData();
  $db_user = Flight::user_dao()->get_user_by_email($data['email']);

  if (is_otp_code_valid($db_user['otp_secret'], $data['code'])) {
    Flight::user_dao()->update_OTP_enabled_to_1($data['email']);
  } else {
    Flight::halt(404, 'Invalid code');
  }
});

//Utilities

//Check if the provided password is safe enough to use
function is_password_valid($password)
{
  $hash = strtoupper(sha1($password));

  $prefix = substr($hash, 0, 5);
  $sufix = substr($hash, 5);

  $output = file_get_contents('https://api.pwnedpasswords.com/range/' . $prefix);

  $pos = strpos($output, $sufix);

  if ($pos === false) {
    return true;
  } else {
    return false;
  }
}

Flight::start();