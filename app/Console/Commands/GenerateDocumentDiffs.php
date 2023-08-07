<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Document;
use App\Models\DocumentUser;
use App\Models\DocumentVersion;
use App\Models\DocumentDiff;
use Illuminate\Support\Facades\DB;
use Jfcherng\Diff\DiffHelper;
use Illuminate\Support\HtmlString;

class GenerateDocumentDiffs extends Command
{
    protected $signature = 'documents:generate-diffs';
    protected $description = 'Generate and store document diffs for clients';

    public function handle()
    {
        // Get active users who are clients
        $activeClients = User::where('status', 'active')->get();

        foreach ($activeClients as $client) {
            $documentUsers = DocumentUser::where('user_id', $client->id)->get();

            foreach ($documentUsers as $documentUser) {
                $document = $documentUser->document;
                $this->info('test.'.$document);
                if ($document->status === 'inactive' || $document->current_version <= $documentUser->last_viewed_version) {
                    continue;
                }

                // Get the latest document version
                $latestVersion = DocumentVersion::where('document_id', $document->id)
                    ->orderBy('version', 'desc')
                    ->first();

                if (!$latestVersion) {
                    continue;
                }

                // Check if a diff already exists for this user and latest version
                $existingDiff = DocumentDiff::where([
                    'document_user_id' => $documentUser->id,
                    'version' => $latestVersion->version,
                ])->first();

                if ($existingDiff) {
                    continue;
                }

                // Calculate diff logic for body_content and tags_content
                $bodyDiff = $this->calculateDiff($documentUser->last_viewed_version, json_decode($latestVersion->body_content),$documentUser->document_id);
                $tagsDiff = $this->calculateDiff($documentUser->last_viewed_version, json_decode($latestVersion->tags_content),$documentUser->document_id);

                // Store the diffs in the database
                DocumentDiff::create([
                    'document_user_id' => $documentUser->id,
                    'version' => $latestVersion->version,
                    'body_diff' => json_encode($bodyDiff),
                    'tags_diff' => json_encode($tagsDiff),
                ]);



                // Update last viewed version for the user
                $documentUser->update(['last_viewed_version' => $document->current_version]);
            }


        }

        $this->info('Document diffs generated and stored.');
    }

    protected function calculateDiff($fromVersion, $toContent,$document_id)
    {
        // Fetch the content from the previous version (if available)
        $previousContent = $this->getContentFromVersion($document_id, $fromVersion);

        // Initialize the DiffHelper
        // $diffHelper = new Jfcherng\Diff\DiffHelper;

        // Calculate the diff
        $diffOptions = [
            'context' => 3, // Number of lines of context around the changes
        ];
        // $diff = $diffHelper->getDiff(
        //     new HtmlString($previousContent),
        //     new HtmlString($toContent),
        //     $diffOptions
        // );
        $diff = DiffHelper::calculate(new HtmlString($previousContent), new HtmlString($toContent),$diffOptions);

        return $diff;
    }

    protected function getContentFromVersion($documentId, $version)
    {
        $version = DocumentVersion::where('document_id', $documentId)
            ->where('version', $version)
            ->first();

        if ($version) {
            return $version->body_content['introduction'] . // Fetch content sections as needed
                $version->body_content['facts'] .
                $version->body_content['summary'];
        }

        return 'no changes';
    }
}