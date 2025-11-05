<?php

namespace App\Service;

class CodeExtractorService
{
    /**
     * Extracts the code part from a string, typically before ' - '.
     *
     * @param string|null $chaine The input string.
     * @return string|null The extracted code or null if no code found/input is null.
     */
    public function extraireCode(?string $chaine): ?string
    {
        if ($chaine === null) {
            return null;
        }

        // Remove superfluous spaces
        $chaine = trim($chaine);
        
        // Method with explode
        if (strpos($chaine, ' - ') !== false) {
            $parts = explode(' - ', $chaine);
            return $parts[0];
        }
        
        // Alternative method with regex
        if (preg_match('/^([^\s-]+)/', $chaine, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
