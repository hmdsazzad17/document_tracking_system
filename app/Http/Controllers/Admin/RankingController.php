<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentDiff;
use App\Models\DocumentUser;
use App\Models\DocumentVersion;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;
class RankingController extends Controller
{
    public function list() {
        $pageTitle    = 'Document List';
        $data = Document::get();

        return view('admin.user_ranking.list', compact('pageTitle', 'data'));
    }

    public function docList() {
        $pageTitle    = 'Document List';
        $data = Document::get();

        return view('admin.user_ranking.DocumentList', compact('pageTitle', 'data'));
    }

    public function docListDetails($id = null) {
        $userId = 2;
        $pageTitle    = 'Document List';
        $data = Document::where('id',$id)->first();
        $getDocumentDetails =DocumentVersion::where("document_id",$data->id)->where("version",$data->current_version)->first();
        $docbody =json_decode($getDocumentDetails->body_content);
        $doctags =json_decode($getDocumentDetails->tags_content);
        $response['title'] = $data->title;
        $response['introduction'] = $docbody->introduction;
        $response['facts'] = $docbody->facts;
        $response['summary'] = $docbody->summary;
        $response['tags'] = $doctags;
        $previousView = DocumentUser::where("user_id",$userId)->where("document_id",$data->id)->orderBy("id",'desc')->first();
        $this->GetDifference($userId);
        $docummentDifference =DocumentDiff::where("document_user_id",$userId)->orderBy("id",'desc')->first();
        if(isset($docummentDifference->body_diff)){
            $renderContent = $docummentDifference->body_diff ;
            $rendererOptions = [
                'detailLevel' => 'line',
                'language' => 'eng',
                'lineNumbers' => true,
                'separateBlock' => true,
                'showHeader' => true,
                'spacesToNbsp' => false,
                'tabSize' => 4,
                'mergeThreshold' => 0.8,
                'cliColorization' => RendererConstant::CLI_COLOR_AUTO,
                'outputTagAsString' => false,
                'jsonEncodeFlags' => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
                'wordGlues' => [' ', '-'],
                'resultForIdenticals' => null,
                'wrapperClasses' => ['diff-wrapper'],
            ];
            $htmlRenderer = RendererFactory::make('Inline', $rendererOptions);
            $response['difference'] = $htmlRenderer->renderArray(json_decode($renderContent, true));
        }else{
            $response['difference'] = '';
        }

        return view('admin.user_ranking.documentDetails', compact('pageTitle', 'response'));
    }

    public function GetDifference($userId = null)
    {
        // Get active users who are clients

            $documentUsers = DocumentUser::where('user_id', $userId)->get();

            foreach ($documentUsers as $documentUser) {
                $document = $documentUser->document;

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
                    'document_user_id' => $documentUser->user_id,
                    'version' => $latestVersion->version,
                ])->first();

                if ($existingDiff) {
                    continue;
                }

                //leastest body conent
                $latestVersion->body_content = json_decode($latestVersion->body_content);

                $lastesHtmlContent = $latestVersion->body_content->introduction.$latestVersion->body_content->facts.$latestVersion->body_content->summary;

                // Calculate diff logic for body_content and tags_content
                $bodyDiff = $this->calculateDiff($documentUser->last_viewed_version, $lastesHtmlContent,$documentUser->document_id);
                $tagsDiff = $this->calculateDiff($documentUser->last_viewed_version, $lastesHtmlContent,$documentUser->document_id);

                // Store the diffs in the database
                DocumentDiff::create([
                    'document_user_id' => $documentUser->user_id,
                    'version' => $latestVersion->version,
                    'body_diff' => $bodyDiff,
                    'tags_diff' => $tagsDiff,
                ]);



                // Update last viewed version for the user
                $documentUser->update(['last_viewed_version' => $document->current_version]);
            }

            return true;

    }

    public function calculateDiff($fromVersion, $toContent,$document_id)
    {
        // Fetch the content from the previous version (if available)
        $previousContent = $this->getContentFromVersion($document_id, $fromVersion);

        // Initialize the DiffHelper
        // $diffHelper = new DiffHelper();

        // Calculate the diff
        $diffOptions = [
            'context' => 3, // Number of lines of context around the changes
        ];
        // $diff = $diffHelper->getDiff(
        //     new HtmlString($previousContent),
        //     new HtmlString($toContent),
        //     $diffOptions
        // );
        $old = json_encode(["content" => "this is previous"]);
        $new = json_encode(["content" => "this is new"]);
        $diff = DiffHelper::calculate( $previousContent, $toContent,'Json');
        // $diff = $diffHelper->getDiff(
        //     new HtmlString($previousContent),
        //     new HtmlString($content),
        //     $diffOptions
        // );
        return $diff;
    }

    public function getContentFromVersion($documentId, $version)
    {
        $version = DocumentVersion::where('document_id', $documentId)
            ->where('version', $version)
            ->first();
        $version->body_content = json_decode($version->body_content);
        if ($version) {
            return $version->body_content->introduction . // Fetch content sections as needed
                $version->body_content->facts .
                $version->body_content->summary;
        }

        return 'no changes';
    }



    public function store(Request $request, $id = 0)
    {
        $validateRule = $id ? 'nullable' : 'required';
        $request->validate([
            'level'               => 'required|integer:gt:0',
            'name'                => 'required',
            'minimum_invest'      => 'required|numeric|gt:0',
            'team_minimum_invest' => 'required|numeric|gt:0',
            'min_referral'        => 'required|integer|gt:0',
            'bonus'               => 'required|numeric|min:0',
            'icon'                => [$validateRule, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $userRanking = UserRanking::findOrFail($id);
            $notify[]    = ['success', 'User ranking updated successfully'];
        } else {
            $userRanking = new UserRanking();
            $notify[]    = ['success', 'User ranking added successfully'];
        }

        if ($request->hasFile('icon')) {
            try {
                $userRanking->icon = fileUploader($request->icon, getFilePath('userRanking'), getFileSize('userRanking'), $userRanking->icon);
            } catch (\Exception$exp) {
                $notify[] = ['error', 'Couldn\'t upload your icon'];
                return back()->withNotify($notify);
            }
        }

        $userRanking->level               = $request->level;
        $userRanking->name                = $request->name;
        $userRanking->minimum_invest      = $request->minimum_invest;
        $userRanking->min_referral_invest = $request->team_minimum_invest;
        $userRanking->min_referral        = $request->min_referral;
        $userRanking->bonus               = $request->bonus;
        $userRanking->save();

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return UserRanking::changeStatus($id);
    }

}
