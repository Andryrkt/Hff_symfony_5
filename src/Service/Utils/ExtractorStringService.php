<?php

namespace App\Service\Utils;

class ExtractorStringService
{
    const SEPARATOR_DEFAULT = ' - ';
    const SEPARATOR_SPACE = ' ';
    const SEPARATOR_HYPHEN = '-';
    
    /**
     * Extrait la partie code d'une chaîne (avant le séparateur).
     *
     * @param string|null $chaine La chaîne d'entrée.
     * @param string $separator Le séparateur à utiliser.
     * @return string|null Le code extrait ou null si aucun code trouvé/entrée est null.
     */
    public function extraireCode(?string $chaine, string $separator = self::SEPARATOR_DEFAULT): ?string
    {
        if ($chaine === null) {
            return null;
        }

        $chaine = trim($chaine);
        
        if (strpos($chaine, $separator) !== false) {
            $parts = explode($separator, $chaine, 2);
            return trim($parts[0]);
        }
        
        // Fallback avec regex pour les séparateurs simples
        $pattern = $this->getSeparatorPattern($separator);
        if (preg_match($pattern, $chaine, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }
    
    /**
     * Extrait la partie description d'une chaîne (après le séparateur).
     *
     * @param string|null $chaine La chaîne d'entrée.
     * @param string $separator Le séparateur à utiliser.
     * @return string|null La description extraite ou null si aucune description trouvée/entrée est null.
     */
    public function extraireDescription(?string $chaine, string $separator = self::SEPARATOR_DEFAULT): ?string
    {
        if ($chaine === null) {
            return null;
        }

        $chaine = trim($chaine);
        
        if (strpos($chaine, $separator) !== false) {
            $parts = explode($separator, $chaine, 2);
            return isset($parts[1]) ? trim($parts[1]) : null;
        }
        
        return null;
    }
    
    /**
     * Extrait les deux parties (code et description) d'une chaîne.
     *
     * @param string|null $chaine La chaîne d'entrée.
     * @param string $separator Le séparateur à utiliser.
     * @return array|null Tableau avec 'code' et 'description' ou null si aucune partie trouvée.
     */
    public function extraireLesDeuxParties(?string $chaine, string $separator = self::SEPARATOR_DEFAULT): ?array
    {
        if ($chaine === null) {
            return null;
        }

        $chaine = trim($chaine);
        
        if (strpos($chaine, $separator) !== false) {
            $parts = explode($separator, $chaine, 2);
            return [
                'code' => trim($parts[0]),
                'description' => isset($parts[1]) ? trim($parts[1]) : null
            ];
        }
        
        return null;
    }
    
    /**
     * Méthode utilitaire pour obtenir le pattern regex selon le séparateur.
     */
    private function getSeparatorPattern(string $separator): string
    {
        switch ($separator) {
            case self::SEPARATOR_SPACE:
                return '/^([^\s]+)/';
            case self::SEPARATOR_HYPHEN:
                return '/^([^-]+)/';
            case self::SEPARATOR_DEFAULT:
            default:
                return '/^([^\s-]+)/';
        }
    }
    
    /**
     * Méthode pour tester automatiquement avec différents séparateurs.
     *
     * @param string|null $chaine La chaîne d'entrée.
     * @return array|null Tableau avec 'code' et 'description' ou null si aucun séparateur ne correspond.
     */
    public function extraireAvecSeparateursMultiples(?string $chaine): ?array
    {
        $separateurs = [
            self::SEPARATOR_DEFAULT,
            self::SEPARATOR_HYPHEN,
            self::SEPARATOR_SPACE
        ];
        
        foreach ($separateurs as $separator) {
            $resultat = $this->extraireLesDeuxParties($chaine, $separator);
            if ($resultat !== null && $resultat['code'] !== '') {
                return $resultat;
            }
        }
        
        return null;
    }
}
