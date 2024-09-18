<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User / Edit
            </h2>
            <a href="{{ route('user.index') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Form for editing user -->
                    <form id="userEditForm">
                        @csrf
                        @method('PUT')
                        <!-- Name Input -->
                        <div class="my-3">
                            <label for="name" class="text-lg font-medium">Name</label>
                            <input type="text" id="name" name="name"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="User Name" value="{{ old('name', $user->name) }}">
                            <p id="nameError" class="text-red-400 font-medium"></p>
                        </div>

                        <!-- Email Input -->
                        <div class="my-3">
                            <label for="email" class="text-lg font-medium">Email</label>
                            <input type="email" id="email" name="email"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="User Email" value="{{ old('email', $user->email) }}">
                            <p id="emailError" class="text-red-400 font-medium"></p>
                        </div>

                        <!-- Password Input -->
                        <div class="my-3">
                            <label for="password" class="text-lg font-medium">Password</label>
                            <input type="password" id="password" name="password"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="Password">
                            <p id="passwordError" class="text-red-400 font-medium"></p>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="my-3">
                            <label for="password_confirmation" class="text-lg font-medium">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="Confirm Password">
                        </div>

                        <!-- Role Dropdown -->
                        <div class="my-3">
                            <label for="role" class="text-lg font-medium">Select Role</label>
                            <select id="role" name="role" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                <option value="" disabled>Select a role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="roleError" class="text-red-400 font-medium"></p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">Update</button>
                        <p id="formMessage" class="mt-3 font-medium"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and AJAX logic -->
    <script>
        $(document).ready(function() {
            $('#userEditForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('user.update', $user->id) }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $('#formMessage').text(response.message).removeClass('text-red-500')
                            .addClass('text-green-500');
                        $('#nameError, #emailError, #passwordError, #roleError').text(''); // Clear previous errors
                        $('#userEditForm')[0].reset(); // Reset the form
                        window.location.href = "{{ route('user.index') }}"; // Redirect to index page
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        $('#nameError, #emailError, #passwordError, #roleError').text(''); // Clear previous errors

                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                        if (errors.email) {
                            $('#emailError').text(errors.email[0]);
                        }
                        if (errors.password) {
                            $('#passwordError').text(errors.password[0]);
                        }
                        if (errors.role) {
                            $('#roleError').text(errors.role[0]);
                        }
                        $('#formMessage').text('Please fix the errors').removeClass('text-green-500').addClass('text-red-500');
                    }
                });
            });
        });
    </script>
</x-app-layout>
