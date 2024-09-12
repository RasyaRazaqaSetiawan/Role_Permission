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
                                <select id="roleName" name="name" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p id="nameError" class="text-red-400 font-medium"></p>
                            </div>

                            <div id="permissionsList" class="mb-3">
                                <label class="font-medium text-base">Select Permissions *</label>
                                @foreach ($permissions as $permission)
                                    <div class="mt-3">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="permission{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" class="rounded permission-checkbox">
                                            <label for="permission{{ $permission->id }}" class="ml-2">{{ $permission->name }}</label>
                                        </div>
                                        @foreach ($hakAkses as $hak)
                                            <div class="ml-6 mt-2 flex items-center">
                                                <input type="checkbox" id="hakAkses{{ $permission->id }}_{{ $hak->id }}" name="hakakses[{{ $permission->id }}][]" value="{{ $hak->id }}" class="rounded hakAksesCheckbox permission-{{ $permission->id }}">
                                                <label for="hakAkses{{ $permission->id }}_{{ $hak->id }}" class="ml-2 mr-1">{{ $hak->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
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

            // Handle the select all hakAkses checkboxes functionality
            $('#permissionsList').on('change', '.permission-checkbox', function() {
                var permissionId = $(this).attr('id').split('permission')[1];
                if ($(this).is(':checked')) {
                    // Check all hakAkses checkboxes for this permission
                    $(`.permission-${permissionId}`).prop('checked', true);
                } else {
                    // Uncheck all hakAkses checkboxes for this permission
                    $(`.permission-${permissionId}`).prop('checked', false);
                }
            });
        });
    </script>
</x-app-layout>
