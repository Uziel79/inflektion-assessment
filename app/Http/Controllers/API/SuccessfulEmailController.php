<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\SuccessfulEmailRepositoryInterface;
use App\Http\Resources\SuccessfulEmailResource;
use Illuminate\Http\Request;
use App\Models\SuccessfulEmail;
use Html2Text\Html2Text;
use App\Services\EmailParsingService;
use Illuminate\Support\Facades\Log;

class SuccessfulEmailController extends Controller
{
    private $successfulEmailRepository;
    private $emailParsingService;

    public function __construct(SuccessfulEmailRepositoryInterface $successfulEmailRepository, EmailParsingService $emailParsingService)
    {
        $this->successfulEmailRepository = $successfulEmailRepository;
        $this->emailParsingService = $emailParsingService;
    }

    public function index()
    {
        return SuccessfulEmailResource::collection($this->successfulEmailRepository->getAllPaginated());
    }

    public function store(StoreSuccessfulEmailRequest $request)
    {
        $validatedData = $request->validated();

        $plainText = $this->emailParsingService->extractPlainText($validatedData['email']);
        $plainText = $this->emailParsingService->sanitizePlainText($plainText);

        $validatedData['raw_text'] = $plainText;

        try {
            $successfulEmail = $this->successfulEmailRepository->create($validatedData);

            Log::info('Successful email created', ['id' => $successfulEmail->id]);

            return response()->json([
                'message' => 'Successful email created',
                'data' => $successfulEmail
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create successful email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(SuccessfulEmail $successfulEmail)
    {
        return new SuccessfulEmailResource($successfulEmail);
    }

    public function update(Request $request, SuccessfulEmail $successfulEmail)
    {
        $validatedData = $request->validate([
            'affiliate_id' => 'integer',
            'envelope' => 'string',
            'from' => 'string',
            'subject' => 'string',
            'dkim' => 'nullable|string',
            'SPF' => 'nullable|string',
            'spam_score' => 'nullable|numeric',
            'email' => 'string',
            'sender_ip' => 'nullable|string',
            'to' => 'string',
            'timestamp' => 'integer',
        ]);

        if (isset($validatedData['email'])) {
            $plainText = $this->emailParsingService->extractPlainText($validatedData['email']);
            $plainText = $this->emailParsingService->sanitizePlainText($plainText);
            $validatedData['raw_text'] = $plainText;
        }

        $this->successfulEmailRepository->update($successfulEmail, $validatedData);

        Log::info('Successful email updated', ['id' => $successfulEmail->id]);

        return $successfulEmail;
    }

    public function destroy(SuccessfulEmail $successfulEmail)
    {
        try {
            $this->successfulEmailRepository->delete($successfulEmail);

            Log::info('Successful email deleted', ['id' => $successfulEmail->id]);

            return response()->json(['message' => 'Email deleted successfully'], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete email', 'error' => $e->getMessage()], 500);
        }
    }
}
