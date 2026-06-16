<template>
  <div>
    <h1 class="text-2xl font-bold mb-2">Admin Panel</h1>
    <p class="text-gray-500 mb-8">Manage your e-commerce platform.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <router-link to="/admin/categories" class="admin-card group">
        <h2 class="admin-card-title">Categories</h2>
        <p class="admin-card-desc">Create, edit, and delete product categories</p>
      </router-link>

      <router-link to="/admin/products" class="admin-card group">
        <h2 class="admin-card-title">Products</h2>
        <p class="admin-card-desc">View and moderate all product listings</p>
      </router-link>

      <router-link to="/admin/users" class="admin-card group">
        <h2 class="admin-card-title">Users</h2>
        <p class="admin-card-desc">Manage user accounts and roles</p>
      </router-link>
    </div>
  </div>
</template>

<style scoped>
@reference "../../../css/app.css";

.admin-card {
  @apply bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow;
}

.admin-card-title {
  @apply text-lg font-semibold text-gray-900 group-hover:text-blue-600;
}

.admin-card-desc {
  @apply text-sm text-gray-500 mt-1;
}
</style>
