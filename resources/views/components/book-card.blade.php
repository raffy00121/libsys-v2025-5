<div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition">
    <img class="w-full h-48 object-cover" src="{{ $book->cover_url }}" alt="Book cover">
    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-800">{{ $book->title }}</h3>
        <p class="text-sm text-gray-600 mb-2">{{ $book->author }}</p>
        <p class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($book->description, 100) }}</p>
        <a href="#" class="mt-3 inline-block text-blue-600 hover:underline text-sm">View Details</a>
    </div>
</div>
