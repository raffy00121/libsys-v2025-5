<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Record;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Record::with('book')
            ->whereHas('book')
            ->orderBy('created_at', 'desc')
                ->paginate(10);

        return view('records.books.index', ['records' => $records]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('records.books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'accession-number' => 'required|string|max:255|unique:records,accession_number',
            'title' => 'required|string|max:500',
            'language' => 'required|string|max:100',
            'ddc-classification' => 'required|string|in:Applied Science,Arts,Fiction,General Works,History,Language,Literature,Philosophy,Pure Science,Religion,Social Science',
            'call-number' => 'string|max:50',
            'physical-location' => 'required|string|in:Circulation,Fiction,Filipiniana,General References,Graduate School,Reserve,PCAARRD,Vertical Files',
            'location-symbol' => 'string|max:10',
            'date-acquired' => 'required|date',
            'source' => 'required|string|in:Purchase,Donation,Exchange,Government Depository',
            'purchase-amount' => 'nullable|numeric|min:0',
            'acquisition-status' => 'required|string|in:Processing,Available,Pending Review',
            'additional-notes' => 'nullable|string|max:1000',
//        end of records part, start of book part
            'primary-author' => 'required|string|max:50',
            'publication-year' => 'required|integer|min:1000|max:' . (date('Y') + 10),
            'publisher' => 'required|string|max:255',
            'place-of-publication' => 'required|string|max:255',
            'isbn-issn' => 'nullable|string|max:50',
            'additional-authors' => 'nullable|string|max:500',
            'editor' => 'nullable|string|max:255',
            'series-title' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'cover-type' => 'required|string|in:Hardcover,Paperback,Spiral-bound,Ring-bound,Other',
            'book-cover-image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'table-of-contents' => 'nullable|string|max:2000',
            'summary-abstract' => 'nullable|string|max:1000',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle file upload if present
            $coverImagePath = null;
            if ($request->hasFile('book-cover-image')) {
                $coverImagePath = $request->file('book-cover-image')->store('book-covers', 'public');
            }

            // Create the book record
            $record = Record::create([
                'accession_number' => $request->input('accession-number'),
                'title' => $request->input('title'),
                'language' => $request->input('language'),
                'ddc_classification' => $request->input('ddc-classification'),
                'call_number' => $request->input('call-number'),
                'physical_location' => $request->input('physical-location'),
                'location_symbol' => $request->input('location-symbol'),
                'date_acquired' => $request->input('date-acquired'),
                'source' => $request->input('source'),
                'purchase_amount' => $request->input('purchase-amount'),
                'acquisition_status' => $request->input('acquisition-status'),
                'additional_notes' => $request->input('additional-notes'),
            ]);

            // Create the associated book with additional details
            $record->book()->create([
                'primary_author' => $request->input('primary-author'),
                'publication_year' => $request->input('publication-year'),
                'publisher' => $request->input('publisher'),
                'place_of_publication' => $request->input('place-of-publication'),
                'isbn_issn' => $request->input('isbn-issn'),
                'series_title' => $request->input('series-title'),
                'edition' => $request->input('edition'),
                'cover_type' => $request->input('cover-type'),
                'book_cover_image' => $coverImagePath, // Use the uploaded image path
                'table_of_contents' => $request->input('table-of-contents'),
                'summary_abstract' => $request->input('summary-abstract'),
            ]);

            // Redirect with success message
            return redirect()->route('books.index')
                ->with('success', 'Book "' . $record->title . '" created successfully!');

        } catch (\Exception $e) {
            $errorMessage = app()->environment('local') // will only show this in local
                ? 'Failed to create book: ' . $e->getMessage()
                : 'Failed to create book. Please try again.';

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Record $record)
    {
        return view('records.books.show', [
            'record' => $record,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Record $record)
    {
        return view('records.books.edit', [
            'record' => $record,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Record $record): RedirectResponse
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'accession-number' => 'required|string|max:255|unique:records,accession_number,' . $record->id,
            'title' => 'required|string|max:500',
            'language' => 'required|string|max:100',
            'ddc-classification' => 'required|string|in:Applied Science,Arts,Fiction,General Works,History,Language,Literature,Philosophy,Pure Science,Religion,Social Science',
            'call-number' => 'string|max:50',
            'physical-location' => 'required|string|in:Circulation,Fiction,Filipiniana,General References,Graduate School,Reserve,PCAARRD,Vertical Files',
            'location-symbol' => 'string|max:10',
            'date-acquired' => 'required|date',
            'source' => 'required|string|in:Purchase,Donation,Exchange,Government Depository',
            'purchase-amount' => 'nullable|numeric|min:0',
            'acquisition-status' => 'required|string|in:Processing,Available,Pending Review',
            'additional-notes' => 'nullable|string|max:1000',
            // end of records part, start of book part
            'primary-author' => 'required|string|max:255',
            'publication-year' => 'required|integer|min:1000|max:' . (date('Y') + 10),
            'publisher' => 'required|string|max:255',
            'place-of-publication' => 'required|string|max:255',
            'isbn-issn' => 'nullable|string|max:50',
            'additional-authors' => 'nullable|string|max:500',
            'editor' => 'nullable|string|max:255',
            'series-title' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'cover-type' => 'required|string|in:Hardcover,Paperback,Spiral-bound,Ring-bound,Other',
            'book-cover-image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'table-of-contents' => 'nullable|string|max:2000',
            'summary-abstract' => 'nullable|string|max:1000',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle file upload if present
            $coverImagePath = $record->book->book_cover_image;
            if ($request->hasFile('book-cover-image')) {
                // Delete the old image if it exists
                if ($coverImagePath && Storage::disk('public')->exists($coverImagePath)) {
                    Storage::disk('public')->delete($coverImagePath);
                }
                $coverImagePath = $request->file('book-cover-image')->store('book-covers', 'public');
            }

            // Update the book record
            $record->update([
                'accession_number' => $request->input('accession-number'),
                'title' => $request->input('title'),
                'language' => $request->input('language'),
                'ddc_classification' => $request->input('ddc-classification'),
                'call_number' => $request->input('call-number'),
                'physical_location' => $request->input('physical-location'),
                'location_symbol' => $request->input('location-symbol'),
                'date_acquired' => $request->input('date-acquired'),
                'source' => $request->input('source'),
                'purchase_amount' => $request->input('purchase-amount'),
                'acquisition_status' => $request->input('acquisition-status'),
                'additional_notes' => $request->input('additional-notes'),
            ]);

            // Update the associated book with additional details
            $record->book()->update([
                'primary_author' => $request->input('primary-author'),
                'publication_year' => $request->input('publication-year'),
                'publisher' => $request->input('publisher'),
                'place_of_publication' => $request->input('place-of-publication'),
                'isbn_issn' => $request->input('isbn-issn'),
                'series_title' => $request->input('series-title'),
                'edition' => $request->input('edition'),
                'cover_type' => $request->input('cover-type'),
                'book_cover_image' => $coverImagePath,
                'table_of_contents' => $request->input('table-of-contents'),
                'summary_abstract' => $request->input('summary-abstract'),
            ]);

            // Redirect with success message
            return redirect()->route('books.index')
                ->with('success', 'Book "' . $record->title . '" updated successfully!');

        } catch (\Exception $e) {
            $errorMessage = app()->environment('local') // will only show this in local
                ? 'Failed to update book: ' . $e->getMessage()
                : 'Failed to update book. Please try again.';

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Record $record)
    {
        try {

            $record->book->delete();
            $record->delete();

            return redirect()->route('books.index')
                ->with('success', 'Book deleted successfully!');
        } catch (\Exception $e) {
            $errorMessage = app()->environment('local') // will only show this in local
                ? 'Failed to delete book: ' . $e->getMessage()
                : 'Failed to delete book. Please try again.';

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
}
