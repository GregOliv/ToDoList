<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login — TodoList</title>

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-sm">
    <div class="bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-semibold text-center mb-4">Masuk</h1>

      <!-- Error -->
      <div id="error" class="text-red-600 text-sm mb-3"></div>

      <!-- Email -->
      <label class="block text-sm">Email</label>
      <input id="email" type="email" class="w-full border rounded-md p-2 mb-3" placeholder="email@example.com" />

      <!-- Password -->
      <label class="block text-sm">Password</label>
      <input id="password" type="password" class="w-full border rounded-md p-2 mb-4" placeholder="••••••"
        minlength="6" />

      <!-- Login -->
      <button id="btnLogin" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">
        Login
      </button>

      <!-- Register -->
      <p class="text-center text-sm text-gray-500 mt-4">
        Belum punya akun?
        <a href="{{ url('/register') }}" class="text-blue-600 underline">
          Daftar
        </a>
      </p>
    </div>
  </div>

  <script>
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const error = document.getElementById("error");
    const btnLogin = document.getElementById("btnLogin");

    /* AUTO REDIRECT JIKA SUDAH LOGIN */
    if (localStorage.getItem("token")) {
      window.location.href = "{{ url('/dashboard') }}";
    }

    /* LOGIN VIA API */
    btnLogin.onclick = async () => {
      const email = emailInput.value.trim();
      const password = passwordInput.value.trim();

      error.textContent = "";

      if (!email || !password) {
        error.textContent = "Email dan password wajib diisi";
        return;
      }

      if (password.length < 6) {
        error.textContent = "Password minimal 6 karakter";
        return;
      }

      // Disable button saat loading
      btnLogin.disabled = true;
      btnLogin.textContent = "Loading...";

      try {
        // Call API Login
        const response = await fetch("/api/auth/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ email, password }),
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || "Login gagal");
        }

        // Simpan token ke localStorage
        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify({ email }));

        // Redirect ke dashboard
        window.location.href = "{{ url('/dashboard') }}";

      } catch (err) {
        error.textContent = err.message || "Terjadi kesalahan, coba lagi";
        btnLogin.disabled = false;
        btnLogin.textContent = "Login";
      }
    };
  </script>

</body>

</html>