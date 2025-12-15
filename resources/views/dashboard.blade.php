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

    <header class="bg-blue-500 dark:bg-blue-700 text-white py-4 px-6 flex justify-between items-center">
    <h1 class="text-xl font-bold">My Tasks</h1>

    <div class="flex items-center gap-3">

            <button id="themeToggle" class="px-3 py-1 bg-black/20 rounded hover:bg-black/30">🌙</button>

            <a href="/add-task" class="bg-green-600 px-3 py-1 rounded hover:bg-green-700">
        + Add Task
      </a>

            <button id="logoutBtn" class="bg-white text-blue-500 px-3 py-1 rounded hover:bg-gray-200 dark:bg-gray-300">
        Logout
      </button>
    </div>
  </header>

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

    <main class="p-6 max-w-2xl mx-auto">
    <div id="taskContainer" class="grid gap-4"></div>
  </main>

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
    let editingTaskId = null;

    // =======================================================
    // 💡 FUNGSI PEMBANTU DEADLINE
    // =======================================================
    function formatDeadlineStatus(deadline, isCompleted) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (!deadline) {
            return { text: 'No Deadline', class: 'bg-gray-100 dark:bg-gray-700 text-gray-500' };
        }
        
        // Jika sudah selesai, abaikan status deadline, tampilkan Completed
        if (isCompleted) {
             return { text: 'Completed', class: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' };
        }

        const deadlineDate = new Date(deadline);
        deadlineDate.setHours(0, 0, 0, 0);
        
        const diffTime = deadlineDate.getTime() - today.getTime();
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) {
            const daysOverdue = Math.abs(diffDays);
            return { text: `Overdue (${daysOverdue} days)`, class: 'bg-red-500 text-white' };
        } else if (diffDays === 0) {
            return { text: 'Due Today', class: 'bg-yellow-500 text-white' };
        } else if (diffDays <= 3) {
            return { text: `${diffDays} days left`, class: 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' };
        } else {
            return { text: `${diffDays} days left`, class: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' };
        }
    }

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

        // Tentukan status completed baru
        const newCompletedStatus = !task.completed;
        
        // PENTING: Lakukan perubahan status visual langsung (Optimistic UI)
        task.completed = newCompletedStatus;
        renderTasks(); // Render ulang untuk menampilkan perubahan

        try {
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
                    completed: newCompletedStatus, // Mengirim boolean
                    priority: task.priority,
                    deadline: task.deadline || null
                })
            });

            if (!res.ok) {
                // Jika gagal di server, kembalikan status di UI
                task.completed = !newCompletedStatus; 
                renderTasks();
                throw new Error("Gagal mengupdate status task di server.");
            }
            
            // Jika sukses, fetchTasks akan dipanggil di akhir proses saveEdit/toggleTask
            // fetchTasks(); // Dihapus karena sudah ada renderTasks() optimistic

        } catch (err) {
            alert("Error: " + err.message);
            // Jika error saat toggleTask, panggil fetchTasks untuk sinkronisasi ulang state
            fetchTasks();
        }
    }

    /* 🔎 FILTER & ELEMENTS */
    const searchInput = document.getElementById("searchInput");
    const filterPriority = document.getElementById("filterPriority");
    const filterStatus = document.getElementById("filterStatus");
    const sortBy = document.getElementById("sortBy");
    const taskContainer = document.getElementById("taskContainer");

    // --- ELEMENT MODAL ---
    const editModal = document.getElementById("editModal");
    const editTitle = document.getElementById("editTitle");
    const editDescription = document.getElementById("editDescription");
    const editPriority = document.getElementById("editPriority");
    const editDeadline = document.getElementById("editDeadline");
    const btnSaveEdit = document.getElementById("btnSaveEdit");

    /* 🎨 RENDER (Diperbarui) */
    function renderTasks() {
        let filtered = [...tasks];

        // 1. Search
        const q = searchInput.value.toLowerCase();
        filtered = filtered.filter(t =>
            t.title.toLowerCase().includes(q) ||
            (t.description && t.description.toLowerCase().includes(q))
        );

        // 2. Filter Priority
        if (filterPriority.value !== "All")
            filtered = filtered.filter(t => (t.priority || 'medium').toLowerCase() === filterPriority.value.toLowerCase());

        // 3. Filter Status
        if (filterStatus.value !== "All") {
            const isComplete = filterStatus.value.toLowerCase() === "completed";
            filtered = filtered.filter(t => Boolean(t.completed) === isComplete);
        }

        // 4. Sort
        if (sortBy.value === "deadline") {
            filtered.sort((a, b) => {
                const dateA = a.deadline ? new Date(a.deadline) : new Date(8640000000000000);
                const dateB = b.deadline ? new Date(b.deadline) : new Date(8640000000000000);
                return dateA - dateB;
            });
        }
        if (sortBy.value === "priority") {
            const pVal = { high: 1, medium: 2, low: 3 }; 
            filtered.sort((a, b) => (pVal[a.priority.toLowerCase()] || 99) - (pVal[b.priority.toLowerCase()] || 99));
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
            const desc = t.description || "-";
            const isDone = Boolean(t.completed);
            
            // Tentukan warna prioritas
            let priorityClass = '';
            switch ((t.priority || '').toLowerCase()) {
                case 'high':
                    priorityClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200';
                    break;
                case 'medium':
                    priorityClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200';
                    break;
                case 'low':
                    priorityClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200';
                    break;
                default:
                    priorityClass = 'bg-gray-100 dark:bg-gray-700';
            }
            
            const deadlineStatus = formatDeadlineStatus(t.deadline, isDone);
            
            // Tentukan style tombol Completed/Pending
            const completeBtnText = isDone ? 'Mark as Pending' : 'Mark as Complete';
            const completeBtnClass = isDone 
                ? 'bg-orange-500 hover:bg-orange-600 text-white' // Orange jika ingin dikembalikan ke Pending
                : 'bg-green-600 hover:bg-green-700 text-white'; // Hijau jika ingin diselesaikan

            taskContainer.innerHTML += `
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex justify-between items-start transition hover:shadow-md">
          <div class="flex-grow">
            <h3 class="font-bold text-lg ${isDone ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200'}">
              ${t.title}
            </h3>
            <p class="text-sm text-gray-500 mb-2">${desc}</p>
            <div class="flex flex-wrap gap-2 text-xs">
               <span class="${deadlineStatus.class} px-2 py-0.5 rounded font-medium">📅 ${deadlineStatus.text}</span>
               <span class="${priorityClass} px-2 py-0.5 rounded">
                 ${t.priority || 'Normal'}
               </span>
            </div>
          </div>
          
                    <div class="flex flex-col items-end gap-1 ml-4">
                <button onclick="toggleTask(${t.id})" 
                    class="px-3 py-1 text-sm rounded transition ${completeBtnClass}">
                    ${completeBtnText}
                </button>
                <div class="flex gap-2">
                    <button onclick="openEditModal(${t.id})" 
                        class="text-blue-500 hover:text-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600 p-1.5 rounded transition">
                        📝
                    </button>
                    <button onclick="deleteTask(${t.id})" 
                        class="text-red-500 hover:text-red-700 dark:bg-gray-700 dark:hover:bg-gray-600 p-1.5 rounded transition">
                        🗑️
                    </button>
                </div>
          </div>
                  </div>`;
        });
    }

    /* 📝 EDIT MODAL LOGIC */
    function openEditModal(id) {
        const task = tasks.find(t => t.id == id);
        if (!task) return;

        editingTaskId = id;
        
        editTitle.value = task.title;
        editDescription.value = task.description || '';
        
        const priorityToSet = task.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1).toLowerCase() : 'Medium';
        editPriority.value = priorityToSet;
        
        editDeadline.value = task.deadline || '';

        editModal.classList.remove('hidden');
    }

    async function saveEdit() {
        if (!editingTaskId) return;

        const updatedTask = tasks.find(t => t.id == editingTaskId);
        if (!updatedTask) return;

        const newTitle = editTitle.value.trim();
        const newDescription = editDescription.value.trim();
        const newPriority = editPriority.value;
        const newDeadline = editDeadline.value;

        if (!newTitle) {
            alert("Title tidak boleh kosong.");
            return;
        }

        btnSaveEdit.disabled = true;
        btnSaveEdit.textContent = "Saving...";

        try {
            const res = await fetch(`/api/tasks/${editingTaskId}`, {
                method: "PUT",
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    title: newTitle,
                    description: newDescription,
                    priority: newPriority.toLowerCase(), 
                    deadline: newDeadline || null,
                    completed: Boolean(updatedTask.completed),
                })
            });

            const data = await res.json();

            if (!res.ok) {
                 let errorMessage = "Gagal menyimpan task";
                if (data.errors) {
                   errorMessage = Object.values(data.errors)[0][0];
                } else if (data.message) {
                   errorMessage = data.message;
                }
                throw new Error(errorMessage);
            }

            closeEditModal();
            fetchTasks(); 

        } catch (err) {
            alert("Error: " + err.message);
        } finally {
            btnSaveEdit.disabled = false;
            btnSaveEdit.textContent = "Save";
        }
    }

    function closeEditModal() {
        editingTaskId = null;
        editModal.classList.add('hidden');
    }

    /* EVENTS */
    searchInput.oninput = renderTasks;
    filterPriority.onchange = renderTasks;
    filterStatus.onchange = renderTasks;
    sortBy.onchange = renderTasks;

    // EVENT MODAL
    document.getElementById("btnCancelEdit").onclick = closeEditModal;
    btnSaveEdit.onclick = saveEdit;
    
    // Global functions (agar bisa dipanggil dari onchange/onclick di HTML)
    window.toggleTask = toggleTask;
    window.deleteTask = deleteTask;
    window.openEditModal = openEditModal;


    /* 🚪 LOGOUT */
    document.getElementById("logoutBtn").onclick = async () => {
        try {
            await fetch("/api/logout", {
                method: "POST",
                headers: { "Authorization": `Bearer ${token}` }
            });
        } catch (e) { console.log("Logout error", e); } 

        localStorage.removeItem("token");
        localStorage.removeItem("user");
        window.location.href = "/login";
    };

    /* 🚀 START */
    fetchTasks();
</script>

</body>

</html>