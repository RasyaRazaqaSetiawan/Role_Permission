<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role
            </h2>
            <a href="{{ route('role.create') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>

            <div class="overflow-x-auto bg-white shadow-md sm:rounded-lg">
                <table class="w-full border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Permissions</th>
                            <th class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $data)
                        <tr class="border-b" id="permissionRow{{  $loop->index + 1 }}">
                                <td class="px-6 py-3">{{ $loop->index + 1 }}</td>
                                <td class="px-6 py-3">{{ $data->name }}</td>
                                <td class="px-6 py-3">{{ implode(', ', $data->permissions->pluck('name')->toArray()) }}</td>
                                <td class="px-6 py-3 text-center space-x-2">
                                    <a href="{{ route('role.edit', $data->id) }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-600 transition">Edit</a>

                                    <form action="{{ route('role.destroy', $data->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-sm rounded-md text-white px-3 py-2 hover:bg-red-500 transition" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-center">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="my-3">
                {{ $roles->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</x-app-layout>
