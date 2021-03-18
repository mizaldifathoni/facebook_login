<?php

 require_once 'config.php';

$permissions = ['email'];

if (isset($accessToken))
{
	if (!isset($_SESSION['facebook_access_token'])) {

		$_SESSION['facebook_access_token'] = (string) $accessToken;

		$oAuth2Client = $fb->getOAuth2Client();

		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}

	if (isset($_GET['code'])) {
		header('Location: ./');
	}

	try {
		$fb_response = $fb->get('/me?fields=name,first_name,last_name,email');
		$fb_response_picture = $fb->get('/me/picture?redirect=false&height=200');

		$fb_user = $fb_response->getGraphUser();
		$picture = $fb_response_picture->getGraphUser();

		$_SESSION['fb_user_id'] = $fb_user->getProperty('id');
		$_SESSION['fb_user_name'] = $fb_user->getProperty('name');
		$_SESSION['fb_user_email'] = $fb_user->getProperty('email');
		$_SESSION['fb_user_pic'] = $picture['url'];


	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Facebook API Error: ' . $e->getMessage();
		session_destroy();

		header("Location: ./");
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK Error: ' . $e->getMessage();
		exit;
	}
} else {

	$fb_login_url = $fb_helper->getLoginUrl('http://localhost/facebook_login/', $permissions);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Masuk dengan Facebook</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">

</head>
<body>

<?php if(isset($_SESSION['fb_user_id'])): ?>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
	  <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Beranda</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
		<span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="collapsibleNavbar">
		<ul class="navbar-nav">
		  <li class="nav-item">
			<a class="nav-link" href="https://www.facebook.com/mizaldifathoni"><i class="fa fa-facebook"> Facebook</i></a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="https://github.com/mizaldifathoni/"><i class="fa fa-github"> GitHub</i></a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="logout.php">Keluar</a>
		  </li>
		</ul>
	  </div>
	</nav>

	<div class="container" style="margin-top:30px">
	  <div class="row">
		<div class="col-sm-2">
		  <h2>Tentang Saya</h2>
		  <h5>Foto Profil:</h5>
		  <div class="fakeimg"><?php echo  $_SESSION['fb_user_pic']; ?></div>
		  <hr class="d-sm-none">
		</div>
		<div class="col-sm-2"></div>
		<div class="col-sm-8">


		  <h3>Informasi Pengguna</h3>
		  <ul class="nav nav-pills flex-column">
			<li class="nav-item">
			  <a class="nav-link" >Facebook ID: <?php echo  $_SESSION['fb_user_id']; ?></a>
			</li>
			<li class="nav-item">
			  <a class="nav-link">Nama Lengkap: <?php echo $_SESSION['fb_user_name']; ?></a>
			</li>
			<li class="nav-item">
			  <a class="nav-link">Email: <?php echo $_SESSION['fb_user_email']; ?></a>
			</li>
		  </ul>

		</div>
	  </div
	</div>

<?php else: ?>

	<div class="login-form">
		<form action="" method="post">
			<br><h2 class="text-center">Masuk</h2><br><br><br>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><span class="fa fa-user"></span></span>
					</div>
					<input type="text" class="form-control" name="username" placeholder="E-mail">
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="fa fa-lock"></i></span>
					</div>
					<input type="password" class="form-control" name="password" placeholder="Password">
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success btn-block login-btn">Masuk</button>
			</div>
			<div class="text-center"><i><h4>atau</h4></i></div><br>

			<div class="text-center social-btn">
				<a href="<?php echo $fb_login_url;?>" class="btn btn-primary btn-block"><i class="fa fa-facebook"></i> Masuk dengan <b>Facebook</b></a>
			</div>

	</div>
<?php endif ?>

</body>
</html>