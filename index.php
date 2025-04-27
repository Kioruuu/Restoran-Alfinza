<?php
session_start();
include "connection/koneksi.php";

// Kalo udah login, langsung ke beranda
if(isset($_SESSION['id_user'])) {
	header('location: beranda.php');
	exit();
}

if(isset($_POST['login'])) {
	$username = mysqli_real_escape_string($conn, $_POST['username']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	
	$query = "SELECT * FROM tb_user WHERE username = '$username' AND password = '$password'";
	$sql = mysqli_query($conn, $query);
	
	if(mysqli_num_rows($sql) > 0) {
		$r = mysqli_fetch_array($sql);
		$_SESSION['id_user'] = $r['id_user'];
		$_SESSION['username'] = $r['username'];
		$_SESSION['id_level'] = $r['id_level'];
		header('location: beranda.php');
		exit();
	} else {
		echo "<script>alert('Username atau Password salah!');</script>";
	}
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - Restoran</title>
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
	<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-md w-full">
			<!-- Logo -->
			<div class="flex justify-center mb-8">
				<div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
					Restaurant V-1
				</div>
			</div>

			<!-- Login Form -->
			<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
				<div class="sm:mx-auto sm:w-full sm:max-w-md">
					<h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white mb-6">
						Sign in to your account
					</h2>
				</div>

				<form class="space-y-6" method="POST">
					<div>
						<label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							Username
						</label>
						<div class="mt-1 relative rounded-md shadow-sm">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<i class="fas fa-user text-gray-400"></i>
							</div>
							<input type="text" name="username" id="username" required 
								   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
								   placeholder="Enter your username">
						</div>
					</div>

					<div>
						<label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							Password
						</label>
						<div class="mt-1 relative rounded-md shadow-sm">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<i class="fas fa-lock text-gray-400"></i>
							</div>
							<input type="password" name="password" id="password" required
								   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
								   placeholder="Enter your password">
						</div>
					</div>

					<div>
						<button type="submit" name="login"
								class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
							<span class="absolute left-0 inset-y-0 flex items-center pl-3">
								<i class="fas fa-sign-in-alt text-indigo-300 group-hover:text-indigo-200"></i>
							</span>
							Sign in
						</button>
					</div>
				</form>

				<div class="mt-6">
					<div class="relative">
						<div class="absolute inset-0 flex items-center">
							<div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
						</div>
						<div class="relative flex justify-center text-sm">
							<span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
								Don't have an account?
							</span>
						</div>
					</div>

					<div class="mt-6">
						<a href="daftar.php"
							 class="w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 dark:text-blue-400 dark:bg-gray-700 dark:hover:bg-gray-600">
							Create new account
						</a>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div class="mt-8 text-center">
				<p class="text-sm text-gray-500 dark:text-gray-400">
					&copy; <?php echo date('Y'); ?> Restaurant V-1. All rights reserved.
				</p>
			</div>
		</div>
	</div>

	<script>
	// Check for dark mode preference
	if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
		document.documentElement.classList.add('dark');
	} else {
		document.documentElement.classList.remove('dark');
	}
	</script>
</body>
</html>