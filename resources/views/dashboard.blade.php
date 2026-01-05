<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard — TodoList</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = { darkMode: 'class' }
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 font-sans">

  <!-- HEADER -->
  <header
    class="bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-800 dark:to-indigo-900 text-white py-4 px-6 flex justify-between items-center shadow-lg sticky top-0 z-50">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold tracking-tight">My Tasks</h1>
    </div>

    <div class="flex items-center gap-4">

      <!-- CLOCK -->
      <div id="realtimeClock"
        class="hidden md:block text-sm font-mono bg-white/10 px-3 py-1 rounded-full border border-white/10 shadow-sm backdrop-blur-sm">
        --:--:--
      </div>

      <!-- DARK MODE -->
      <button id="themeToggle" class="p-2 rounded-full hover:bg-white/20 transition-all duration-300"
        title="Toggle Theme">
        🌙
      </button>

      <!-- PROFILE (CUSTOMIZATION) -->
      <button id="profileBtn" onclick="openProfileModal()"
        class="flex items-center justify-center p-2 rounded-full hover:bg-white/20 transition-all duration-300"
        title="Customize Profile">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </button>

      <!-- ADD TASK -->
      <a href="/add-task"
        class="hidden sm:inline-block bg-green-500 text-white px-5 py-2 rounded-full shadow-lg hover:bg-green-600 hover:shadow-xl transition-all font-medium">
        + Add Task
      </a>
      <!-- Mobile Add Task -->
      <a href="/add-task"
        class="sm:hidden bg-green-500 text-white p-2 rounded-full shadow-lg hover:bg-green-600 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
      </a>
      <!-- CATEGORIES -->
      <button id="manageCategoriesBtn"
        class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-4 py-2 rounded-full hover:bg-white/20 transition-all font-medium text-sm">
        Categories
      </button>


      <!-- LOGOUT -->
      <button id="logoutBtn"
        class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-4 py-2 rounded-full hover:bg-white/20 transition-all font-medium text-sm">
        Logout
      </button>
    </div>
  </header>

  <!-- NOTIFICATION CONTAINER -->
  <div id="notificationStack" class="fixed top-20 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

  <!-- STATS & CHART SECTION -->
  <section class="p-6 max-w-4xl mx-auto mt-6">
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 flex flex-col md:flex-row items-center justify-around gap-10 hover:shadow-2xl transition-shadow duration-300">

      <!-- Chart Container -->
      <div class="w-56 h-56 relative animate-in fade-in zoom-in duration-500">
        <canvas id="taskChart"></canvas>
      </div>

      <!-- Tally / Details -->
      <div id="tallyDisplay" class="flex gap-8 text-center w-full md:w-auto justify-center">
        <!-- Injected by JS -->
        <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-10 w-32 rounded"></div>
      </div>

    </div>
  </section>

  <!-- FILTER + SEARCH + SORT -->
  <section class="px-6 max-w-3xl mx-auto grid gap-4 mb-8">

    <div class="relative group">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
          xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd"
            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
            clip-rule="evenodd" />
        </svg>
      </div>
      <input id="searchInput"
        class="w-full pl-10 p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900 outline-none transition-all shadow-sm"
        placeholder="Search task by title or description...">
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3">
      <select id="filterPriority"
        class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
        <option value="All">All Priority</option>
        <option value="Low">Low Priority</option>
        <option value="Medium">Medium Priority</option>
        <option value="High">High Priority</option>
      </select>

      <select id="filterStatus"
        class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
        <option value="All">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
      </select>

      <select id="filterCategory"
        class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
        <option value="All">All Categories</option>
      </select>

      <select id="sortBy"
        class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
        <option value="none">Sort: Default</option>
        <option value="deadline">Deadline (Soonest)</option>
        <option value="priority">Priority (Highest)</option>
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
      </select>
    </div>
  </section>

  <!-- TASK LIST -->
  <main class="px-6 pb-20 max-w-3xl mx-auto">
    <div id="taskContainer" class="space-y-4"></div>
  </main>

  <!-- EDIT MODAL -->
  <div id="editModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity opacity-0 pointer-events-none data-[state=open]:opacity-100 data-[state=open]:pointer-events-auto">
    <div
      class="bg-white dark:bg-gray-800 p-8 rounded-2xl w-full max-w-lg shadow-2xl transform transition-transform scale-95 data-[state=open]:scale-100">

      <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Edit Task
      </h2>

      <div class="space-y-5">
        <div>
          <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Title</label>
          <input id="editTitle"
            class="w-full border border-gray-300 dark:border-gray-600 p-3 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Description</label>
          <textarea id="editDescription"
            class="w-full border border-gray-300 dark:border-gray-600 p-3 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-colors"
            rows="3"></textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Category</label>
          <select id="editCategory"
            class="w-full border border-gray-300 dark:border-gray-600 p-3 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
            <option value="">No Category</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Priority</label>
            <select id="editPriority"
              class="w-full border border-gray-300 dark:border-gray-600 p-3 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
              <option>Low</option>
              <option>Medium</option>
              <option>High</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Deadline</label>
            <input id="editDeadline" type="date"
              class="w-full border border-gray-300 dark:border-gray-600 p-3 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-8">
        <button id="btnCancelEdit"
          class="px-5 py-2.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-medium">Cancel</button>
        <button id="btnSaveEdit"
          class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-medium">Save
          Changes</button>
      </div>
    </div>
  </div>
  <!-- CATEGORY MODAL -->
  <div id="categoriesModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity opacity-0 pointer-events-none data-[state=open]:opacity-100 data-[state=open]:pointer-events-auto">
    <div
      class="bg-white dark:bg-gray-800 p-8 rounded-2xl w-full max-w-md shadow-2xl transform transition-transform scale-95 data-[state=open]:scale-100">

      <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Manage
        Categories</h2>

      <!-- Add Category Form -->
      <div class="flex gap-2 mb-6">
        <input id="newCategoryName"
          class="flex-1 border border-gray-300 dark:border-gray-600 p-2 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
          placeholder="New category name...">
        <input type="color" id="newCategoryColor" value="#3B82F6"
          class="w-10 h-10 p-0 border-none rounded bg-transparent cursor-pointer">
        <button id="btnAddCategory"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all font-medium">+</button>
      </div>

      <!-- Categories List -->
      <div id="categoriesList" class="space-y-2 max-h-60 overflow-y-auto mb-6 pr-2">
        <!-- Injected by JS -->
      </div>

      <div class="flex justify-end pt-4 border-t dark:border-gray-700">
        <button id="btnCloseCategories"
          class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">Close</button>
      </div>
    </div>
  </div>

  <!-- PROFILE MODAL -->
  <div id="profileModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity opacity-0 pointer-events-none data-[state=open]:opacity-100 data-[state=open]:pointer-events-auto">
    <div
      class="bg-white dark:bg-gray-800 p-8 rounded-2xl w-full max-w-lg shadow-2xl transform transition-transform scale-95 data-[state=open]:scale-100">

      <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Account
        Profile</h2>

      <div class="space-y-6 max-h-[70vh] overflow-y-auto px-1">
        <!-- Avatar Section -->
        <div class="flex flex-col items-center gap-4">
          <div class="relative group">
            <img id="profileAvatarImg" src="https://ui-avatars.com/api/?name=User&background=random"
              class="w-24 h-24 rounded-full object-cover border-4 border-blue-500 shadow-md">
            <label for="avatarInput"
              class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </label>
            <input type="file" id="avatarInput" class="hidden" accept="image/*">
          </div>
          <p class="text-xs text-gray-500">Click to change avatar</p>
        </div>

        <!-- Name & Email -->
        <div class="grid gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Full Name</label>
            <input id="profileName"
              class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Email Address</label>
            <input id="profileEmail" type="email"
              class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <button id="btnSaveProfile"
            class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition-all font-medium shadow-md">Update
            Profile</button>
        </div>

        <!-- Password Change -->
        <div class="pt-6 mt-6 border-t dark:border-gray-700 grid gap-4">
          <h3 class="font-bold text-gray-800 dark:text-white">Change Password</h3>
          <input id="currentPassword" type="password" placeholder="Current Password"
            class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
          <input id="newPassword" type="password" placeholder="New Password"
            class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
          <input id="confirmPassword" type="password" placeholder="Confirm New Password"
            class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
          <button id="btnSavePassword"
            class="w-full border border-blue-600 text-blue-600 py-2.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all font-medium">Update
            Password</button>
        </div>
      </div>

      <div class="flex justify-end pt-6 mt-6 border-t dark:border-gray-700">
        <button id="btnCloseProfile"
          class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">Close</button>
      </div>
    </div>
  </div>

  <script>
    /* 🔐 PROTEKSI LOGIN */
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "/login";
    }

    /* ⏰ REALTIME CLOCK */
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('en-GB', { hour12: false }); // HH:MM:SS
      document.getElementById('realtimeClock').textContent = timeString;
    }
    setInterval(updateTime, 1000);
    updateTime(); // init immediately

    /* 🌙 DARK MODE & CHART THEME */
    const themeBtn = document.getElementById("themeToggle");

    function updateChartTheme() {
      if (!myChart) return;
      const isDark = document.documentElement.classList.contains("dark");
      const textColor = isDark ? '#e5e7eb' : '#374151'; // gray-200 : gray-700

      // Update Legend Color
      if (myChart.options.plugins && myChart.options.plugins.legend) {
        myChart.options.plugins.legend.labels.color = textColor;
      }
      myChart.update();
    }

    themeBtn.onclick = () => {
      document.documentElement.classList.toggle("dark");
      const isDark = document.documentElement.classList.contains("dark");
      localStorage.setItem("theme", isDark ? "dark" : "light");

      // Update Chart Color on Toggle
      updateChartTheme();
    };

    if (localStorage.getItem("theme") === "dark")
      document.documentElement.classList.add("dark");

    /* 📦 STATE MANAGEMENT */
    let tasks = [];
    let categories = [];
    let editingTaskId = null;
    let myChart = null;

    async function fetchCategories() {
      try {
        const res = await fetch("/api/categories", {
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          }
        });
        if (res.ok) {
          categories = await res.json();
          populateCategoryFilters();
        }
      } catch (err) {
        console.error("Gagal mengambil kategori:", err);
      }
    }

    function populateCategoryFilters() {
      const filterCat = document.getElementById("filterCategory");
      const editCat = document.getElementById("editCategory");
      const listCat = document.getElementById("categoriesList");

      // Clear except first
      filterCat.innerHTML = '<option value="All">All Categories</option>';
      editCat.innerHTML = '<option value="">No Category</option>';
      listCat.innerHTML = '';

      categories.forEach(cat => {
        const opt = `<option value="${cat.id}">${cat.name}</option>`;
        filterCat.innerHTML += opt;
        editCat.innerHTML += opt;

        // Add to management list
        listCat.innerHTML += `
          <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" style="background-color: ${cat.color}"></span>
              <span class="font-medium">${cat.name}</span>
            </div>
            <button onclick="deleteCategory(${cat.id})" class="text-red-500 hover:text-red-700 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
          </div>
        `;
      });

      if (categories.length === 0) {
        listCat.innerHTML = '<p class="text-center text-gray-500 py-4 text-sm">No categories yet.</p>';
      }
    }

    async function addCategory() {
      const nameInput = document.getElementById("newCategoryName");
      const colorInput = document.getElementById("newCategoryColor");
      const name = nameInput.value.trim();
      const color = colorInput.value;

      if (!name) return alert("Category name is required");

      try {
        const res = await fetch("/api/categories", {
          method: "POST",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({ name, color })
        });

        if (res.ok) {
          nameInput.value = '';
          fetchCategories();
        }
      } catch (err) {
        alert("Failed to add category");
      }
    }

    async function deleteCategory(id) {
      if (!confirm("Delete this category? Tasks using this category will be set to 'No Category'.")) return;

      try {
        const res = await fetch(`/api/categories/${id}`, {
          method: "DELETE",
          headers: { "Authorization": `Bearer ${token}` }
        });

        if (res.ok) {
          fetchCategories();
          fetchTasks(); // Refresh tasks to update badges
        }
      } catch (err) {
        alert("Failed to delete category");
      }
    }

    /* --- CATEGORY MODAL LOGIC --- */
    const categoriesModal = document.getElementById("categoriesModal");
    function openCategoriesModal() {
      categoriesModal.classList.remove('hidden');
      requestAnimationFrame(() => {
        categoriesModal.setAttribute('data-state', 'open');
      });
    }
    function closeCategoriesModal() {
      categoriesModal.setAttribute('data-state', 'closed');
      setTimeout(() => {
        categoriesModal.classList.add('hidden');
      }, 300);
    }
    /* --- PROFILE MODAL LOGIC --- */
    const profileModal = document.getElementById("profileModal");
    function openProfileModal() {
      // Sync local storage data to form
      const user = JSON.parse(localStorage.getItem("user") || "{}");
      document.getElementById("profileName").value = user.name || "";
      document.getElementById("profileEmail").value = user.email || "";
      if (user.avatar) {
        document.getElementById("profileAvatarImg").src = user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar;
      }

      profileModal.classList.remove('hidden');
      requestAnimationFrame(() => {
        profileModal.setAttribute('data-state', 'open');
      });
    }
    function closeProfileModal() {
      profileModal.setAttribute('data-state', 'closed');
      setTimeout(() => {
        profileModal.classList.add('hidden');
      }, 300);
    }

    async function saveProfile() {
      const name = document.getElementById("profileName").value.trim();
      const email = document.getElementById("profileEmail").value.trim();

      if (!name || !email) return alert("Name and Email are required");

      try {
        const res = await fetch("/api/profile", {
          method: "PUT",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({ name, email })
        });
        const data = await res.json();
        if (res.ok) {
          localStorage.setItem("user", JSON.stringify(data.user));
          alert("Profile updated!");
        } else {
          alert(data.message || "Failed to update profile");
        }
      } catch (err) {
        alert("Error updating profile");
      }
    }

    async function savePassword() {
      const current_password = document.getElementById("currentPassword").value;
      const password = document.getElementById("newPassword").value;
      const password_confirmation = document.getElementById("confirmPassword").value;

      if (!current_password || !password) return alert("Both current and new password are required");
      if (password !== password_confirmation) return alert("Passwords do not match");

      try {
        const res = await fetch("/api/profile/password", {
          method: "PUT",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({ current_password, password, password_confirmation })
        });
        const data = await res.json();
        if (res.ok) {
          alert("Password updated!");
          document.getElementById("currentPassword").value = "";
          document.getElementById("newPassword").value = "";
          document.getElementById("confirmPassword").value = "";
        } else {
          alert(data.message || "Failed to update password");
        }
      } catch (err) {
        alert("Error updating password");
      }
    }

    async function uploadAvatar(e) {
      const file = e.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append("avatar", file);

      try {
        const res = await fetch("/api/profile/avatar", {
          method: "POST",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          },
          body: formData
        });
        const data = await res.json();
        if (res.ok) {
          localStorage.setItem("user", JSON.stringify(data.user));
          document.getElementById("profileAvatarImg").src = data.avatar_url;
          alert("Avatar updated!");
        } else {
          alert(data.message || "Failed to upload avatar");
        }
      } catch (err) {
        alert("Error uploading avatar");
      }
    }

    function showNotification(message, type = 'success') {
      const stack = document.getElementById("notificationStack");
      const id = Date.now();
      const colorClass = type === 'error' ? 'bg-red-500' : 'bg-green-500';
      const icon = type === 'error' ? '❌' : '✅';

      const toast = document.createElement("div");
      toast.id = `toast-${id}`;
      toast.className = `${colorClass} text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-in slide-in-from-right duration-300 pointer-events-auto cursor-pointer`;
      toast.innerHTML = `
        <span class="text-lg">${icon}</span>
        <span class="font-medium">${message}</span>
      `;
      toast.onclick = () => {
        toast.classList.add('animate-out', 'fade-out', 'slide-out-to-right');
        setTimeout(() => toast.remove(), 300);
      };

      stack.appendChild(toast);
      setTimeout(() => {
        if (toast.parentElement) toast.onclick();
      }, 5000);
    }

    async function requestNotificationPermission() {
      if (!("Notification" in window)) return;
      if (Notification.permission === "default") {
        await Notification.requestPermission();
      }
    }

    function sendBrowserNotification(title, body) {
      if (!("Notification" in window)) return;
      if (Notification.permission === "granted") {
        new Notification(title, {
          body: body,
          icon: '/favicon.ico' // Ensure favicon exists or use a generic icon URL
        });
      }
    }

    // =======================================================
    // 📊 CHART & STATS
    // =======================================================
    function renderStats() {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const completedCount = tasks.filter(t => Boolean(t.completed || t.status === 'completed')).length;
      const overdueCount = tasks.filter(t => {
        const isDone = Boolean(t.completed || t.status === 'completed');
        if (isDone || !t.deadline) return false;
        const d = new Date(t.deadline);
        d.setHours(0, 0, 0, 0);
        return d < today;
      }).length;
      const pendingCount = tasks.length - completedCount - overdueCount;
      const totalCount = tasks.length;

      // 1. Update Tally Text
      const tallyContainer = document.getElementById('tallyDisplay');
      tallyContainer.innerHTML = `
            <div class="flex flex-col items-center group">
                <span class="text-3xl font-bold text-gray-800 dark:text-white group-hover:scale-110 transition-transform">${totalCount}</span>
                <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">Total</span>
            </div>
            <div class="w-px bg-gray-200 dark:bg-gray-700 h-12 mx-2"></div>
            <div class="flex flex-col items-center group">
                <span class="text-3xl font-bold text-green-500 group-hover:scale-110 transition-transform">${completedCount}</span>
                <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">Done</span>
            </div>
            <div class="w-px bg-gray-200 dark:bg-gray-700 h-12 mx-2"></div>
            <div class="flex flex-col items-center group">
                <span class="text-3xl font-bold text-red-500 group-hover:scale-110 transition-transform">${overdueCount}</span>
                <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">Overdue</span>
            </div>
             <div class="w-px bg-gray-200 dark:bg-gray-700 h-12 mx-2"></div>
            <div class="flex flex-col items-center group">
                <span class="text-3xl font-bold text-blue-500 group-hover:scale-110 transition-transform">${pendingCount}</span>
                <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">Pending</span>
            </div>
        `;

      // Reminder Notification
      if (overdueCount > 0 && !window.overdueAlertShown) {
        showNotification(`You have ${overdueCount} overdue tasks!`, 'error');
        sendBrowserNotification("Task Reminder", `You have ${overdueCount} tasks that are overdue. Please check your dashboard!`);
        window.overdueAlertShown = true;
      }

      // 2. Update/Init Chart
      const ctx = document.getElementById('taskChart').getContext('2d');
      const isDark = document.documentElement.classList.contains("dark");
      const textColor = isDark ? '#e5e7eb' : '#374151';

      if (myChart) {
        myChart.data.datasets[0].data = [completedCount, overdueCount, pendingCount];
        // Ensure colors are correct (sometimes lost if config mutates deeply)
        updateChartTheme();
        myChart.update();
      } else {
        myChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Completed', 'Overdue', 'Pending'],
            datasets: [{
              data: [completedCount, overdueCount, pendingCount],
              backgroundColor: [
                '#10B981', // Emerald 500
                '#EF4444', // Red 500
                '#3B82F6'  // Blue 500
              ],
              hoverBackgroundColor: [
                '#059669',
                '#DC2626',
                '#2563EB'
              ],
              borderWidth: 0,
              hoverOffset: 10
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Donut thickness
            animation: {
              animateScale: true,
              animateRotate: true
            },
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: textColor,
                  padding: 20,
                  usePointStyle: true,
                  font: {
                    size: 12,
                    family: "'Inter', sans-serif"
                  }
                }
              },
              tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: 10,
                cornerRadius: 8,
                titleFont: { family: "'Inter', sans-serif" },
                bodyFont: { family: "'Inter', sans-serif" }
              }
            }
          }
        });
      }
    }

    // =======================================================
    // 💡 FUNGSI PEMBANTU DEADLINE
    // =======================================================
    function formatDeadlineStatus(deadline, isCompleted) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (!deadline) {
        return { text: 'No Deadline', class: 'bg-gray-100 dark:bg-gray-700/50 text-gray-500' };
      }

      if (isCompleted) {
        return { text: 'Completed', class: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' };
      }

      const deadlineDate = new Date(deadline);
      deadlineDate.setHours(0, 0, 0, 0);

      const diffTime = deadlineDate.getTime() - today.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays < 0) {
        const daysOverdue = Math.abs(diffDays);
        return { text: `Overdue ${daysOverdue}d`, class: 'bg-red-500 text-white shadow-md shadow-red-500/30' };
      } else if (diffDays === 0) {
        return { text: 'Due Today', class: 'bg-amber-500 text-white shadow-md shadow-amber-500/30' };
      } else if (diffDays <= 3) {
        return { text: `${diffDays} days left`, class: 'bg-orange-100 dark:bg-orange-900/40 text-orange-800 dark:text-orange-200' };
      } else {
        return { text: `${diffDays} days left`, class: 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200' };
      }
    }

    // Initialize deadline min dates
    function initDeadlineConstraints() {
      const todayStr = new Date().toISOString().split('T')[0];
      const editDeadlineInput = document.getElementById("editDeadline");
      if (editDeadlineInput) editDeadlineInput.min = todayStr;
    }
    initDeadlineConstraints();

    /* 📡 API CALLS */
    async function fetchTasks() {
      // Show loading state if needed, but here we just render
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
        renderStats();
        renderTasks();
      } catch (err) {
        console.error("Gagal mengambil data:", err);
        tasks = [];
        renderStats();
        renderTasks();
      }
    }

    async function deleteTask(id) {
      if (!confirm("Are you sure you want to delete this task?")) return;

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
      const taskIndex = tasks.findIndex(t => t.id == id);
      if (taskIndex === -1) return;

      const task = tasks[taskIndex];
      // Use both completed and status as a fallback for robustness
      const isCurrentlyDone = Boolean(task.completed || task.status === 'completed');
      const newStatus = !isCurrentlyDone;

      // --- OPTIMISTIC UPDATE ---
      // Temporarily update local state for instant feedback
      const originalValue = task.completed;
      const originalStatus = task.status;
      tasks[taskIndex].completed = newStatus;
      tasks[taskIndex].status = newStatus ? 'completed' : 'pending';
      renderTasks();
      renderStats();

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
            completed: newStatus,
            priority: (task.priority || 'medium').toLowerCase(),
            deadline: task.deadline ? task.deadline : null,
            category_id: task.category_id || null
          })
        });

        if (!res.ok) {
          throw new Error("Gagal update server");
        }

        // Refresh with real data from server
        const updatedTask = await res.json();
        tasks[taskIndex] = updatedTask;
        renderTasks();
        renderStats();

      } catch (err) {
        console.error(err);
        // ROLLBACK on error
        tasks[taskIndex].completed = originalValue;
        tasks[taskIndex].status = originalStatus;
        renderTasks();
        renderStats();
        alert("Gagal mengupdate status task");
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

    function openModal() {
      editModal.classList.remove('hidden');
      // Small delay to allow CSS transition to see the change
      requestAnimationFrame(() => {
        editModal.setAttribute('data-state', 'open');
      });
    }

    function closeModalLogic() {
      editModal.setAttribute('data-state', 'closed');
      setTimeout(() => {
        editModal.classList.add('hidden');
        editingTaskId = null;
      }, 300); // match transition duration
    }

    /* 🎨 RENDER */
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

      // 4. Filter Category
      if (document.getElementById("filterCategory").value !== "All") {
        const catId = document.getElementById("filterCategory").value;
        filtered = filtered.filter(t => String(t.category_id) === catId);
      }

      // 5. Sort
      if (sortBy.value === "deadline") {
        filtered.sort((a, b) => {
          const dateA = a.deadline ? new Date(a.deadline) : new Date(8640000000000000);
          const dateB = b.deadline ? new Date(b.deadline) : new Date(8640000000000000);
          return dateA - dateB;
        });
      } else if (sortBy.value === "priority") {
        const pVal = { high: 1, medium: 2, low: 3 };
        filtered.sort((a, b) => (pVal[(a.priority || '').toLowerCase()] || 99) - (pVal[(b.priority || '').toLowerCase()] || 99));
      } else if (sortBy.value === "az") {
        filtered.sort((a, b) => a.title.localeCompare(b.title));
      } else if (sortBy.value === "za") {
        filtered.sort((a, b) => b.title.localeCompare(a.title));
      }

      // Render HTML
      taskContainer.innerHTML = "";

      if (!filtered.length) {
        taskContainer.innerHTML = `
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                    <div class="text-6xl mb-4">🍃</div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-lg">No tasks found</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Try changing your filters or add a new task.</p>
                </div>`;
        return;
      }

      filtered.forEach(t => {
        const desc = t.description || "-";
        const deadline = t.deadline || null;
        // Use both as fallback
        const isDone = Boolean(t.completed || t.status === 'completed');
        const categoryName = t.category ? t.category.name : null;
        const categoryColor = t.category ? t.category.color : '#94a3b8';

        // Priority Colors
        let priorityClass = '';
        switch ((t.priority || '').toLowerCase()) {
          case 'high':
            priorityClass = 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800';
            break;
          case 'medium':
            priorityClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800';
            break;
          case 'low':
            priorityClass = 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800';
            break;
          default:
            priorityClass = 'bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600';
        }

        const deadlineStatus = formatDeadlineStatus(deadline, isDone);
        const cardOpacity = isDone ? 'opacity-75' : 'opacity-100';

        const categoryTag = categoryName ? `
          <span style="background-color: ${categoryColor}20; color: ${categoryColor}; border: 1px solid ${categoryColor}40;" 
                class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider shadow-sm">
            ${categoryName}
          </span>
        ` : '';

        taskContainer.innerHTML += `
        <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-800 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4 ${cardOpacity}">
          
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                ${categoryTag}
                <span class="${priorityClass} text-[10px] px-2 py-0.5 rounded-full uppercase font-bold tracking-wider shadow-sm">
                  ${t.priority || 'NORMAL'}
                </span>
                <span class="${deadlineStatus.class} text-[10px] px-2 py-0.5 rounded-full font-bold shadow-sm flex items-center gap-1">
                   ${deadlineStatus.text}
                </span>
            </div>

            <h3 class="font-bold text-lg leading-tight ${isDone ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-gray-100'} transition-colors">
              ${t.title}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">${desc}</p>
            ${deadline ? `
              <div class="mt-2 flex items-center gap-2 text-xs font-semibold ${isDone ? 'text-gray-400' : 'text-blue-600 dark:text-blue-400'}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>${new Date(deadline).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
              </div>
            ` : ''}
          </div>

          <div class="flex items-center gap-2 self-end sm:self-center opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button onclick="openEditModal(${t.id})" 
               title="Edit Task"
               class="p-2.5 bg-gray-50 dark:bg-gray-700 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-full transition-all shadow-sm">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            </button>
            
            <button onclick="toggleTask(${t.id})"
               title="${isDone ? 'Mark Undone' : 'Mark Done'}" 
               class="p-2.5 bg-gray-50 dark:bg-gray-700 ${isDone ? 'text-green-600' : 'text-gray-400 hover:text-green-600'} hover:bg-green-100 dark:hover:bg-green-900/50 rounded-full transition-all shadow-sm">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="${isDone ? 'currentColor' : 'none'}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </button>

            <button onclick="deleteTask(${t.id})" 
               title="Delete Task"
               class="p-2.5 bg-gray-50 dark:bg-gray-700 text-gray-400 hover:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-full transition-all shadow-sm">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
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
      document.getElementById("editCategory").value = task.category_id || '';

      openModal();
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
            category_id: document.getElementById("editCategory").value || null,
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

        closeModalLogic();
        fetchTasks();

      } catch (err) {
        alert("Error: " + err.message);
      } finally {
        btnSaveEdit.disabled = false;
        btnSaveEdit.textContent = "Save Changes";
      }
    }

    /* EVENTS */
    searchInput.oninput = renderTasks;
    filterPriority.onchange = renderTasks;
    filterStatus.onchange = renderTasks;
    sortBy.onchange = renderTasks;

    // EVENT MODAL
    document.getElementById("btnCancelEdit").onclick = closeModalLogic;
    btnSaveEdit.onclick = saveEdit;

    // Global functions (needed for onclick in HTML)
    window.toggleTask = toggleTask;
    window.deleteTask = deleteTask;
    window.openEditModal = openEditModal;
    window.deleteCategory = deleteCategory;
    window.openProfileModal = openProfileModal;

    // Category Management
    document.getElementById("manageCategoriesBtn").onclick = openCategoriesModal;
    document.getElementById("btnCloseCategories").onclick = closeCategoriesModal;
    document.getElementById("btnAddCategory").onclick = addCategory;

    // Profile Management
    document.getElementById("btnCloseProfile").onclick = closeProfileModal;
    document.getElementById("btnSaveProfile").onclick = saveProfile;
    document.getElementById("btnSavePassword").onclick = savePassword;
    document.getElementById("avatarInput").onchange = uploadAvatar;

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
    requestNotificationPermission();
    fetchCategories();
    fetchTasks();

    /* 🔎 ADDITIONAL FILTERS */
    document.getElementById("filterCategory").onchange = renderTasks;
  </script>

</body>

</html>