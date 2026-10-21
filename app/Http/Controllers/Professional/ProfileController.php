<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Profile;
use App\Traits\ImageUploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use ImageUploadTraits;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $professionalId = Auth::guard('professional')->id();
        $profiles = Profile::where('professional_id', $professionalId)->with('professional')->get();
        return view('professional.profile.show-profile', compact('profiles'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */

    // public function store(Request $request, string $id)
    // {

    //     $data = $request->validate([
    //         'firstName' => 'required|string',
    //         'lastName' => 'required|string',
    //         'email' => 'required|email',
    //         'phone' => 'nullable|string',
    //         'specialization' => 'nullable|string',
    //         'experience' => 'nullable|string',
    //         'startingPrice' => 'nullable|numeric',
    //         'address' => 'nullable|string',
    //         'education' => 'nullable|string',
    //         'comments' => 'nullable|string',
    //         'bio' => 'nullable|string',
    //         'photo' => 'nullable|image',
    //         'gallery.*' => 'nullable|image',
    //         'qualificationDocument' => 'nullable|file',
    //         'aadhaarCard' => 'nullable|file',
    //         'panCard' => 'nullable|file',
    //     ]);

    //     $profile = Profile::findOrFail($id);
    //     $professionalId = Auth::guard('professional')->id();

    //     // Delete & update photo
    //     if ($request->hasFile('photo') && $profile->photo) {
    //         Storage::disk('public')->delete($profile->photo);
    //         $profile->photo = $this->uploadImage($request, 'photo', 'uploads/profiles/photo');
    //     }

    //     // Delete & update gallery
    //     if ($request->hasFile('gallery')) {
    //         if ($profile->gallery) {
    //             foreach (json_decode($profile->gallery, true) as $img) {
    //                 Storage::disk('public')->delete($img);
    //             }
    //         }
    //         $profile->gallery = json_encode($this->uploadMultipleImage($request, 'gallery', 'uploads/profiles/gallery'));
    //     }

    //     // Delete & update qualification document
    //     if ($request->hasFile('qualificationDocument') && $profile->qualification_document) {
    //         Storage::disk('public')->delete($profile->qualification_document);
    //         $profile->qualification_document = $this->uploadImage($request, 'qualificationDocument', 'uploads/profiles/documents');
    //     }

    //     // Delete & update aadhaar
    //     if ($request->hasFile('aadhaarCard') && $profile->aadhaar_card) {
    //         Storage::disk('public')->delete($profile->aadhaar_card);
    //         $profile->aadhaar_card = $this->uploadImage($request, 'aadhaarCard', 'uploads/profiles/identity');
    //     }

    //     // Delete & update pan
    //     if ($request->hasFile('panCard') && $profile->pan_card) {
    //         Storage::disk('public')->delete($profile->pan_card);
    //         $profile->pan_card = $this->uploadImage($request, 'panCard', 'uploads/profiles/identity');
    //     }

    //     // Update other fields
    //     $profile->professional_id = $professionalId;
    //     $profile->first_name = $data['firstName'];
    //     $profile->last_name = $data['lastName'];
    //     $profile->email = $data['email'];
    //     $profile->phone = $data['phone'] ?? null;
    //     $profile->specialization = $data['specialization'] ?? null;
    //     $profile->experience = $data['experience'] ?? null;
    //     $profile->starting_price = $data['startingPrice'] ?? null;
    //     $profile->address = $data['address'] ?? null;
    //     $profile->education = $data['education'] ?? null;
    //     $profile->comments = $data['comments'] ?? null;
    //     $profile->bio = $data['bio'] ?? null;
    //     $profile->save();

    //     // Update Professional name also
    //     $professional = Professional::findOrFail($professionalId);
    //     $professional->name = $data['firstName'] . ' ' . $data['lastName'];
    //     $professional->save();

    //     return response()->json(['success' => true, 'message' => 'Profile and name updated successfully!']);
    // }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $profile = Profile::where('professional_id', Auth::guard('professional')->id())
            ->findOrFail($id);

        return view('professional.profile.add-profile', compact('profile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $professionalId = Auth::guard('professional')->id();
        $profile = Profile::where('professional_id', $professionalId)->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('professionals', 'email')->ignore($professionalId)],
            'phone' => 'nullable|string|max:30',
            'specialization' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'startingPrice' => 'nullable|string|regex:/^(\d+(\.\d{1,2})?(-\d+(\.\d{1,2})?)?)$/',
            'address' => 'nullable|string|max:1000',
            'education' => 'nullable|string|max:5000',
            'comments' => 'nullable|string|max:5000',
            'bio' => 'nullable|string|max:5000',
            'gst_number' => 'nullable|string|max:15',
            'gst_address' => 'nullable|string|max:1000',
            'gst_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo' => 'nullable|image|max:5120',
            'cropped_photo' => 'nullable|string|max:12000000',
            'gallery' => 'nullable|array|max:20',
            'gallery.*' => 'nullable|image|max:5120',
            'deleted_images' => 'nullable|json',
            'qualificationDocument' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'idProofDocument' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'aadhaarCard' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'panCard' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            // Bank account details
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:11',
            'account_type' => 'nullable|in:savings,current',
            'bank_branch' => 'nullable|string|max:255',
            'bank_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filesToDelete = [];

        // Handle profile photo update
        if ($request->hasFile('photo')) {
            $oldPhoto = $profile->photo;
            $newPhoto = $this->uploadImage($request, 'photo', 'uploads/profiles/photo');
            if ($newPhoto) {
                $profile->photo = $newPhoto;
                if ($oldPhoto) {
                    $filesToDelete[] = $oldPhoto;
                }
            }
        } elseif ($request->filled('cropped_photo')) {
            // Handle cropped photo as base64 data
            try {
                $oldPhoto = $profile->photo;
                $profile->photo = $this->uploadBase64Image(
                    $request->cropped_photo,
                    'uploads/profiles/photo'
                );
                if ($oldPhoto) {
                    $filesToDelete[] = $oldPhoto;
                }
            } catch (\Exception $e) {
                Log::error('Base64 image upload failed: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Photo upload failed. Please try again.'], 500);
            }
        }

        // Enhanced gallery handling - preserve existing images and handle deletions
        $currentGallery = $profile->gallery ? json_decode($profile->gallery, true) : [];
        
        // Ensure $currentGallery is always an array (in case json_decode returns null)
        if (!is_array($currentGallery)) {
            $currentGallery = [];
        }
        
        // Handle image deletions using the deleted_images field
        if ($request->filled('deleted_images')) {
            $deletedImages = json_decode($request->deleted_images, true);
            
            if (is_array($deletedImages) && count($deletedImages) > 0) {
                foreach ($deletedImages as $imagePath) {
                    // Find the image in the current gallery
                    $key = array_search($imagePath, $currentGallery);
                    
                    if ($key !== false) {
                        $filesToDelete[] = $imagePath;
                        // Remove from the gallery array
                        unset($currentGallery[$key]);
                    }
                }
                
                // Reindex array to avoid issues with JSON encoding
                $currentGallery = array_values($currentGallery);
            }
        }
        
        // Add new gallery images
        if ($request->hasFile('gallery')) {
            $newImages = $this->uploadMultipleImage($request, 'gallery', 'uploads/profiles/gallery');
            
            // Ensure both arrays are valid before merging
            if (is_array($newImages) && is_array($currentGallery)) {
                $currentGallery = array_merge($currentGallery, $newImages);
            } elseif (is_array($newImages)) {
                // If currentGallery is not an array, use only new images
                $currentGallery = $newImages;
            }
        }
        
        $encodedGallery = json_encode($currentGallery);
        if ($encodedGallery === false) {
            Log::error('Gallery JSON encoding failed.', [
                'profile_id' => $profile->id,
                'json_error' => json_last_error_msg(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'The gallery could not be saved. Please try again.',
            ], 500);
        }
        $profile->gallery = $encodedGallery;

        // Handle document uploads
        if ($request->hasFile('qualificationDocument')) {
            $oldDocument = $profile->qualification_document;
            $profile->qualification_document = $this->uploadImage($request, 'qualificationDocument', 'uploads/profiles/documents');
            if ($oldDocument) {
                $filesToDelete[] = $oldDocument;
            }
        }

        if ($request->hasFile('idProofDocument')) {
            $oldDocument = $profile->id_proof_document;
            $profile->id_proof_document = $this->uploadImage($request, 'idProofDocument', 'uploads/profiles/identity');
            if ($oldDocument) {
                $filesToDelete[] = $oldDocument;
            }
        }

        if ($request->hasFile('gst_certificate')) {
            $oldDocument = $profile->gst_certificate;
            $profile->gst_certificate = $this->uploadImage($request, 'gst_certificate', 'uploads/profiles/gst');
            if ($oldDocument) {
                $filesToDelete[] = $oldDocument;
            }
        }

        if ($request->hasFile('bank_document')) {
            $oldDocument = $profile->bank_document;
            $profile->bank_document = $this->uploadImage($request, 'bank_document', 'uploads/profiles/bank');
            if ($oldDocument) {
                $filesToDelete[] = $oldDocument;
            }
        }

        // Update other fields
        $profile->professional_id = $professionalId;
        $profile->name = $data['name'];
        $profile->email = $data['email'];
        $profile->phone = $data['phone'] ?? null;
        $profile->specialization = $data['specialization'] ?? null;
        $profile->experience = $data['experience'] ?? null;
        $profile->starting_price = $data['startingPrice'] ?? null;
        $profile->address = $data['address'] ?? null;
        $profile->education = $data['education'] ?? null;
        $profile->comments = $data['comments'] ?? null;
        $profile->bio = $data['bio'] ?? null;
        $profile->gst_number = $data['gst_number'] ?? null;
        $profile->gst_address = $data['gst_address'] ?? null;
        
        // Update bank account details
        $profile->account_holder_name = $data['account_holder_name'] ?? null;
        $profile->bank_name = $data['bank_name'] ?? null;
        $profile->account_number = $data['account_number'] ?? null;
        $profile->ifsc_code = $data['ifsc_code'] ?? null;
        $profile->account_type = $data['account_type'] ?? null;
        $profile->bank_branch = $data['bank_branch'] ?? null;
        
        $profile->save();

        // Update Professional name also
        $professional = Professional::findOrFail($professionalId);
        $professional->name = $data['name'];
        $professional->email = $data['email']; // Also update email in professionals table
        $professionalSaved = $professional->save();

        // Log for debugging
        Log::info('Professional Profile Update:', [
            'profile_saved' => $profile->wasRecentlyCreated || $profile->wasChanged(),
            'professional_saved' => $professionalSaved,
            'professional_name' => $professional->name,
            'profile_name' => $profile->name,
            'professional_id' => $professionalId
        ]);

        foreach (array_unique($filesToDelete) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            } elseif (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
