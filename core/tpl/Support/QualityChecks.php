<?php
/**
 * Copyright (c) 2023.  Broos Action
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * broosaction.com
 * QualityChecks.php created  09-12-2023  11:20
 */


namespace Core\tpl\Support;

use DOMDocument;

class QualityChecks
{

    public static function assessHtmlQuality($html)
    {
        // Initialize the quality score
        $qualityScore = 0;

        // Check for well-formedness (basic check for opening and closing tags)
        if (preg_match_all('/<[^\/]*>|<\/[^>]*>/', $html, $matches)) {
            $tagCount = count($matches[0]);
            $qualityScore += $tagCount + 2; // Give 2 points for each tag found
        }

        // Check for the presence of a DOCTYPE declaration
        if (stripos($html, '<!DOCTYPE') !== false) {
            $qualityScore += 5; // Give 5 points for having a DOCTYPE declaration
        }

        // Check for the use of HTML5 semantic elements
        $html5Semantics = ['header', 'nav', 'main', 'article', 'section', 'aside', 'footer'];
        foreach ($html5Semantics as $element) {
            if (stripos($html, "<$element") !== false) {
                $qualityScore += 3; // Give 3 points for each HTML5 semantic element used
            }
        }

        // Check for the use of meta charset declaration
        if (stripos($html, '<meta charset=') !== false) {
            $qualityScore += 2; // Give 2 points for meta charset declaration
        }

        // Check for valid HTML5 structure (head, title, and body)
        if (preg_match('/<head>.*<title>.*<\/title>.*<\/head>.*<body>/', $html)) {
            $qualityScore += 5; // Give 5 points for valid HTML5 structure
        }

        // Check for HTML5 form elements
        if (stripos($html, '<form') !== false) {
            $qualityScore += 3; // Give 3 points for the presence of a form element
        }

        // You can add more checks based on your specific HTML5 criteria

        return $qualityScore;
    }


    public static function countMediaElements($html)
    {
        // Initialize counters
        $photoCount = 0;
        $videoCount = 0;
        $graphicCount = 0;

        // Create a DOMDocument instance
        $dom = new DOMDocument();
        @$dom->loadHTML($html); // Load the HTML, suppress warnings

        // Count image elements
        $imgElements = $dom->getElementsByTagName('img');
        $photoCount = $imgElements->length;

        // Count image elements
        $imgElements = $dom->getElementsByTagName('svg');
        $photoCount += $imgElements->length;

        // Count video elements (you can customize the criteria)
        $videoElements = $dom->getElementsByTagName('video');
        foreach ($videoElements as $video) {
            $videoCount++;
        }

        // Count graphics elements (customize the criteria, e.g., using CSS classes)
        $graphicsElements = $dom->getElementsByTagName('div'); // Example: Counting <div> elements as graphics
        foreach ($graphicsElements as $graphics) {
            $graphicCount++;
        }

        // Return the counts as an associative array
        return [
            'photos' => $photoCount,
            'videos' => $videoCount,
            'graphics' => $graphicCount,
        ];
    }


    public static function calculateSeoScore($html)
    {
        // Initialize the SEO score
        $seoScore = 0;

        // Create a DOMDocument instance
        $dom = new DOMDocument();
        @$dom->loadHTML($html); // Load the HTML, suppress warnings

        // Check for the presence of a title tag
        $titleElements = $dom->getElementsByTagName('title');
        if ($titleElements->length > 0) {
            $seoScore += 10; // Give 10 points for having a title tag
        }

        // Check for meta description tag
        $metaDescriptionElements = $dom->getElementsByTagName('meta');
        $metaDescriptionFound = false;
        foreach ($metaDescriptionElements as $meta) {
            if ($meta->getAttribute('name') == 'description') {
                $metaDescriptionFound = true;
                break;
            }
        }
        if ($metaDescriptionFound) {
            $seoScore += 10; // Give 10 points for having a meta description tag
        }

        // Count the number of H1 headings
        $h1Elements = $dom->getElementsByTagName('h1');
        $h1Count = $h1Elements->length;
        $seoScore += $h1Count * 5; // Give 5 points for each H1 heading

        // Check for mobile-friendliness (simplified check, you may need more advanced methods)
        $isMobileFriendly = strpos($html, 'viewport') !== false;
        if ($isMobileFriendly) {
            $seoScore += 10; // Give 10 points for mobile-friendliness
        }

        // You can add more SEO checks based on your specific criteria

        return $seoScore;
    }


}