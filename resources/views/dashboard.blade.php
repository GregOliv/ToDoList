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
    <button id="logoutBtn"
      class="bg-white text-blue-500 px-3 py-1 rounded hover:bg-gray-200 dark:bg-gray-300">
      Logout
    </button>
  </div>
</header>

<!-- FILTER + SEARCH + SORT -->
<section class="p-6 max-w-2xl mx-auto grid gap-3">

  <input id="searchInput"
    class="w-full p-2 rounded border dark:bg-gray-700 dark:border-gray-600"
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
    <textarea id="editDescription" class="w-full border p-2 rounded mb-3 dark:bg-gray-700 dark:border-gray-600"></textarea>

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
if (!localStorage.getItem("token")) {
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

/* 📦 STORAGE */
const loadTasks = () => JSON.parse(localStorage.getItem("tasks") || "[]");
const saveTasks = t => localStorage.setItem("tasks", JSON.stringify(t));

/* 🔎 FILTER */
const searchInput = document.getElementById("searchInput");
const filterPriority = document.getElementById("filterPriority");
const filterStatus = document.getElementById("filterStatus");
const sortBy = document.getElementById("sortBy");
const taskContainer = document.getElementById("taskContainer");

/* 🎨 RENDER */
function renderTasks() {
  let tasks = loadTasks();

  const q = searchInput.value.toLowerCase();
  tasks = tasks.filter(t =>
    t.title.toLowerCase().includes(q) ||
    t.description.toLowerCase().includes(q)
  );

  if (filterPriority.value !== "All")
    tasks = tasks.filter(t => t.priority === filterPriority.value);

  if (filterStatus.value !== "All")
    tasks = tasks.filter(t => t.completed === (filterStatus.value === "Completed"));

  if (sortBy.value === "deadline")
    tasks.sort((a,b)=>a.deadline.localeCompare(b.deadline));
  if (sortBy.value === "priority")
    tasks.sort((a,b)=>({High:1,Medium:2,Low:3}[a.priority]-{High:1,Medium:2,Low:3}[b.priority]));
  if (sortBy.value === "az")
    tasks.sort((a,b)=>a.title.localeCompare(b.title));
  if (sortBy.value === "za")
    tasks.sort((a,b)=>b.title.localeCompare(a.title));

  taskContainer.innerHTML = "";

  if (!tasks.length) {
    taskContainer.innerHTML = `<p class="text-center text-gray-500">No tasks</p>`;
    return;
  }

  tasks.forEach((t,i)=>{
    taskContainer.innerHTML += `
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex justify-between">
      <div>
        <h3 class="font-bold ${t.completed?'line-through text-gray-400':''}">${t.title}</h3>
        <p class="text-sm text-gray-500">${t.description}</p>
        <p class="text-xs">Deadline: ${t.deadline}</p>
      </div>
      <div class="flex gap-2">
        <input type="checkbox" ${t.completed?'checked':''} data-toggle="${i}">
        <button data-del="${i}" class="text-red-500">✕</button>
      </div>
    </div>`;
  });
}

/* EVENTS */
searchInput.oninput = renderTasks;
filterPriority.onchange = renderTasks;
filterStatus.onchange = renderTasks;
sortBy.onchange = renderTasks;

document.addEventListener("change", e=>{
  if(e.target.dataset.toggle){
    const t = loadTasks();
    t[e.target.dataset.toggle].completed = !t[e.target.dataset.toggle].completed;
    saveTasks(t); renderTasks();
  }
});

document.addEventListener("click", e=>{
  if(e.target.dataset.del){
    const t = loadTasks();
    t.splice(e.target.dataset.del,1);
    saveTasks(t); renderTasks();
  }
});

/* 🚪 LOGOUT */
document.getElementById("logoutBtn").onclick = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  window.location.href = "/login";
};

renderTasks();
</script>

</body>
</html>
