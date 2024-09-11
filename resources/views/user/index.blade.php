<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User
            </h2>
            <a href="{{ route('user.create') }}"
                class="bg-slate-700 text-sm rounded-md text-white px-3 py-3">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>

            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left" width="60">#</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Role Name</th>
                            <th class="px-6 py-3 text-center" width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if ($user->isNotEmpty())
                            @foreach ($user as $data)
                                <tr class="border-b border-gray-200">
                                    <td class="px-6 py-3 text-left">{{ $data->id }}</td>
                                    <td class="px-6 py-3 text-left">{{ $data->name }}</td>
                                    <td class="px-6 py-3 text-left">{{ $data->email }}</td>
                                    <td class="px-6 py-3 text-left">
                                        @if ($data->roles->isNotEmpty())
                                            {{ $data->roles->pluck('name')->implode(', ') }}
                                        @else
                                            No Role
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-center space-x-2">
                                        <a href="{{ route('user.edit', $data->id) }}"
                                            class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-600 transition">Edit</a>

                                        <form action="{{ route('user.destroy', $data->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 text-sm rounded-md text-white px-3 py-2 hover:bg-red-500 transition"
                                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-center">No users found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="my-3">
                {{ $user->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</x-app-layout>
