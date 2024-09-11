<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permissions / Create
            </h2>
            <a href="{{ route('permissions.index') }}"
                class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="createPermissionForm">
                        @csrf
                        <div>
                            <label for="" class="text-lb font-medium">Name</label>
                            <div class="my-3">
                                <input id="name" name="name" placeholder="Enter Name"
                                    type="text" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                <p id="nameError" class="text-red-400 font-medium"></p>
                            </div>
                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#createPermissionForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('permissions.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Handle success, e.g., redirect or show a message
                            window.location.href = "{{ route('permissions.index') }}";
                        }
                    },
                    error: function(response) {
                        // Handle validation errors
                        var errors = response.responseJSON.errors;
                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                    }
                });
            });
        });
    </script>
</x-app-layout>
