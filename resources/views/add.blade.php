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
    <input id="title"
           class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600"
           placeholder="Judul task">

    <label class="block mb-2 font-semibold">Description</label>
    <textarea id="description"
              class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600"
              placeholder="Deskripsi (opsional)"></textarea>

    <label class="block mb-2 font-semibold">Priority</label>
    <select id="priority"
            class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600">
      <option value="Low">Low</option>
      <option value="Medium">Medium</option>
      <option value="High">High</option>
    </select>

    <label class="block mb-2 font-semibold">Deadline *</label>
    <input id="deadline"
           type="date"
           class="w-full border p-2 rounded mb-4 dark:bg-gray-700 dark:border-gray-600">

    <p id="errorMsg" class="text-red-500 text-sm mb-3"></p>

    <button id="addBtn"
            class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
      Save Task
    </button>

  </div>
</main>

<script>
/* DARK MODE */
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

/* LOCAL STORAGE */
function loadTasks() {
  return JSON.parse(localStorage.getItem("tasks") || "[]");
}

function saveTasks(tasks) {
  localStorage.setItem("tasks", JSON.stringify(tasks));
}

/* ADD TASK */
document.getElementById("addBtn").onclick = () => {
  const title = titleInput.value.trim();
  const desc = description.value.trim();
  const priority = document.getElementById("priority").value;
  const deadline = document.getElementById("deadline").value;
  const error = document.getElementById("errorMsg");

  error.textContent = "";

  if (!title) {
    error.textContent = "Title tidak boleh kosong";
    return;
  }

  if (!deadline) {
    error.textContent = "Deadline wajib diisi";
    return;
  }

  const tasks = loadTasks();
  tasks.push({ title, description: desc, priority, deadline, completed: false });
  saveTasks(tasks);

  window.location.href = "{{ url('/dashboard') }}";
};
</script>

</body>
</html>
