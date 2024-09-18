<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permissions / Create
            </h2>
            <a href="{{ route('permissions.index') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="createPermissionForm" action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="name" class="text-lb font-medium">Name</label>
                            <div class="my-3">
                                <input id="name" name="name" value="{{ old('name') }}" placeholder="Enter Name" type="text" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('name')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#createPermissionForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            let form = $(this);
            let url = form.attr('action');
            let method = form.attr('method');
            let formData = form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = "{{ route('permissions.index') }}"; // Redirect to index page on success
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $(`[name="${key}"]`).siblings('p').text(errors[key][0]);
                        }
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endsection
