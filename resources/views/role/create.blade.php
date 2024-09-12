<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles / Create
            </h2>
            <a href="{{ route('role.index') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="roleCreateForm" method="POST">
                        @csrf
                        <div>
                            <label for="roleName" class="text-lg font-medium">Name</label>
                            <div class="my-3">
                                <input id="roleName" name="name" type="text" placeholder="Enter Name"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                <p id="nameError" class="text-red-400 font-medium"></p>
                            </div>

                            <div id="permissionsList" class="grid grid-cols-4 gap-4 mb-3">
                                <!-- Permissions list will be loaded here -->
                            </div>

                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">
                                Submit
                            </button>
                            <p id="formMessage" class="mt-3 font-medium"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and AJAX logic -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load permissions via AJAX
            function loadPermissions() {
                $.ajax({
                    url: "{{ route('permissions.index') }}",
                    method: "GET",
                    success: function(data) {
                        let permissionsHtml = '';
                        $.each(data, function(index, permission) {
                            permissionsHtml += `
                                <div class="mt-3 flex items-center">
                                    <input type="checkbox" id="permission${permission.id}" name="permissions[]" value="${permission.id}" class="rounded">
                                    <label for="permission${permission.id}" class="ml-2">${permission.name}</label>
                                </div>
                            `;
                        });
                        $('#permissionsList').html(permissionsHtml);
                    },
                    error: function(xhr) {
                        console.log('Failed to load permissions:', xhr);
                    }
                });
            }

            // Load permissions when page loads
            loadPermissions();

            // Handle form submission via AJAX
            $('#roleCreateForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('role.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $('#formMessage').text('Role created successfully!').addClass('text-green-500');
                        $('#nameError').text(''); // Clear previous errors
                        $('#roleCreateForm')[0].reset(); // Reset the form
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '';
                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                        if (errors.permissions) {
                            errorHtml += errors.permissions.join(', ');
                        }
                        $('#formMessage').html(errorHtml).addClass('text-red-500');
                    }
                });
            });
        });
    </script>
</x-app-layout>
