<div>

    <x-flash-messenger/>

    <div class="flex justify-between items-center w-full sm:w-auto">
        <!-- Search bar -->
        <div class="flex-1 sm:flex-none">
            <input
                type="text"
                wire:model.live="search"
                placeholder="acc no., title, ddc class, author, year"
                class="block w-full sm:w-74 rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-900 sm:text-sm sm:leading-6"
            >
        </div>
        <div class="flex gap-2">
            <a href="{{ route('books.create') }}">
                <button
                    type="button"
                    class="block rounded-md bg-red-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-900"
                >
                    Add book
                </button>
            </a>
            <button
                wire:click="openModal"
                type="button"
                class="block rounded-md bg-red-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-900"
            >
                Import Books
            </button>
        </div>
    </div>

    <div id="books-table" class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8">Accession no.</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">DDC/LC Classification</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Author</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Year of Publication</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 lg:pr-8">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($records as $record)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">{{ $record->accession_number }}</td>
                            <td class="px-3 py-4 text-sm text-gray-600 max-w-md truncate">{{ $record->title }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $record->book->ddcClassification->name ?? $record->lc_classification }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                @if (is_array($record->book->authors))
                                    {{ implode(', ', $record->book->authors) }}
                                @else
                                    {{ $record->book->authors ?? 'Not specified' }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $record->book->publication_year ?? 'Not specified' }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 lg:pr-8">
                                <a href="{{ route('books.show', $record) }}" class="text-red-900 hover:text-indigo-900">View all details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-sm text-gray-600">No records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="py-8">
        {{ $records->links() }}
    </div>

    <x-modal>
        hi I'm in the modal
        <br>

        diri mo mag import ug books
        <br>
        naay modal diri kay i orient sa tika na dapat csv then ang mga columns nimo is dapat ing-ani ug format
        <form wire:submit.prevent="submit" enctype="multipart/form-data">
            <div class="mt-2">
                <input
                    type="file"
                    name="import_csv"
                    wire:model.live="import_csv"
                    accept=".csv"
                    required
                >
                @error('import_csv')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button
                    type="submit"
                    @disabled(!$import_csv)
                    @class([
                        'block rounded-md px-3 py-2 text-center text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                        'bg-red-900 text-white hover:bg-red-800 focus-visible:outline-red-900' => $import_csv,
                        'bg-gray-300 text-gray-500 cursor-not-allowed' => !$import_csv
                    ])
                >
                    <span wire:loading wire:target="submit">Importing...</span>
                    <span wire:loading.remove wire:target="submit">Proceed Import</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Loading Overlay -->
    <div
        wire:loading
        wire:target="submit"
        class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="flex flex-col items-center">
            <svg
                class="animate-spin h-10 w-10 text-red-900"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <span class="mt-2 text-white text-lg">Importing books, please wait...</span>
        </div>
    </div>
</div>
