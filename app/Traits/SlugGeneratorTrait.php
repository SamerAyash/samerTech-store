<?php

namespace App\Traits;

use Illuminate\Support\Str;
trait SlugGeneratorTrait{
    
    protected static function generateUniqueSlug($locale, $title, $modelId = null)
    {
        $baseSlug = $locale == 'ar' ? static::arabic_slug($title) : Str::slug($title);
        $slug = $baseSlug;
        $count = 1;
        while (static::whereHas('translations', function ($q) use ($locale, $slug) {
            $q->where('locale', $locale)
            ->where('slug', $slug);
        })
        ->when($modelId, fn($q) => $q->where('id', '!=', $modelId))->exists()) {
            $slug = ($locale == 'ar' ? parent::arabic_slug($title) : Str::slug($title)) . '-' . $count;
            $count++;
        }
        return $slug;
    }

    /**
     * Generate Arabic slug from string
     */
    protected static function arabic_slug($string = null, $separator = "-") 
    {
        if (is_null($string)) {
            return "";
        }
        // Remove spaces from the beginning and from the end of the string
        $string = trim($string);
        // Lower case everything
        // using mb_strtolower() function is important for non-Latin UTF-8 string | more info: http://goo.gl/QL2tzK
        $string = mb_strtolower($string, "UTF-8");
        // Make alphanumeric (removes all other characters)
        // this makes the string safe especially when used as a part of a URL
        // this keeps latin characters and arabic charactrs as well
        $string = preg_replace("/[^a-z0-9_\s\-\x{0600}-\x{06FF}]/u", "", $string);
        // Remove multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Convert whitespaces and underscore to the given separator
        $string = preg_replace("/[\s_]/", $separator, $string);
        return $string;
    }
}
