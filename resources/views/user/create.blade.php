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
                    <!-- Form for creating role -->
                    <form id="roleCreateForm" method="POST">
                        @csrf
                        <!-- Role Name Input -->
                        <div>
                            <label for="roleName" class="text-lg font-medium">Name</label>
                            <div class="my-3">
                                <input type="text" id="roleName" name="name" class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="Role Name">
                                <p id="nameError" class="text-red-400 font-medium"></p>
                            </div>

                            <!-- Role Dropdown -->
                            <div class="my-3">
                                <label for="roleDropdown" class="text-lg font-medium">Select Role</label>
                                <select id="roleDropdown" name="role" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                    <option value="" disabled selected>Select a role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <p id="roleError" class="text-red-400 font-medium"></p>
                            </div>

                            <!-- Submit Button -->
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
            // Handle form submission via AJAX
            $('#roleCreateForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('role.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $('#formMessage').text(response.message).addClass('text-green-500');
                        $('#nameError').text(''); // Clear previous errors
                        $('#roleError').text(''); // Clear previous errors
                        $('#roleCreateForm')[0].reset(); // Reset the form
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '';
                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                        if (errors.role) {
                            $('#roleError').text(errors.role[0]);
                        }
                        $('#formMessage').html(errorHtml).addClass('text-red-500');
                    }
                });
            });
        });
    </script>
</x-app-layout>
