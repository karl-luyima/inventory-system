@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', '👥 User Management')

@section('content')
<div class="p-8 space-y-6">

    {{-- User Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Total Users</h2>
            <p id="totalUsers" class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Admins</h2>
            <p id="adminCount" class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Clerks</h2>
            <p id="clerkCount" class="text-3xl font-bold text-purple-600 mt-2">0</p>
        </div>
    </div>

    {{-- User Table --}}
    <div class="bg-white p-6 rounded-xl shadow-md mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 All Users</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="userTable"></tbody>
        </table>
    </div>

</div>

<script>
    function fetchUserData() {
        fetch("{{ route('admin.users.data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalUsers').innerText = data.totalUsers;
                document.getElementById('adminCount').innerText = data.adminCount;
                document.getElementById('clerkCount').innerText = data.clerkCount;

                let table = document.getElementById('userTable');
                table.innerHTML = "";
                data.users.forEach(user => {
                    let row = `<tr class="border-b">
                        <td class="p-3">${user.name}</td>
                        <td class="p-3">${user.email}</td>
                        <td class="p-3">${user.role}</td>
                        <td class="p-3">
                            <a href="/admin/users/edit/${user.id}" class="text-yellow-600 hover:underline">✎ Edit</a>
                            <a href="/admin/users/delete/${user.id}" class="text-red-600 hover:underline ml-2">✖ Delete</a>
                        </td>
                    </tr>`;
                    table.innerHTML += row;
                });
            })
            .catch(err => console.error("Error:", err));
    }

    fetchUserData();
    setInterval(fetchUserData, 10000);
</script>
@endsection
