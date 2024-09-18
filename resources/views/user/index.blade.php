<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... other head elements ... -->

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Users
                </h2>
                <a href="{{ route('user.create') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-3">
                    Create
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <x-message></x-message>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 datatable">
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
                            @forelse ($users as $user)
                                <tr class="border-b border-gray-200">
                                    <td class="px-6 py-3 text-left">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-3 text-left">{{ $user->name }}</td>
                                    <td class="px-6 py-3 text-left">{{ $user->email }}</td>
                                    <td class="px-6 py-3 text-left">
                                        @if ($user->roles->isNotEmpty())
                                            {{ $user->roles->pluck('name')->implode(', ') }}
                                        @else
                                            No Role
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-center space-x-2">
                                        <a href="{{ route('user.edit', $user->id) }}"
                                           class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-600 transition">
                                           Edit
                                        </a>

                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 text-sm rounded-md text-white px-3 py-2 hover:bg-red-500 transition"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-3 text-center">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="my-3">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </x-app-layout>

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            });
        });
    </script>
</body>
</html>
