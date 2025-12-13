<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Register — TodoList</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-sm">
  <div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold text-center mb-4">Buat Akun</h1>

    <!-- ERROR -->
    <div id="error" class="text-red-600 text-sm mb-3"></div>

    <!-- USERNAME -->
    <label class="block text-sm">Username</label>
    <input id="username" type="text"
      class="w-full border rounded-md p-2 mb-3"
      placeholder="Username" />

    <!-- EMAIL -->
    <label class="block text-sm">Email</label>
    <input id="email" type="email"
      class="w-full border rounded-md p-2 mb-3"
      placeholder="email@example.com" />

    <!-- PASSWORD -->
    <label class="block text-sm">Password</label>
    <input id="password" type="password"
      class="w-full border rounded-md p-2 mb-3"
      placeholder="Minimal 6 karakter" />

    <!-- CONFIRM PASSWORD -->
    <label class="block text-sm">Confirm Password</label>
    <input id="confirmPassword" type="password"
      class="w-full border rounded-md p-2 mb-4"
      placeholder="Ulangi password" />

    <!-- BUTTON -->
    <button id="btnRegister"
      class="w-full bg-green-600 text-white p-2 rounded-md hover:bg-green-700">
      Create Account
    </button>

    <p class="text-center text-sm text-gray-500 mt-4">
      Sudah punya akun?
      <a href="/login" class="text-blue-600 underline">Login</a>
    </p>
  </div>
</div>

<script>
const btnRegister = document.getElementById("btnRegister");
const err = document.getElementById("error");

btnRegister.addEventListener("click", () => {
  const username = document.getElementById("username").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const confirm = document.getElementById("confirmPassword").value;

  err.textContent = "";

  // VALIDASI FRONTEND
  if (!username || !email || !password || !confirm) {
    err.textContent = "Semua field wajib diisi";
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    err.textContent = "Format email tidak valid";
    return;
  }

  if (password.length < 6) {
    err.textContent = "Password minimal 6 karakter";
    return;
  }

  if (password !== confirm) {
    err.textContent = "Password dan konfirmasi tidak sama";
    return;
  }

  // SIMPAN USER (SIMULASI FRONTEND)
  const users = JSON.parse(localStorage.getItem("users") || "[]");

  if (users.find(u => u.email === email)) {
    err.textContent = "Email sudah digunakan";
    return;
  }

  users.push({ username, email, password });
  localStorage.setItem("users", JSON.stringify(users));

  alert("Registrasi berhasil, silakan login");
  window.location.href = "/login";
});
</script>

</body>
</html>
