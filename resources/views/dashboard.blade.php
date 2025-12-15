<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard — TodoList</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' }
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">

  <!-- HEADER -->
  <header class="bg-blue-500 dark:bg-blue-700 text-white py-4 px-6 flex justify-between items-center">
    <h1 class="text-xl font-bold">My Tasks</h1>

    <div class="flex items-center gap-3">

      <!-- DARK MODE -->
      <button id="themeToggle" class="px-3 py-1 bg-black/20 rounded hover:bg-black/30">🌙</button>

      <!-- ADD TASK -->
      <a href="/add-task" class="bg-green-600 px-3 py-1 rounded hover:bg-green-700">
        + Add Task
      </a>

      <!-- LOGOUT -->
      <button id="logoutBtn" class="bg-white text-blue-500 px-3 py-1 rounded hover:bg-gray-200 dark:bg-gray-300">
        Logout
      </button>
    </div>
  </header>

  <!-- FILTER + SEARCH + SORT -->
  <section class="p-6 max-w-2xl mx-auto grid gap-3">

    <input id="searchInput" class="w-full p-2 rounded border dark:bg-gray-700 dark:border-gray-600"
      placeholder="Search task by title or description...">

    <div class="grid grid-cols-3 gap-3">
      <select id="filterPriority" class="p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
        <option value="All">All Priority</option>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
      </select>

      <select id="filterStatus" class="p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
        <option value="All">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
      </select>

      <select id="sortBy" class="p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
        <option value="none">Sort: None</option>
        <option value="deadline">Deadline (Soonest)</option>
        <option value="priority">Priority (Highest)</option>
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
      </select>
    </div>
  </section>

  <!-- TASK LIST -->
  <main class="p-6 max-w-2xl mx-auto">
    <div id="taskContainer" class="grid gap-4"></div>
  </main>

  <!-- EDIT MODAL -->
  <div id="editModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow">

      <h2 class="text-lg font-semibold mb-3">Edit Task</h2>

      <input id="editTitle" class="w-full border p-2 rounded mb-3 dark:bg-gray-700 dark:border-gray-600">
      <textarea id="editDescription"
        class="w-full border p-2 rounded mb-3 dark:bg-gray-700 dark:border-gray-600"></textarea>

      <div class="flex gap-3 mb-3">
        <select id="editPriority" class="border p-2 rounded w-1/2 dark:bg-gray-700 dark:border-gray-600">
          <option>Low</option>
          <option>Medium</option>
          <option>High</option>
        </select>

        <input id="editDeadline" type="date" class="border p-2 rounded w-1/2 dark:bg-gray-700 dark:border-gray-600">
      </div>

      <div class="flex justify-end gap-3">
        <button id="btnCancelEdit" class="px-4 py-2 bg-gray-300 rounded dark:bg-gray-600">Cancel</button>
        <button id="btnSaveEdit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
      </div>
    </div>
  </div>

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
        document.documentElement.classList.contains("dark") ? "dark" : "light");
    };
    if (localStorage.getItem("theme") === "dark")
      document.documentElement.classList.add("dark");

    /* 📦 STATE MANAGEMENT */
    let tasks = [];

    /* 📡 API CALLS */
    async function fetchTasks() {
      try {
        const res = await fetch("/api/tasks", {
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          }
        });

        if (res.status === 401) {
          alert("Sesi habis, silakan login kembali");
          localStorage.clear();
          window.location.href = "/login";
          return;
        }

        tasks = await res.json();
        renderTasks();
      } catch (err) {
        console.error("Gagal mengambil data:", err);
      }
    }

    async function deleteTask(id) {
      if (!confirm("Yakin ingin menghapus task ini?")) return;

      try {
        const res = await fetch(`/api/tasks/${id}`, {
          method: "DELETE",
          headers: { "Authorization": `Bearer ${token}` }
        });

        if (res.ok) fetchTasks();
      } catch (err) {
        alert("Gagal menghapus task");
      }
    }

    async function toggleTask(id) {
      const task = tasks.find(t => t.id == id);
      if (!task) return;

      try {
        // Note: Controller membutuhkan title & description saat update
        const res = await fetch(`/api/tasks/${id}`, {
          method: "PUT",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({
            title: task.title,
            description: task.description || "",
            completed: !task.completed // Toggle status
          })
        });

        if (res.ok) fetchTasks();
      } catch (err) {
        alert("Gagal mengupdate status task");
      }
    }

    /* 🔎 FILTER & ELEMENTS */
    const searchInput = document.getElementById("searchInput");
    const filterPriority = document.getElementById("filterPriority");
    const filterStatus = document.getElementById("filterStatus");
    const sortBy = document.getElementById("sortBy");
    const taskContainer = document.getElementById("taskContainer");

    /* 🎨 RENDER */
    function renderTasks() {
      let filtered = [...tasks];

      // 1. Search
      const q = searchInput.value.toLowerCase();
      filtered = filtered.filter(t =>
        t.title.toLowerCase().includes(q) ||
        (t.description && t.description.toLowerCase().includes(q))
      );

      // 2. Filter Priority (Pastikan field priority ada di database/model task)
      if (filterPriority.value !== "All")
        filtered = filtered.filter(t => t.priority === filterPriority.value);

      // 3. Filter Status (completed: 1/0 di DB)
      if (filterStatus.value !== "All") {
        const isComplete = filterStatus.value === "Completed";
        filtered = filtered.filter(t => Boolean(t.completed) === isComplete);
      }

      // 4. Sort
      if (sortBy.value === "deadline")
        filtered.sort((a, b) => (a.deadline || "").localeCompare(b.deadline || ""));
      if (sortBy.value === "priority") {
        const pVal = { High: 1, Medium: 2, Low: 3 };
        filtered.sort((a, b) => (pVal[a.priority] || 99) - (pVal[b.priority] || 99));
      }
      if (sortBy.value === "az")
        filtered.sort((a, b) => a.title.localeCompare(b.title));
      if (sortBy.value === "za")
        filtered.sort((a, b) => b.title.localeCompare(a.title));

      // Render HTML
      taskContainer.innerHTML = "";

      if (!filtered.length) {
        taskContainer.innerHTML = `<p class="text-center text-gray-500">No tasks found</p>`;
        return;
      }

      filtered.forEach(t => {
        // Handle null values
        const desc = t.description || "-";
        const deadline = t.deadline || "-";
        const isDone = Boolean(t.completed); // Pastikan boolean proper

        taskContainer.innerHTML += `
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex justify-between items-start transition hover:shadow-md">
      <div>
        <h3 class="font-bold text-lg ${isDone ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200'}">
          ${t.title}
        </h3>
        <p class="text-sm text-gray-500 mb-1">${desc}</p>
        <div class="flex gap-2 text-xs">
           <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">📅 ${deadline}</span>
           <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded">
             ${t.priority || 'Normal'}
           </span>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <input type="checkbox" 
          class="w-5 h-5 rounded cursor-pointer" 
          ${isDone ? 'checked' : ''} 
          onchange="toggleTask(${t.id})">
          
        <button onclick="deleteTask(${t.id})" 
          class="text-red-500 hover:text-red-700 bg-red-50 dark:bg-red-900/30 p-2 rounded transition">
          🗑️
        </button>
      </div>
    </div>`;
      });
    }

    /* EVENTS */
    searchInput.oninput = renderTasks;
    filterPriority.onchange = renderTasks;
    filterStatus.onchange = renderTasks;
    sortBy.onchange = renderTasks;

    /* 🚪 LOGOUT */
    document.getElementById("logoutBtn").onclick = async () => {
      try {
        await fetch("/api/logout", {
          method: "POST",
          headers: { "Authorization": `Bearer ${token}` }
        });
      } catch (e) { console.log("Logout error", e); } // Ignore error logic, force logout local

      localStorage.removeItem("token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    };

    /* 🚀 START */
    fetchTasks();
  </script>

</body>

</html>