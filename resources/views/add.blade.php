<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Task — TodoList</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' }
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">

  <!-- HEADER -->
  <header class="bg-blue-500 dark:bg-blue-700 text-white py-4 px-6 flex justify-between items-center">
    <h1 class="text-xl font-bold">Add New Task</h1>

    <div class="flex items-center gap-3">
      <!-- DARK MODE -->
      <button id="themeToggle" class="px-3 py-1 bg-black/20 rounded hover:bg-black/30">
        🌙
      </button>

      <!-- BACK -->
      <a href="{{ url('/dashboard') }}"
        class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 dark:bg-gray-300 dark:text-gray-900">
        ← Back
      </a>
    </div>
  </header>

  <!-- MAIN -->
  <main class="p-6 max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">

      <label class="block mb-2 font-semibold">Title *</label>
      <input id="title" class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600"
        placeholder="Judul task">

      <label class="block mb-2 font-semibold">Description</label>
      <textarea id="description" class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600"
        placeholder="Deskripsi (opsional)"></textarea>
      <label class="block mb-2 font-semibold">Category</label>
      <select id="category" class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600">
        <option value="">No Category</option>
      </select>

      <label class="block mb-2 font-semibold">Priority</label>
      <select id="priority" class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600">
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
      </select>

      <label class="block mb-2 font-semibold">Deadline *</label>
      <input id="deadline" type="date" class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600">

      <p id="errorMsg" class="text-red-500 text-sm mb-3"></p>

      <button id="addBtn" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
        Save Task
      </button>

    </div>
  </main>

  <script>
    /* 🔐 PROTEKSI LOGIN */
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "/login";
    }

    /* 🌙 DARK MODE */
    const themeBtn = document.getElementById("themeToggle");
    themeBtn.onclick = () => {
      document.documentElement.classList.toggle("dark");
      localStorage.setItem("theme",
        document.documentElement.classList.contains("dark") ? "dark" : "light"
      );
    };
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
    }

    /* 📦 FETCH CATEGORIES */
    async function fetchCategories() {
      try {
        const res = await fetch("/api/categories", {
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          }
        });
        if (res.ok) {
          const categories = await res.json();
          const catSelect = document.getElementById("category");
          categories.forEach(cat => {
            const opt = document.createElement("option");
            opt.value = cat.id;
            opt.textContent = cat.name;
            catSelect.appendChild(opt);
          });
        }
      } catch (err) {
        console.error("Gagal mengambil kategori:", err);
      }
    }
    fetchCategories();

    /* 📡 ADD TASK VIA API */
    document.getElementById("addBtn").onclick = async () => {
      const title = document.getElementById("title").value.trim();
      const desc = document.getElementById("description").value.trim();
      const priority = document.getElementById("priority").value;
      const deadline = document.getElementById("deadline").value;
      const error = document.getElementById("errorMsg");
      const btn = document.getElementById("addBtn");

      error.textContent = "";

      if (!title) {
        error.textContent = "Title tidak boleh kosong";
        return;
      }

      // Note: Validasi deadline di backend mungkin opsional, tapi frontend check bagus
      if (!deadline) {
        error.textContent = "Deadline wajib diisi";
        return;
      }

      // Disable button
      btn.disabled = true;
      btn.textContent = "Saving...";

      try {
        const res = await fetch("/api/tasks", {
          method: "POST",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({
            title: title,
            description: desc,
            priority: priority,
            deadline: deadline,
            category_id: document.getElementById("category").value || null
          })
        });

        const data = await res.json();

        if (!res.ok) {
          // Tampilkan error validasi dari server
          if (data.errors) {
            throw new Error(Object.values(data.errors)[0][0]);
          }
          throw new Error(data.message || "Gagal menyimpan task");
        }

        // Sukses
        window.location.href = "/dashboard";

      } catch (err) {
        error.textContent = err.message || "Terjadi kesalahan saat menyimpan";
        btn.disabled = false;
        btn.textContent = "Save Task";
      }
    };
  </script>

</body>

</html>