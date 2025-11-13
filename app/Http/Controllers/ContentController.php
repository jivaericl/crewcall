<?php

namespace App\Http\Controllers;

use App\Models\ContentFile;
use App\Models\ContentFileVersion;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Download text-based content (plain_text, rich_text)
     */
    public function download($contentId)
    {
        $content = ContentFile::findOrFail($contentId);
        
        // Generate filename based on content type
        $extension = $content->file_type === 'rich_text' ? 'html' : 'txt';
        $filename = str_replace(' ', '_', $content->name) . '.' . $extension;
        
        // Get content from metadata
        $fileContent = $content->metadata['content'] ?? '';
        
        // For rich text, wrap in basic HTML
        if ($content->file_type === 'rich_text') {
            $fileContent = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='UTF-8'>\n<title>{$content->name}</title>\n</head>\n<body>\n{$fileContent}\n</body>\n</html>";
        }
        
        return response()->streamDownload(function() use ($fileContent) {
            echo $fileContent;
        }, $filename, [
            'Content-Type' => $content->file_type === 'rich_text' ? 'text/html' : 'text/plain',
        ]);
    }
    
    /**
     * Download a specific version of text-based content
     */
    public function downloadVersion($versionId)
    {
        $version = ContentFileVersion::with('contentFile')->findOrFail($versionId);
        
        // If version has text content in metadata
        if (isset($version->metadata['content'])) {
            $extension = $version->mime_type === 'text/html' ? 'html' : 'txt';
            $filename = str_replace(' ', '_', $version->contentFile->name) . '_v' . $version->version_number . '.' . $extension;
            
            $fileContent = $version->metadata['content'];
            
            // For rich text, wrap in basic HTML
            if ($version->mime_type === 'text/html') {
                $fileContent = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='UTF-8'>\n<title>{$version->contentFile->name} - Version {$version->version_number}</title>\n</head>\n<body>\n{$fileContent}\n</body>\n</html>";
            }
            
            return response()->streamDownload(function() use ($fileContent) {
                echo $fileContent;
            }, $filename, [
                'Content-Type' => $version->mime_type,
            ]);
        }
        
        // For file-based versions, redirect to storage
        return redirect(\Storage::url($version->file_path));
    }
}
