<template>
  <div>
    <h1 class="text-2xl font-bold mb-2">Admin Panel</h1>
    <p class="text-gray-500 mb-8">Manage your e-commerce platform.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <router-link to="/admin/categories"
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group">
        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">Categories</h2>
        <p class="text-sm text-gray-500 mt-1">Create, edit, and delete product categories</p>
      </router-link>

      <router-link to="/admin/products"
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group">
        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">Products</h2>
        <p class="text-sm text-gray-500 mt-1">View and moderate all product listings</p>
      </router-link>

      <router-link to="/admin/users"
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow group">
        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">Users</h2>
        <p class="text-sm text-gray-500 mt-1">Manage user accounts and roles</p>
      </router-link>
    </div>
  </div>
</template>
