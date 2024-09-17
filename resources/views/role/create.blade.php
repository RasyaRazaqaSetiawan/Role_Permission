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

                            <div id="permissionsList" class="mb-3">
                                <label class="font-medium text-base">Select Permissions</label>
                                <div class="flex flex-wrap gap-4">
                                    @foreach ($permissions as $permission)
                                        <div class="flex flex-col items-start p-3 border rounded-lg">
                                            <!-- Permission Checkbox -->
                                            <div class="flex items-center">
                                                <input type="checkbox" id="permission{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" class="rounded permission-checkbox">
                                                <label for="permission{{ $permission->id }}" class="ml-2">{{ $permission->name }}</label>
                                            </div>
                                            <!-- Hak Akses Checkbox -->
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach ($hakAkses as $hak)
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="hakAkses{{ $permission->id }}_{{ $hak->id }}" name="hakakses[{{ $permission->id }}][]" value="{{ $hak->id }}" class="rounded hakAksesCheckbox permission-{{ $permission->id }}">
                                                        <label for="hakAkses{{ $permission->id }}_{{ $hak->id }}" class="ml-2">{{ $hak->name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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

                // Check if at least one permission is selected
                if ($('input[name="permissions[]"]:checked').length === 0) {
                    $('#formMessage').text('Please select at least one permission.').addClass('text-red-500');
                    return;
                }

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('role.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $('#formMessage').text(response.message).addClass('text-green-500');
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
