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
                    <form action="{{ route('role.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="roleName" class="text-lg font-medium">Name</label>
                            <div class="my-3">
                                <input id="roleName" value="{{ old('name') }}" name="name"
                                    placeholder="Enter Name" type="text"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('name')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-4 gap-4 mb-3">
                                @if ($permissions->isNotEmpty())
                                    @foreach ($permissions as $permission)
                                        <div class="mt-3 flex items-center">
                                            <input type="checkbox" id="permission{{ $permission->id }}"
                                                name="permissions[]" value="{{ $permission->id }}" class="rounded">
                                            <label for="permission{{ $permission->id }}" class="ml-2">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
