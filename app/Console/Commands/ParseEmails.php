<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuccessfulEmail;
use App\Services\EmailParsingService;
use Html2Text\Html2Text;
use Illuminate\Support\Facades\Log;

class ParseEmails extends Command
{
    protected $signature = 'emails:parse';
    protected $description = 'Parse raw email content to plain text';

    private $emailParsingService;

    public function __construct(EmailParsingService $emailParsingService)
    {
        parent::__construct();
        $this->emailParsingService = $emailParsingService;
    }

    public function handle()
    {
        SuccessfulEmail::whereNull(SuccessfulEmail::RAW_TEXT_COLUMN)
            ->orWhere(SuccessfulEmail::RAW_TEXT_COLUMN, '')
            ->chunk(SuccessfulEmail::CHUNK_SIZE, function ($emails) {
                foreach ($emails as $email) {
                    $plainText = $this->emailParsingService->extractPlainText($email->{SuccessfulEmail::EMAIL_COLUMN});
                    $plainText = $this->emailParsingService->sanitizePlainText($plainText);
                    $email->{SuccessfulEmail::RAW_TEXT_COLUMN} = $plainText;
                    $email->save();
                }
            });

        Log::info('Emails parsed successfully.');

        $this->info('Emails parsed successfully.');
    }
}
